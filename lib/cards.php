<?php

function read_cards() {
    global $mysqli;
    
    $sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function reset_cards() {
    global $mysqli;

    $sql = "DELETE FROM cards_players
            WHERE (player = 'player_1' OR player = 'player_2')";
    $mysqli -> query($sql);

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
            
            $sql = "INSERT INTO cards_players (x, y, player)
                    VALUES ('$key', '$random_2', 'player_1')";
            $mysqli -> query($sql);
        }
        else {
            $sql = "INSERT INTO cards_players (x, y, player)
                    VALUES ('$key', '$cards[$key]', 'player_1')";
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

            $sql = "INSERT INTO cards_players (x, y, player)
                    VALUES ('$key', '$random_2', 'player_2')";
            $mysqli -> query($sql);
        }
        else {
            $sql = "INSERT INTO cards_players (x, y, player)
                    VALUES ('$key', '$cards[$key]', 'player_2')";
            $mysqli -> query($sql);
        
            unset($cards[$key]);
        }
    }
}

function move_card($x, $token) {

    global $mysqli;
	
    $player = current_player($token);

    if($player == "player_1") {
        $sql = "SELECT x, y FROM cards_players
                WHERE player='player_2'";

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            $i=0;
            while($row = $result->fetch_assoc()) {
                if($i == $x) {
                    $sql = "INSERT INTO cards_players (x, y, player)
                            VALUES (?, ?, 'player_1')";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('ss', $row["x"], $row["y"]);
                    $st->execute();
                    
                    $sql1 = 'SELECT count(x) AS c, y, x FROM cards_players
                            WHERE player="player_2"
                            GROUP BY x';

                    $result1 = $mysqli->query($sql1);

                    if ($result1->num_rows > 0) {
                        // output data of each row
                        while($row1 = $result1->fetch_assoc()) {
                            $sql2 = "SELECT x, y FROM cards_players
                                    WHERE player='player_2' AND x=?";
                            
                            $st2 = $mysqli->prepare($sql2);
                            $st2->bind_param('s', $row1["x"]);
                            $st2->execute();
                            $res2 = $st2->get_result();

                            $j=0;
                            while($row2 = $res2->fetch_assoc() and $j < 1) {
                                $sql3 = "DELETE FROM cards_players
                                        WHERE player='player_2' AND x=? AND y=?";
                                
                                $st3 = $mysqli->prepare($sql3);
                                $st3->bind_param('ss', $row["x"], $row["y"]);
                                $st3->execute();
                                
                                $temp_x = $row["x"];

                                $j++;
                            }
                        }
                    }
                    break;
                }
                $i++;
            }
        }
    }
    else {
        $sql = "SELECT x, y FROM cards_players
        WHERE player='player_1'";

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            $i=0;
            while($row = $result->fetch_assoc()) {
                if($i == $x) {
                    $sql = "INSERT INTO cards_players (x, y, player)
                            VALUES (?, ?, 'player_2')";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('ss',$row["x"], $row["y"]);
                    $st->execute();
                    
                    $sql1 = 'SELECT count(x) AS c, y, x FROM cards_players
                            WHERE player="player_1"
                            GROUP BY x';

                    $result1 = $mysqli->query($sql1);

                    if ($result1->num_rows > 0) {
                        // output data of each row
                        while($row1 = $result1->fetch_assoc()) {
                            $sql2 = "SELECT x, y FROM cards_players
                                    WHERE player='player_1' AND x=?";
                            
                            $st2 = $mysqli->prepare($sql2);
                            $st2->bind_param('s', $row1["x"]);
                            $st2->execute();
                            $res2 = $st2->get_result();

                            $j=0;
                            while($row2 = $res2->fetch_assoc() and $j < 1) {
                                $sql3 = "DELETE FROM cards_players
                                        WHERE player='player_1' AND x=? AND y=?";
                                
                                $st3 = $mysqli->prepare($sql3);
                                $st3->bind_param('ss', $row["x"], $row["y"]);
                                $st3->execute();
                                
                                $temp_x = $row["x"];

                                $j++;
                            }
                        }
                    }
                    break;
                }
                $i++;
            }
        }
    }

    if($temp_x != 'K') {
        remove_card($player, $row["x"], $row["y"]);
    }

    $sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function check_for_win($token) {
    global $mysqli;

    $player = current_player($token);

    if($player == 'player_1') {
        $sql = 'SELECT count(x) as c, x FROM cards_players
                WHERE player="player_2"';

        $st = $mysqli->prepare($sql);
        $st->execute();
        $res = $st->get_result();
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                if($row['c'] == 1 and $row['x'] == 'K') {
                    $sql = 'UPDATE game_status SET result="player_1"';

                    $st = $mysqli->prepare($sql);
                    $st->execute();

                    exit;
                }
            }
        }
        else {
            $sql = 'UPDATE game_status SET result="player_2"';

            $st = $mysqli->prepare($sql);
            $st->execute();
            
            exit;
        }
    }
    else if($player == 'player_2') {
        $sql = 'SELECT count(x) as c, x FROM cards_players
                WHERE player="player_1"';

        $st = $mysqli->prepare($sql);
        $st->execute();
        $res = $st->get_result();
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                if($row['c'] == 1 and $row['x'] == 'K') {
                    $sql = 'UPDATE game_status SET result="player_2"';

                    $st = $mysqli->prepare($sql);
                    $st->execute();
                    
                    exit;
                }
            }
        }
        else {
            $sql = 'UPDATE game_status SET result="player_1"';

            $st = $mysqli->prepare($sql);
            $st->execute();
            
            exit;
        }
    }

    if($player == "player_1") {
        if(check_remove_cards('player_2') == 0) {
            $sql = "DELETE FROM cards_players
                    WHERE player='player_2'";
            
            $st = $mysqli->prepare($sql);
            $st->execute();

            $sql = 'UPDATE game_status SET result="player_2"';

            $st = $mysqli->prepare($sql);
            $st->execute();
            
            exit;
        }
    }
    else {
        if(check_remove_cards('player_1') == 0) {
            $sql = "DELETE FROM cards_players
                    WHERE player='player_1'";
            
            $st = $mysqli->prepare($sql);
            $st->execute();

            $sql = 'UPDATE game_status SET result="player_1"';

            $st = $mysqli->prepare($sql);
            $st->execute();
            
            exit;
        }
    }
}

