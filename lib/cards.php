<?php

// function show_cards($input) {
// 	global $mysqli;
	
// 	$b = current_player($input['token']);
// 	if($b) {
// 		show_cards_by_player($b);
// 	} else {
// 		header('Content-type: application/json');
// 		print json_encode(read_cards(), JSON_PRETTY_PRINT);
// 	}
// }

// function read_cards() {
// 	global $mysqli;

// 	$sql = 'SELECT * FROM cards_players';

// 	$st = $mysqli->prepare($sql);
// 	$st->execute();
// 	$res = $st->get_result();

// 	return($res->fetch_all(MYSQLI_ASSOC));
// }

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

	$sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

// function show_cards_by_player($b) {
// 	global $mysqli;

// 	$orig_cards = read_cards_player($b);
//     $cards_p = convert_cards($orig_cards);
// 	$status = read_status();
// 	if($status['status']=='started' && $status['p_turn']==$b && $b!=null) {
// 		// It my turn !!!!
// 		$n = add_valid_moves_to_cards($cards_p, $b);
		
// 		// Εάν n==0, τότε έχασα !!!!!
// 		// Θα πρέπει να ενημερωθεί το game_status.
// 	}

// 	header('Content-type: application/json');
// 	print json_encode($orig_cards, JSON_PRETTY_PRINT);
// }

// function read_cards_player($player) {
//     global $mysqli;

// 	$sql = "SELECT * FROM cards
//             WHERE player=?";

//     $st->bind_param('s',$player);
//     $st->execute();
//     $res = $st->get_result();

// 	return($res->fetch_all(MYSQLI_ASSOC));
// }

// function convert_cards(&$orig_board) {
// 	$cards_p = [];

// 	foreach($orig_board as $i=>&$row) {
// 		$cards_p[] = &$row;
// 	} 

// 	return($cards_p);
// }

// function add_valid_moves_to_cards(&$cards, $b) {
// 	$number_of_moves = 0;
	
// 	for($x=1;$x<9;$x++) {
// 		for($y=1;$y<9;$y++) {
// 			$number_of_moves += add_valid_moves_to_card($board, $b, $x, $y);
// 		}
// 	}

// 	return($number_of_moves);
// }

// function add_valid_moves_to_card(&$board, $b, $x, $y) {
// 	$number_of_moves=0;
// 	if($board[$x][$y]['piece_color']==$b) {
// 		switch($board[$x][$y]['piece']){
// 			case 'P': $number_of_moves+=pawn_moves($board,$b,$x,$y);break;
// 			case 'K': $number_of_moves+=king_moves($board,$b,$x,$y);break;
// 			case 'Q': $number_of_moves+=queen_moves($board,$b,$x,$y);break;
// 			case 'R': $number_of_moves+=rook_moves($board,$b,$x,$y);break;
// 			case 'N': $number_of_moves+=knight_moves($board,$b,$x,$y);break;
// 			case 'B': $number_of_moves+=bishop_moves($board,$b,$x,$y);break;
// 		}
// 	} 

// 	return($number_of_moves);
// }

// function show_card($x, $y) {
// 	global $mysqli;
	
// 	$sql = 'SELECT * FROM cards_players
//             WHERE x=? AND y=?';

// 	$st = $mysqli->prepare($sql);
// 	$st->bind_param('ii',$x,$y);
// 	$st->execute();
// 	$res = $st->get_result();

// 	header('Content-type: application/json');
// 	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
// }

function move_card($x, $token) {

    global $mysqli;
	
	// if($token == null || $token == '') {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"token is not set."]);
	// 	exit;
	// }
	
	$player = current_player($token);
	// if($player == null ) {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"You are not a player of this game."]);
	// 	exit;
	// }

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

    if($res->num_rows > 0) {
        while($row = $res->fetch_assoc()) {
            if($row['c'] == 1 and $row['x'] == 'K') {
                print json_encode(['errormesg'=>$player . "Win!"]);
	         	exit;
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
                    $sql = "INSERT IGNORE INTO cards_players (x, y, player)
                            VALUES (?, ?, 'player_1')";
                    
                    $st = $mysqli->prepare($sql);
                    $st->bind_param('ss',$row["x"], $row["y"]);
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
                    $sql = "INSERT IGNORE INTO cards_players (x, y, player)
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

    $sql = 'SELECT x, y, player FROM cards_players';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function remove_cards($player) {
	global $mysqli;

    // if($player == null ) {
	// 	header("HTTP/1.1 400 Bad Request");
	// 	print json_encode(['errormesg'=>"You are not a player of this game."]);
	// 	exit;
	// }

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

?>