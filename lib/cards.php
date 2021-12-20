<?php

function show_cards() {
    global $mysqli;

    $sql = "SELECT x, y, player FROM cards_players";
    $st = $mysqli -> prepare($sql);

    $st -> execute();
    $res = $st -> get_result();

    header('Content-type: application/json');
    print json_encode($res -> fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function reset_cards() {
    global $mysqli;

    $cards = array("A" => array("H", "B", "T", "R"),
                   "2" => array("H", "B", "T", "R"),
                   "3" => array("H", "B", "T", "R"),
                   "4" => array("H", "B", "T", "R"),
                   "5" => array("H", "B", "T", "R"),
                   "6" => array("H", "B", "T", "R"),
                   "7" => array("H", "B", "T", "R"),
                   "8" => array("H", "B", "T", "R"),
                   "9" => array("H", "B", "T", "R"),
                   "10" => array("H", "B", "T", "R"),
                   "K" => "B"
    );

    for($i = 1; $i <= 20; $i++) {
        $key = array_rand($cards,1);
        $random = $cards[$key];

        if(is_array($random)){
            $random_2 = array_shift($cards[$key]);

            if(empty($cards[$key])){
                unset($cards[$key]);
            }
            
            $sql = "INSERT INTO cards_players (x, y, num, player)
                    VALUES ('$key', '$random_2', '$i', 'player_1')";
            $mysqli -> query($sql);
        }
        else {
            $sql = "INSERT INTO cards_players (x, y, num, player)
                    VALUES ('$key', '$cards[$key]', '$i', 'player_1')";
            $mysqli -> query($sql);
        
            unset($cards[$key]);
        }
    }

    for($i = 1; $i <= 21; $i++) {
        $key = array_rand($cards,1);
        $random = $cards[$key];

        if(is_array($random)){
            $random_2 = array_shift($cards[$key]);

            if(empty($cards[$key])){
                unset($cards[$key]);
            }

            $sql = "INSERT INTO cards_players (x, y, num, player)
                    VALUES ('$key', '$random_2', '$i', 'player_2')";
            $mysqli -> query($sql);
        }
        else {
            $sql = "INSERT INTO cards_players (x, y, num, player)
                    VALUES ('$key', '$cards[$key]', '$i', 'player_2')";
            $mysqli -> query($sql);
        
            unset($cards[$key]);
        }
    }


    show_cards();
}

?>