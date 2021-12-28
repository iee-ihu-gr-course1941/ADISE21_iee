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

function handle_player($method, $request, $input) {
    if($method == 'GET') {
		show_player($request);
	}
    else if($method == 'PUT') {
        set_player($b, $input);
    }
}

function show_player($request) {
	global $mysqli;

	$sql = 'SELECT username, player from players
            WHERE player=?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s', $request);
	$st->execute();
	$res = $st->get_result();

	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function set_player($request, $input) {
	if(!isset($input['username']) || $input['username'] == '') {
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"No username given."]);
		exit;
	}

	$username = $input['username'];

	global $mysqli;

	$sql = 'SELECT count(*) as c from players
            WHERE player = ? AND username IS NOT NULL';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
	$st->execute();

	$res = $st->get_result();
	$r = $res->fetch_all(MYSQLI_ASSOC);
	if($r[0]['c']>0) {
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"Player $b is already set. Please select another color."]);
		exit;
	}

	$sql = 'UPDATE players SET username=?, token=md5(CONCAT( ?, NOW()))
            WHERE player = ?';

	$st2 = $mysqli->prepare($sql);
	$st2->bind_param('sss',$username,$username,$b);
	$st2->execute();
	
	update_game_status();

	$sql = 'SELECT * FROM players
            WHERE player=?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$b);
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
            WHERE token = ?';

	$st = $mysqli->prepare($sql);
	$st->bind_param('s',$token);
	$st->execute();
	$res = $st->get_result();
	if($row = $res->fetch_assoc()) {
		return($row['piece_color']);
	}
    
	return(null);
}
?>