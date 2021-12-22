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
            document.getElementById("game").appendChild(img)
        }
        else {
            var img = new Image()
            var card = "images/cards/S.png"
            img.src = card
            document.getElementById("game_2").appendChild(img)
        }
    }
}