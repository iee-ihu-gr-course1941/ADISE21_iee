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
            document.getElementById("game_2").appendChild(img)

            document.getElementById("img_" + i).style.cursor = "pointer"
            document.getElementById("img_" + i).style.marginRight = "15px"
            document.getElementById("img_" + i).style.marginBottom = "10px"
        }
        else {
            var img = new Image()
            var card = "images/cards/S.png"
            img.src = card
            img.id = "img_" + i
            document.getElementById("game_2").appendChild(img)

            document.getElementById("img_" + i).style.cursor = "pointer"
            document.getElementById("img_" + i).style.marginRight = "15px"
            document.getElementById("img_" + i).style.marginBottom = "10px"
        }
    }
}