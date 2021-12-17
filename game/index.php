<?php 

require_once "../lib/db_connect.php";

$sql = "SELECT id, username FROM users";
$result = mysqli_query($mysqli, $sql);

if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "id: " . $row["id"] . " - username: " . $row["username"] . "<br>";
    }
}
else {
    echo "0 results";
}

?>