<?php 
require_once "../../lib/db_connect.php";

global $mysqli;

session_start();

$username = $_SESSION['username'];

$sql = 'DELETE FROM players
        WHERE username=?';

$st = $mysqli->prepare($sql);
$st->bind_param('s', $username);
$st->execute();

session_destroy();

header("Location: index.php");

?>