function remove_cards($player) {
	global $mysqli;

	if($player == 'player_1') {
		$sql = 'SELECT count(x) AS c, y, x FROM cards_players
                WHERE player="player_1"
                GROUP BY x';

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM cards_players
                            WHERE player='player_1' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $row["x"]);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM cards_players
                                WHERE player='player_1' AND x=? AND y=?";
                        
                        $st_ = $mysqli->prepare($sql);
                        $st_->bind_param('ss', $row_["x"], $row_["y"]);
                        $st_->execute();

                        $i++;
                    }
                }
            }
        }
	}
    else {
        $sql = 'SELECT count(x) AS c, y, x FROM cards_players
                WHERE player="player_2"
                GROUP BY x';

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM cards_players
                            WHERE player='player_2' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $row["x"]);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM cards_players
                                WHERE player='player_2' AND x=? AND y=?";
                        
                        $st_ = $mysqli->prepare($sql);
                        $st_->bind_param('ss', $row_["x"], $row_["y"]);
                        $st_->execute();

                        $i++;
                    }
                }
            }
        }
    }

    $sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function remove_card($player, $x, $y) {
    global $mysqli;

	if($player == 'player_1') {
		$sql = 'SELECT count(x) AS c FROM cards_players
                WHERE player="player_1" AND x=?
                GROUP BY x';

        $st = $mysqli->prepare($sql);
        $st->bind_param('s', $x);
        $st->execute();
        $result = $st->get_result();

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM cards_players
                            WHERE player='player_1' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $x);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM cards_players
                                WHERE player='player_1' AND x=? AND y=?";
                        
                        $st_ = $mysqli->prepare($sql);
                        $st_->bind_param('ss', $row_["x"], $row_["y"]);
                        $st_->execute();

                        $i++;
                    }
                }
            }
        }
	}
    else {
        $sql = 'SELECT count(x) AS c FROM cards_players
                WHERE player="player_2" AND x=?
                GROUP BY x';

        $st = $mysqli->prepare($sql);
        $st->bind_param('s', $x);
        $st->execute();
        $result = $st->get_result();

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM cards_players
                            WHERE player='player_2' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $x);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM cards_players
                                WHERE player='player_2' AND x=? AND y=?";
                        
                        $st_ = $mysqli->prepare($sql);
                        $st_->bind_param('ss', $row_["x"], $row_["y"]);
                        $st_->execute();

                        $i++;
                    }
                }
            }
        }
    }
}

function check_remove_cards($player) {
    global $mysqli;

    $sql = "CREATE TABLE copy_cards_players AS 
            SELECT * FROM cards_players";

    $st = $mysqli -> prepare($sql);
    $st -> execute();

	if($player == 'player_1') {
		$sql = 'SELECT count(x) AS c, y, x FROM copy_cards_players
                WHERE player="player_1"
                GROUP BY x';

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM copy_cards_players
                            WHERE player='player_1' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $row["x"]);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM copy_cards_players
                                WHERE player='player_1' AND x=? AND y=?";
                        
                        $st_ = $mysqli->prepare($sql);
                        $st_->bind_param('ss', $row_["x"], $row_["y"]);
                        $st_->execute();

                        $i++;
                    }
                }
            }
        }
	}
    else {
        $sql = 'SELECT count(x) AS c, y, x FROM copy_cards_players
                WHERE player="player_2"
                GROUP BY x';

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM copy_cards_players
                            WHERE player='player_2' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $row["x"]);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM copy_cards_players
                                WHERE player='player_2' AND x=? AND y=?";
                        
                        $st_ = $mysqli->prepare($sql);
                        $st_->bind_param('ss', $row_["x"], $row_["y"]);
                        $st_->execute();

                        $i++;
                    }
                }
            }
        }
    }

    $sql = 'SELECT COUNT(x) AS c FROM copy_cards_players
            WHERE player=?';

    $st_ = $mysqli->prepare($sql);
    $st_->bind_param('s', $player);
    $st_->execute();
    $result = $st_->get_result();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if($row["c"] == 0) {
                $sql = "DROP TABLE copy_cards_players";

                $st = $mysqli -> prepare($sql);
                $st -> execute();

                return 0;
            }
            else {
                $sql = "DROP TABLE copy_cards_players";

                $st = $mysqli -> prepare($sql);
                $st -> execute();
                
                return 1;
            }
        }
    }
}

?>