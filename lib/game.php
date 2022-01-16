<?php

function show_status($token) {
    global $mysqli;

	check_abort($token);

    $sql = "SELECT * FROM game_status";
    $st = $mysqli -> prepare($sql);

    $st -> execute();
    $res = $st ->get_result();

    header('Content-type: application/json');
    print json_encode($res -> fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function check_abort($token_) {
	global $mysqli;
	
	$player = current_player($token_);

	$sql = "SELECT count(*) AS c FROM game_status";

	$st = $mysqli->prepare($sql);
	$st->execute();

	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if($r[0]['c'] == 0) {
		$sql = "INSERT INTO game_status (last_change)
				VALUES (NOW())";

		$st = $mysqli->prepare($sql);
		$st->execute();
	}
	else {
		$sql = "SELECT count(*) AS c FROM players
				WHERE player='player_1'";

		$st = $mysqli->prepare($sql);
		$st->execute();

		$res = $st->get_result();
		$r = $res->fetch_all(MYSQLI_ASSOC);

		$sql1 = "SELECT count(*) AS c FROM players
				WHERE player='player_2'";

		$st1 = $mysqli->prepare($sql1);
		$st1->execute();

		$res1 = $st1->get_result();
		$r1 = $res1->fetch_all(MYSQLI_ASSOC);

		if($r[0]['c'] == 1 and $r1[0]['c'] == 0) {
			$sql = "UPDATE game_status SET status='aborded', p_turn=NULL, result='player_1'
					WHERE status='started'";
			
			$st = $mysqli->prepare($sql);
			$r = $st->execute();
		}
		else if($r[0]['c'] == 0 and $r1[0]['c'] == 1) {
			$sql = "UPDATE game_status SET status='aborded', p_turn=NULL, result='player_2'
					WHERE status='started'";
			
			$st = $mysqli->prepare($sql);
			$r = $st->execute();
		}
	}
}

function update_game_status() {
	global $mysqli;

	$status = read_status();

	$new_status = null;
	$new_turn = null;
	
	$st3=$mysqli->prepare('SELECT count(*) AS aborded FROM players
							WHERE last_action < (NOW() - INTERVAL 5 MINUTE)');
	
	$st3->execute();
	$res3 = $st3->get_result();
	$aborted = $res3->fetch_assoc()['aborded'];
	if($aborted>0) {
		$sql = "UPDATE players SET username = NULL, token = NULL
                WHERE last_action < (NOW() - INTERVAL 5 MINUTE)";

		$st2 = $mysqli->prepare($sql);
		$st2->execute();

        if($status['status'] == 'started') {
            $new_status='aborded';
        }
	}

	
	$sql = 'SELECT count(*) AS c FROM players
            WHERE username IS NOT NULL';

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	$active_players = $res->fetch_assoc()['c'];
	
	
	switch($active_players) {
		case 0:
                $new_status = 'not active';
            break;
		case 1:
                $new_status = 'initialized';
            break;
		case 2:
                $new_status = 'started'; 

                if($status['p_turn'] == null) {
                    $new_turn = 'player_1';
                }
			break;
	}

	$sql = 'UPDATE game_status SET status=?, p_turn=?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('ss', $new_status, $new_turn);
	$st->execute();
}

function read_status() {
	global $mysqli;
	
	$sql = 'SELECT * FROM game_status';
	$st = $mysqli->prepare($sql);

	$st->execute();
	$res = $st->get_result();
	$status = $res->fetch_assoc();

	return($status);
}

function reset_status() {
	global $mysqli;

	$sql = "DELETE FROM game_status
			WHERE result='player_1' OR result = 'player_2' OR p_turn = 'player_1' OR p_turn = 'player_2'";

	$st = $mysqli->prepare($sql);
	$st->execute();

	$sql = 'DELETE FROM players
			WHERE player="player_1" OR player="player_2"';

	$st = $mysqli->prepare($sql);
	$st->execute();
}

function change_p_turn() {
	global $mysqli;

	$sql = "SELECT p_turn FROM game_status";

	$st = $mysqli->prepare($sql);
	$st->execute();
	$res = $st->get_result();
	$p_turn = $res->fetch_assoc()['p_turn'];

	if($p_turn == "player_1") {
		$sql = 'UPDATE game_status SET p_turn="player_2"';

		$st = $mysqli->prepare($sql);
		$st->execute();
	}
	else if($p_turn == "player_2") {
		$sql = 'UPDATE game_status SET p_turn="player_1"';

		$st = $mysqli->prepare($sql);
		$st->execute();
	}
}

?>