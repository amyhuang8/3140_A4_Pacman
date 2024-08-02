<?php

/*
 * Author: Amy Huang & Anoushka Jawale
 * Creation Date: July 10, 2024
 * Last Updated: August 1, 2024
 * Description: This PHP file contains the runtime logic for 1D Pacman.
 */

// VARIABLE DECLARATION: db connection
global $conn;

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

    header('Content-Type: application/json'); //setting header

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

    if ($_POST['action'] === "updateLeaderboard") { // updating the leaderboard

        // VARIABLE DECLARATION:
        $name = $game->getName();
        $score = $game->getScore();

        // PROCESS: preparing the SQL query to check for duplicates
        $check_sql = $conn->prepare("SELECT COUNT(*) FROM leaderboard WHERE username=? AND highscore=?");
        $check_sql->bind_param("si", $name, $score);

        // PROCESS: executing the check
        $check_sql->execute();
        $check_sql->bind_result($count);
        $check_sql->fetch();
        $check_sql->close();

        // PROCESS: checking for duplicate entry
        if ($count === 0) {

            // PROCESS: preparing the SQL query to insert a new record
            $sql = $conn->prepare("INSERT INTO leaderboard (username, highscore) VALUES (?, ?)");

            // PROCESS: binding parameters to statement
            $sql->bind_param("si", $name, $score);

            // PROCESS: executing the statement
            try {

                $sql->execute();
                $sql->close();

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

    if ($_POST['action'] === "displayLeaderboard") { //showing the leaderboard

        // PROCESS: preparing the SQL query
        $sql = $conn->prepare("SELECT * FROM leaderboard ORDER BY highscore DESC LIMIT 10");

        // PROCESS: executing the query
        $sql->execute();

        // VARIABLE DECLARATION: saving the results
        $result = $sql->get_result();

        // VARIABLE DECLARATION: fetching all results as an associative array
        $leaderboard = $result->fetch_all(MYSQLI_ASSOC);
        $response = [
            'leaderboard' => $leaderboard,
        ];

        $sql->close(); //closing sql

        // OUTPUT:
        echo json_encode($response);
        exit;

    }

    if ($_POST['action'] === "resetGame") { //resetting the game

        // VARIABLE DECLARATION:
        $name = $game->getName();
        $highScore = $game->getHighScore();

        // PROCESS: preparing the SQL query to update a record
        $sql = $conn->prepare("UPDATE users SET highscore=? WHERE username=?");

        // PROCESS: binding parameters to statement
        $sql->bind_param("is", $highScore, $name);

        // PROCESS: executing the statement
        try {

            $sql->execute();
            $sql->close();

        } catch (Exception $e) {

            // OUTPUT:
            error_log($e->getMessage());

        }

        $game->resetGame();
        $_SESSION['game'] = $game; //updating session var.

        // VARIABLE DECLARATION:
        $response = [
            'board' => $game->getBoard(),
            'directionPM' => $game->getDirectionPM(),
            'directionGhost' => $game->getDirectionGhost(),
            'highScore' => $highScore,
            'level' => $game->getLevel(),
        ];

        // OUTPUT:
        echo json_encode($response);
        exit;

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
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/run_game.css">

        <!--JQUERY SCRIPT-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <body>
        <!--HEADER/GAME STATS-->
        <header>
            <h1 id="score">Score: 0</h1>
            <h1 id="high-score">High Score: 0</h1>
            <h1 id="level">Level: 1</h1>
            <button id="begin-button" onclick="resetGame();">Start</button>
            <form action="logout.php" method="post">
                <button id="exit-button" type="submit">
                    <img src="resources/icon_home.png" alt="Home icon">
                </button>
            </form>
        </header>

        <!--GAME BOARD-->
        <div class="game-borders" style="height: 25vh;">
            <div class="game-borders" style="height: 20vh;">
                <div id="game-container" class="game-board"></div>
            </div>
        </div>

        <!--INSTRUCTIONS MODAL-->
        <div id="instructionsModal" class="modal">
            <div class="modal-content">
                <h1 class="modal-text">Instructions:</h1>
                <h1 class="modal-text">Use A/D or left/right arrow keys to change direction!</h1>
                <h2 class="modal-text">Click outside to START.</h2>
                <br>
            </div>
        </div>

        <!--GAME OVER MODAL-->
        <div id="gameOverModal" class="modal">
            <div class="modal-content">
                <h1 class="modal-text">Game over!</h1>
                <h1 class="modal-text">Do you want to play again?</h1>
                <button id="restart">Restart</button>
            </div>
        </div>

        <!--LEADERBOARD MODAL-->
        <div id="leaderboardModal" class="modal">
            <div class="modal-content">
                <h1 class="modal-text">Leaderboard:</h1>
                <ul id="leaderboardList" class="modal-text"></ul>
                <button id="closeLeaderboard">Close</button>
            </div>
        </div>

        <!--AUDIOS-->
        <audio id="bgm" src="resources/audio/beat_pacman.mp3" loop></audio>
        <audio id="sfx-fruit" src="resources/audio/sfx_fruit.mp3"></audio>
        <audio id="sfx-level-up" src="resources/audio/sfx_level-up.mp3"></audio>
        <audio id="sfx-game-over" src="resources/audio/sfx_game-over.mp3"></audio>

        <!--FOOTER-->
        <footer>
            <button id="leaderboard-button" onclick="displayLeaderboard();">Show Leaderboard</button>
            <p>1D PACMAN 2024</p>
        </footer>

        <!--SCRIPT-->
        <script type="text/javascript" src="js/run_game.js"></script>
    </body>
</html>
