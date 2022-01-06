var me = {};
var game_status = {}
var i = -1
var username_

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

            document.getElementById("img_" + i).style.cursor = "pointer"
            document.getElementById("img_" + i).style.marginRight = "15px"
            document.getElementById("img_" + i).style.marginBottom = "10px"
        }
        else {
            var img = new Image()
            var card = "images/cards/S.png"
            img.src = card
            img.id = "img_" + i
            document.getElementById("player_2").appendChild(img)

            document.getElementById("img_" + i).style.cursor = "pointer"
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

    //card_sharing()

    $.ajax({url: "./moutzouris.php/players/player_"+selected_option, 
        method: 'PUT',
        dataType: "json",
        headers: {"X-Token": me.token},
        contentType: 'application/json',
        data: JSON.stringify({username: $('#username').val(), player: selected_option}),
        success: login_result,
        error: login_error
    })
}

function login_result(data) {
	me = data[0]
    $("#game").hide()

	// update_info();
	// game_status_update();
}

function login_error(data,y,z,c) {
	var x = data.responseJSON
	//alert(x.errormesg)
}
