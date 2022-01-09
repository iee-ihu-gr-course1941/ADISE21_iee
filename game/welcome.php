<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./css/style_welcome.css">
    <script src="./js/jquery/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="./js/script_welcome.js"></script>

    <script type="text/javascript" src="./js/moutzouris.js"></script>
    <link rel="stylesheet" href="./css/moutzouris.css">

    <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="./bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap" rel="stylesheet">

    <link rel="icon" href="./images/favicon.png">
    <title>Moutzouris - Card Game</title>
</head>
<body>
    <div class="loader-container">
        <div class="loader"></div>
    </div>

    <div class="body-container">
        <button onclick="location.href='./login_register/logout.php';">Logout</button>
    </div>

    <p>WELCOME
        <span id="paragraph">
            <?php
                session_start();
                echo $_SESSION['username'];
            ?>
        </span>
    </p>

    <div id="game" style="display: flex; align-items: center; justify-content: center;">
        <br>
        <select id="menu" class="form-select" aria-label="Default select example" style="width: 15%">
            <option value="player_1">Player 1</option>
            <option value="player_2">Player 2</option>
        </select>
        &nbsp;
        <input id="username" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" style="width: 15%" readonly>
        &nbsp;
        <button id="btn_login" onclick="login_to_game();" type="button" class="btn btn-outline-dark" style="font-family: 'Permanent Marker', cursive; color: white; border: 2px solid black;">Go</button>
    </div>

    </br></br>

    <div id="player_1"></div>
    <button  id="btn-remove" onclick="do_remove();" class="btn btn-outline-dark" style="margin: 5%; font-family: 'Permanent Marker', cursive; color: white; border: 2px solid black; display: none;">ΔΙΩΞΕ ΦΥΛΑ</button>
    <div id="move_div" style="margin: 5%; display: none;"><p style="display: inline;">Διάλεξε φύλο (0 - ...):</p> <input id='the_move' type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" style="width: 15%; display: inline;">&nbsp;<button  onclick="do_move();" class="btn btn-outline-dark" style="font-family: 'Permanent Marker', cursive; color: white; border: 2px solid black;">ΠΑΡΕ ΦΥΛΟ</button></div>
    <div id="player_2"></div>
    <div id="game_info"></div>
</body>
</html>