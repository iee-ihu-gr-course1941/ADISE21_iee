<?php

require_once "../lib/db_connect.php";
require_once "../lib/cards.php";

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
}

function handle_board($method) {
    if ($method == 'GET') {
        show_cards();
    }
    else if ($method == 'POST') {
        reset_cards();
    }
}
?>