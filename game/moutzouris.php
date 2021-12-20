<?php

require_once "../lib/db_connect.php";
require_once "../lib/cards.php";
require_once "../lib/game.php";

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

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
    
}

function handle_status($method) {
    if($method == 'GET') {
        show_status();
    }
    else {
        header('HTTP/1.1405 Method Not Allowed');
    }
}
?>