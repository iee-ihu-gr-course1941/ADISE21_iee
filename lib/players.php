<?php

function show_players() {
    global $mysqli;

    $sql = "SELECT username, player FROM players";
    $st = $mysqli -> prepare($sql);

    $st -> execute();
    $res = $st -> get_result();

    header('Content-type: application/json');
    print json_encode($res -> fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function handle_players($method, $request, $input) {
    if($method == 'GET') {
		show_player($input);
	} else if($method == 'PUT') {
        set_player($request, $input);
    }
}

function show_player($input) {
	global $mysqli;

	$request = $input['player'];

	$sql = 'SELECT username, player FROM players
            WHERE player=?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s', $request);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function set_player($request, $input) {
	global $mysqli;

	$username = $input['username'];
	$player = $input['player'];

	$sql = "SELECT count(*) AS c FROM players
            WHERE player=? AND username IS NOT NULL";

	$st = $mysqli->prepare($sql);
	$st->bind_param("s", $player);
	$st->execute();

	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if($r[0]['c']>0) {
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"$player is already set. Please select another player."]);

		exit;
	}

	$sql = "SELECT count(*) AS c FROM players
            WHERE username=?";

	$st = $mysqli->prepare($sql);
	$st->bind_param("s", $username);
	$st->execute();

	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if($r[0]['c']>0) {
		$sql = "UPDATE players SET player=?, token=md5(CONCAT(?, NOW()))
				WHERE username=?";

		$st2 = $mysqli->prepare($sql);
		$st2->bind_param("sss", $player, $username, $username);
		$st2->execute();
	}
	else {
		$sql = "INSERT INTO players (username, player, token)
				VALUES (?, ?, md5(CONCAT(?, NOW())))";

		$st2 = $mysqli->prepare($sql);
		$st2->bind_param("sss", $username, $player, $username);
		$st2->execute();
	}

	update_game_status();

	$sql = "SELECT * FROM players
            WHERE player=?";

	$st = $mysqli->prepare($sql);
	$st->bind_param("s", $player);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function current_player($token) {
	global $mysqli;

	if($token == null){
        return(null);
    }

	$sql = 'SELECT * FROM players
            WHERE token=?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$token);
	$st->execute();
	
	$res = $st->get_result();
	if($row = $res->fetch_assoc()) {
		return($row['player']);
	}
    
	return(null);
}
?>