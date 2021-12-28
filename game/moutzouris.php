<?php

require_once "../lib/db_connect.php";
require_once "../lib/cards.php";
require_once "../lib/game.php";
require_once "../lib/players.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

if($input == null) {
    $input = [];
}

if(isset($_SERVER['HTTP_X_TOKEN'])) {
    $input['token'] = $_SERVER['HTTP_X_TOKEN'];
}
else {
    $input['token'] = '';
}

switch($r = array_shift($request)) {
    case 'cards':
            switch($b = array_shift($request)) {
                case '':
                case null:
                        handle_cards($method);
                    break;
            }
        break;
    case 'players':
            handle_player($method, $request, $input);
        break;
    case 'status':
            if(sizeof($request) == 0) {
                handle_status($method);
            }
            else {
                header("HTTP/1.1 404 Not Found");
            }
        break;
}





function handle_cards($method) {
    if ($method == 'GET') {
        show_cards();
    }
    else if ($method == 'POST') {
        reset_cards();
    }
    else {
        header('HTTP/1.1405 Method Not Allowed');
    }
}

function handle_player($method, $request, $input) {
    switch ($b = array_shift($request)) {
        case '':
        case null: 
            if($method == 'GET') {
                show_players();
            }
            else {
                header("HTTP/1.1 400 Bad Request"); 
                print json_encode(['errormesg'=>"Method $method not allowed here."]);
            }
            break;
        case 'player_1':
            case 'player_2':
                    handle_player($method, $request, $input);
                break;
        default:
                header("HTTP/1.1 404 Not Found");
                print json_encode(['errormesg'=>"Player $b not found."]);
            break;
    }
}

function handle_status($method) {
    if($method == 'GET') {
        show_status();
    }
    else {
        header('HTTP/1.1 405 Method Not Allowed');
    }
}
?>