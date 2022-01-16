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
                case 'remove':
                        handle_remove($method, $input);
                    break;
                case 'card':
                        handle_card($method, $input);
                    break;
                default: 
                    header("HTTP/1.1 404 Not Found");
                break;
            }
        break;
    case 'players':
            handle_player($method, $request, $input);
        break;
    case 'status':
            if(sizeof($request) == 0) {
                handle_status($method, $input);
            }
            else {
                header("HTTP/1.1 404 Not Found");
            }
        break;
    case 'reset':
            handle_reset($method);
        break;
    case 'p_turn':
            handle_p_turn($method);
        break;
    case 'check':
            handle_check($input);
        break;
    case 'users':
            handle_user($method);
        break;
}





function handle_cards($method) {
    if ($method == 'GET') {
        read_cards();
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
                    handle_players($method, $request, $input);
                break;
        default:
                header("HTTP/1.1 404 Not Found");
                print json_encode(['errormesg'=>"$request not found."]);
            break;
    }
}

function handle_status($method, $input) {
    if($method == 'GET') {
        show_status($input["token"]);
    }
    else {
        header('HTTP/1.1 405 Method Not Allowed');
    }
}

function handle_reset($method) {
    if($method == 'PUT') {
        reset_status();
    }
}

function handle_user($method) {
    if($method == 'GET') {
        get_user();
    }
}

function handle_card($method, $input) {
    if($method == 'GET') {
        show_card($input["x"]);
    } else if ($method == 'PUT') {
        move_card($input["x"][0], $input['token']);
    }    
}

function handle_remove($method, $input) {
    if($method == 'POST') {
        remove_cards($input['player']);
    }
    else {
        header('HTTP/1.1 405 Method Not Allowed');
    }
}

function handle_p_turn($method) {
    if($method == 'PUT') {
        change_p_turn();
    }
}

function handle_check($input) {
    check_for_win($input['token']);
}
?>