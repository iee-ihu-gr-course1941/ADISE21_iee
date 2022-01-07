var me = {token:null, player:null};
var game_status = {};
var last_change = new Date().getTime();
var timer = null;

function card_sharing() {
    $.ajax({url: "./moutzouris.php/cards", method: "POST", success: share_cards_by_data});
}

function share_cards_by_data(data) {
    for(i=0; i<data.length; i++){
        var o = data[i];
        if(o.player != 'player_2') {
            var img = new Image()
            var card = "images/cards/" + o.x + "_" + o.y + ".png"
            img.src = card
            img.id = "img_" + i
            document.getElementById("player_1").appendChild(img)

            document.getElementById("img_" + i).style.marginRight = "15px"
            document.getElementById("img_" + i).style.marginBottom = "10px"
        }
        else {
            var img = new Image()
            var card = "images/cards/S.png"
            img.src = card
            img.id = "img_" + i
            document.getElementById("player_2").appendChild(img)

            document.getElementById("img_" + i).style.marginRight = "15px"
            document.getElementById("img_" + i).style.marginBottom = "10px"
        }
    }
}

function login_to_game() {
    if($('#username').val()=='') {
		alert('You have to set a username');
		return;
	}

    var menu = document.getElementById("menu")
    var selected_option = menu.options[menu.selectedIndex].value

    $.ajax({url: './moutzouris.php/players/' + selected_option, 
                method: 'PUT',
                dataType: 'json',
                headers: {'X-Token': me.token},
                contentType: 'application/json',
                data: JSON.stringify({username: $('#username').val(), player: selected_option}),
                success: login_result,
                error: login_error})
}

function login_result(data) {
	me = data[0]
    card_sharing()
    document.getElementById("game").style.display = "none"
    document.getElementById("move_div").style.display = "block"

	update_info();
	game_status_update();
}

function login_error(data, y, z, c) {
	var x = data.responseJSON
	alert(x.errormesg)
}

function update_info() {
    $('#game_info').html("I am : " + me.player + ", my name is " + me.username + '<br>Token=' + me.token + '<br>Game state: ' + game_status.status + ', ' + game_status.p_turn + ' must play now.');
}

function game_status_update() {
	clearTimeout(timer);
	$.ajax({url: "./moutzouris.php/status/", success: update_status, headers: {"X-Token": me.token} });
}

function update_status(data) {
	last_change = new Date().getTime();
	var game_stat_old = game_status;
	game_status = data[0];
	update_info();
	clearTimeout(timer);
	if(game_status.p_turn == me.player &&  me.player != null) {
		x=0;
		// do play
		if(game_stat_old.p_turn != game_status.p_turn) {
			fill_board();
		}
		$('#move_div').show(1000);
		timer=setTimeout(function() { game_status_update();}, 15000);
	} else {
		// must wait for something
		$('#move_div').hide(1000);
		timer=setTimeout(function() { game_status_update();}, 4000);
	}
 	
}