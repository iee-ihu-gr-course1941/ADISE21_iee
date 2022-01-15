<?php

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

	$sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function move_card($x, $token) {

    global $mysqli;
	
	if($token == null || $token == '') {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"token is not set."]);
		exit;
	}
	
	$player = current_player($token);
	if($player == null ) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}

	// $status = read_status();
	// if($status['status'] != 'started') {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"Game is not in action."]);
	// 	exit;
	// }

	// if($status['p_turn'] != $player) {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"It is not your turn."]);
	// 	exit;
	// }

    $sql = 'SELECT count(x) as c, x FROM cards_players
            WHERE player=?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s', $player);
	$st->execute();
	$res = $st->get_result();
    
    if($player == 'player_1') {
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                if($row['c'] == 1 and $row['x'] == 'K') {
                    print json_encode(['errormesg'=>'player_2' . "Win!"]);
                    exit;
                }
            }
        }
    }
    else {
        if($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                if($row['c'] == 1 and $row['x'] == 'K') {
                    print json_encode(['errormesg'=>'player_1' . "Win!"]);
                    exit;
                }
            }
        }
    }

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

    remove_card($player, $row["x"], $row["y"]);

    if(check_remove_cards($player) == 0) {
        $sql = "DELETE FROM cards_players
                WHERE player=?";
        
        $st = $mysqli->prepare($sql);
        $st->bind_param('s', $player);
        $st->execute();

        $sql = 'SELECT x, y, player FROM cards_players';

        $st = $mysqli->prepare($sql);
        $st->execute();
        $res = $st->get_result();

        header('Content-type: application/json');
        print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

        print json_encode(['errormesg'=>$player . "Win!"]);
	 	exit;
    }

    $sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function remove_cards($player) {
	global $mysqli;

    if($player == null ) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}

	// $status = read_status();
	// if($status['status'] != 'started') {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"Game is not in action."]);
	// 	exit;
	// }

	// if($status['p_turn'] != $player) {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"It is not your turn."]);
	// 	exit;
	// }

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

    if($player == null ) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}

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

    if($player == null ) {
		header("HTTP/1.1 400 Bad Request");
		print json_encode(['errormesg'=>"You are not a player of this game."]);
		exit;
	}

    $sql = "SELECT x, y, num, players 
            INTO #tmp_cards_players
            FROM cards_players";

    $st = $mysqli -> prepare($sql);
    $st -> execute();

	if($player == 'player_1') {
		$sql = 'SELECT count(x) AS c, y, x FROM #tmp_cards_players
                WHERE player="player_1"
                GROUP BY x';

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM #tmp_cards_players
                            WHERE player='player_1' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $row["x"]);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM #tmp_cards_players
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
        $sql = 'SELECT count(x) AS c, y, x FROM #tmp_cards_players
                WHERE player="player_2"
                GROUP BY x';

        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row["c"] > 1) {
                    $sql = "SELECT x, y FROM #tmp_cards_players
                            WHERE player='player_2' AND x=?";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('s', $row["x"]);
                    $st->execute();
                    $res = $st->get_result();

                    $i=0;
                    while($row_ = $res->fetch_assoc() and $i < 2) {
                        $sql = "DELETE FROM #tmp_cards_players
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

    $sql = 'SELECT COUNT(x) AS c FROM #tmp_cards_players
            WHERE player=?';

    $st_ = $mysqli->prepare($sql);
    $st_->bind_param('s', $player);
    $st_->execute();
    $result = $st_->get_result();

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if($row["c"] == 0) {
                return 0;
            }
            else {
                return 1;
            }
        }
    }
}

?>