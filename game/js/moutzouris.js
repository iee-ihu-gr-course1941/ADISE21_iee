var me = {token:null, player:null}
var game_status = {}
var last_change = new Date().getTime()
var timer = null

$(function() {
    var input = document.getElementById("username")
    var text = document.getElementById("paragraph").innerHTML
    var result = text.trim()

    input.value = result
})

function card_sharing() {
    $.ajax({url: "./moutzouris.php/cards", method: "POST", headers: {'X-Token': me.token}, success: share_cards_by_data});
}

function share_cards_by_data(data) {
    for(i=0; i<data.length; i++){
        var o = data[i];
        if(o.player == me.player) {
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
    var player = $('#menu').val()

    me.player = player

    $.ajax({url: './moutzouris.php/players/' + player, 
                method: 'PUT',
                dataType: 'json',
                headers: {'X-Token': me.token},
                contentType: 'application/json',
                data: JSON.stringify({username: $('#username').val(), player: player}),
                success: login_result,
                error: login_error})
}

function login_result(data) {
	me = data[0]

    card_sharing()

    document.getElementById("game").style.display = "none"
    document.getElementById("btn-remove").style.display = "block"

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
	$.ajax({url: "moutzouris.php/status", success: update_status, headers: {"X-Token": me.token} });
}

function update_status(data) {
	last_change = new Date().getTime()

    var game_status = data[0]
    var game_stat_old = game_status

    update_info();
	clearTimeout(timer)
	if(game_status.p_turn == me.player &&  me.player != null) {
		x=0
		// do play
        $('#move_div').show(1000)
		timer = setTimeout(function() { game_status_update()}, 15000);
	}
    else {
        // must wait for something
        $('#move_div').hide(1000)
		timer = setTimeout(function() { game_status_update()}, 4000);
    }	
}

function do_remove() {
    var player = $('#menu').val()

    $.ajax({url: "./moutzouris.php/cards/remove", 
            method: 'POST',
            data: JSON.stringify({player: player}),
            success: move_result
        })
}

// function do_move() {
// 	var s = $('#the_move').val()
	
// 	var a = s.trim().split(/[ ]+/)

// 	if(a.length != 2) {
// 		alert('Must give 2 characters')
// 		return
// 	}

// 	$.ajax({url: "moutzouris.php/cards/card/" + a[0] + '/' + a[1], 
// 			method: 'PUT',
// 			dataType: "json",
// 			contentType: 'application/json',
// 			data: JSON.stringify( {x: a[0], y: a[1]}),
// 			headers: {"X-Token": me.token},
// 			success: move_result,
// 			error: login_error
//         })
// }

function move_result(data){
    document.getElementById("btn-remove").style.display = "none"
    document.getElementById("move_div").style.display = "block"

    clean_table()
    share_cards_by_data(data)
    game_status_update()
}

function clean_table() {
    document.getElementById("player_1").innerHTML = ""
    document.getElementById("player_2").innerHTML = ""
}