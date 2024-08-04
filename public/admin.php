<?php

/*
 * Author: Amy Huang & Anoushka Jawale
 * Creation Date: July 31, 2024
 * Last Updated: August 4, 2024
 * Description: This PHP file contains the Administrator logic for 1D Pacman.
 */

// VARIABLE DECLARATION: db connection
global $conn;

require_once('../config/_config.php');
include '../app/models/Game.php';

session_start();

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
    if ($_POST['action'] === "clearLeaderboard") {

        // PROCESS: preparing the SQL deletion
        $sql = $conn->prepare("TRUNCATE TABLE leaderboard");

        // PROCESS: executing the statement
        try {

            $sql->execute();
            $sql->close(); //closing sql

            // OUTPUT:
            echo json_encode("The leaderboard has been cleared!");
            exit;

        } catch (Exception $e) {

            // OUTPUT:
            echo json_encode($e->getMessage());
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
        <meta name="last_updated" content="July 31, 2024">
        <meta name="description" content="This is our work for Assignment 4 of CSI 3140.">
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
        <link rel="stylesheet" href="css/admin.css">

        <!--JQUERY SCRIPT-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!--SCRIPT-->
        <script src="js/index.js"></script>
    </head>

    <body>
        <!--HEADER-->
        <header>
            <h1>Administrator Actions</h1>
        </header>

        <!--ACTION BUTTONS-->
        <button id="clearLeaderboard-button" onclick="clearLeaderboard();">Clear Leaderboard</button>
        <form action="logout.php" method="post">
            <button id="return-home-button" type="submit">Return Home</button>
        </form>

        <!--FOOTER-->
        <footer>
            <p>1D PACMAN 2024</p>
        </footer>
    </body>
</html>