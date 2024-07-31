<?php

/*
 * Author: Amy Huang & Anoushka Jawale
 * Creation Date: July 10, 2024
 * Last Updated: June 12, 2024
 * Description: This PHP file contains the runtime logic for 1D Pacman.
 */

$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "pacman";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require_once('../config/_config.php');
include '../app/models/Game.php';

session_start(); //starting session

// PROCESS: checking if game state already exists in session
if (isset($_SESSION['game'])) { //exists
    $game = $_SESSION['game'];
} else { //does not
    // VARIABLE DECLARATION: new game
    $game = new Game();
    $_SESSION['game'] = $game;
}

// PROCESS: checking for POST req. from front-end
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // PROCESS: handling AJAX
    if ($_POST['action'] === "moveGhost") { //moving the ghost

        $game->moveGhost();
        $_SESSION['game'] = $game; //updating session var.

        // VARIABLE DECLARATION:
        $response = [
            'board' => $game->getBoard(),
            'directionPM' => $game->getDirectionPM(),
            'directionGhost' => $game->getDirectionGhost(),
            'score' => $game->getScore(),
            'isGameOver' => $game->isGameOver(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST['action'] === "moveLeftPacman") { //moving Pacman to the left

        $game->moveLeftPacman();
        $_SESSION['game'] = $game; //updating session var.

        // VARIABLE DECLARATION:
        $response = [
            'board' => $game->getBoard(),
            'directionPM' => $game->getDirectionPM(),
            'directionGhost' => $game->getDirectionGhost(),
            'isFruitEaten' => $game->isFruitEaten(),
            'score' => $game->getScore(),
            'level' => $game->getLevel(),
            'isGameAdvanced' => $game->isGameAdvanced(),
            'isGameOver' => $game->isGameOver(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST['action'] === "moveRightPacman") { //moving Pacman to the right

        $game->moveRightPacman();
        $_SESSION['game'] = $game; //updating session var.

        // VARIABLE DECLARATION:
        $response = [
            'board' => $game->getBoard(),
            'directionPM' => $game->getDirectionPM(),
            'directionGhost' => $game->getDirectionGhost(),
            'isFruitEaten' => $game->isFruitEaten(),
            'score' => $game->getScore(),
            'level' => $game->getLevel(),
            'isGameAdvanced' => $game->isGameAdvanced(),
            'isGameOver' => $game->isGameOver(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST['action'] === "advanceLevel") { //advancing the game level

        $game->createNewBoard();
        $_SESSION['game'] = $game; //updating session var.

        // VARIABLE DECLARATION:
        $response = [
            'board' => $game->getBoard(),
            'directionPM' => $game->getDirectionPM(),
            'directionGhost' => $game->getDirectionGhost(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST['action'] === "displayLeaderboard") { //showing the leaderboard

        // VARIABLE DECLARATION:
        $response = [
            'leaderboard' => $game->getLeaderboard(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST['action'] === "resetGame") { //resetting the game

        $game->resetGame();
        $_SESSION['game'] = $game; //updating session var.

        // VARIABLE DECLARATION:
        $response = [
            'board' => $game->getBoard(),
            'directionPM' => $game->getDirectionPM(),
            'directionGhost' => $game->getDirectionGhost(),
            'highScore' => $game->getHighScore(),
            'level' => $game->getLevel(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST["action"] == "sendForm") { //sending sign-up form data

        // VARIABLE DECLARATION:
        $username = htmlspecialchars($_POST['name']);
        $password = htmlspecialchars($_POST['password']);
        $location = htmlspecialchars($_POST['location']);

        // PROCESS: preparing the SQL insertion
        $sql = $conn->prepare("INSERT INTO users (username, password, country) VALUES (?, ?, ?)");

        // PROCESS: binding parameters to statement
        $sql->bind_param("sss", $username, $password, $location);

        // PROCESS: executing the statement
        try {

            $sql->execute();

            // VARIABLE DECLARATION:
            $response = [
                'isSuccess' => true
            ];

            // OUTPUT:
            echo json_encode($response);
            exit;

        } catch (Exception $e) {

            // VARIABLE DECLARATION:
            $response = [
                'isSuccess' => false,
                'errorMsg' => $e->getMessage()
            ];

            // OUTPUT:
            echo json_encode($response);
            exit;

        }
    }

}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!--META DATA-->
        <meta charset="UTF-8">
        <meta name="author" content="Amy Huang & Anoushka Jawale">
        <meta name="creation_date" content="July 10, 2024">
        <meta name="last_updated" content="July 11, 2024">
        <meta name="description" content="This is our work for Assignment 3 of CSI 3140.">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--WEBSITE TITLE-->
        <title>1D Pacman</title>

        <!--FAVICONS-->
        <link rel="apple-touch-icon" sizes="180x180" href="resources/favicon.ico">
        <link rel="icon" type="image/png" sizes="32x32" href="resources/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="resources/favicon-16x16.png">
        <link rel="manifest" href="resources/site.webmanifest">

        <!--STYLESHEET-->
        <link rel="stylesheet" href="css/sign_up.css">

        <!--JQUERY SCRIPT-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    </head>

    <body>
    <h1>Sign up</h1>

    <div id="form-bg">
        <form id="myForm" onsubmit="sendForm(); return false;">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="location">Location:</label><br>
            <input type="text" id="location" name="location" required><br><br>

            <button type="submit">Submit</button>
            <input type="hidden" name="action" value="sendForm">
        </form>
</div>
        <!--FOOTER-->
        <footer>
            <p>1D PACMAN 2024</p>
        </footer>

        <!--SCRIPT-->
        <script src="js/user_input.js"></script>

    </body>
</html>
