<?php

/*
 * Author: Amy Huang & Anoushka Jawale
 * Creation Date: July 10, 2024
 * Last Updated: June 12, 2024
 * Description: This PHP file contains the runtime logic for 1D Pacman.
 */

// VARIABLE DECLARATION: database information
$servername = "localhost";
$username = "root";
$password = "password123";
$dbname = "pacman";

// PROCESS: reading the SQL file for db creation
$sqlFile = '../create.sql';
$sql = file_get_contents($sqlFile);

if ($sql === false) { //error-handling
    // OUTPUT:
    die('Error reading SQL file.');
}

// PROCESS: creating new db connection
$conn = new mysqli($servername, $username, $password, $dbname);

// PROCESS: checking db connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PROCESS: checking if database already exists
$dbExistsQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'";
$dbExistsResult = $conn->query($dbExistsQuery);

if ($dbExistsResult->num_rows <= 0) { //doesn't already exist

    // OUTPUT:
    echo "Database '$dbname' does not exist. Proceeding with database creation.";

    // PROCESS: splitting SQL content into individual queries
    $sqlQueries = explode(';', $sql);

    // PROCESS: running all SQL creation queries
    foreach ($sqlQueries as $query) {

        // VARIABLE DECLARATION:
        $query = trim($query);

        // PROCESS: checking that query is not empty
        if (!empty($query)) {

            // PROCESS: checking for success running query
            if ($conn->query($query) === false) { //error-handling
                // OUTPUT:
                echo 'Error executing query: ' . $conn->error;
            }

        }

    }

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
    if ($_POST["action"] == "sendForm") { //sending game session form data

        // VARIABLE DECLARATION:
        $name = htmlspecialchars($_POST['name']);
        $game->setName($name); //updating username
        $_SESSION['game'] = $game; //updating session var.

        $password = htmlspecialchars($_POST['password']);
        $location = htmlspecialchars($_POST['location']);

        // PROCESS: checking for admin login
        if ($name === "admin" && $password === "adminpassword" && $location === "adminoffice") {

            // PROCESS: preparing the SQL query
            $sql = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");

            // PROCESS: binding parameters to statement
            $sql->bind_param("ss", $name, $password);

            $isAdmin = true; //updating flag

        } else {

            // PROCESS: preparing the SQL insertion
            $sql = $conn->prepare("INSERT INTO users (username, password, country) VALUES (?, ?, ?)");

            // PROCESS: binding parameters to statement
            $sql->bind_param("sss", $name, $password, $location);

            $isAdmin = false; //updating flag

        }

        // PROCESS: executing the statement
        try {

            $sql->execute();

            // PROCESS: checking if user is logging in as the admin
            if ($isAdmin) {

                // PROCESS: retrieving the results of the SQL query
                $result = $sql->get_result();

                // PROCESS: checking if admin exists in the database
                if ($result->num_rows > 0) { //exists

                    // VARIABLE DECLARATION:
                    $response = [
                        'isSuccess' => true,
                        'isAdmin' => $isAdmin
                    ];

                } else { //does not exist

                    // VARIABLE DECLARATION:
                    $response = [
                        'isSuccess' => false,
                        'isAdmin' => $isAdmin,
                        'errorMsg' => "Admin does not exist in database."
                    ];

                }

            } else {

                // VARIABLE DECLARATION:
                $response = [
                    'isSuccess' => true,
                    'isAdmin' => $isAdmin
                ];

            }

            $sql->close(); //closing sql

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

        <!--STYLESHEETS-->
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/index.css">

        <!--JQUERY SCRIPT-->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!--SCRIPT-->
        <script src="js/index.js"></script>
    </head>

    <body>
        <!--HEADING-->
        <header><h1>Enter Game Session</h1></header>

        <!--GAME SESSION FORM-->
        <form id="myForm" onsubmit="sendForm(); return false;">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="location">Location:</label><br>
            <input type="text" id="location" name="location" required><br><br>

            <button id="enter-game-button" type="submit">Submit</button>
            <input type="hidden" name="action" value="sendForm">
        </form>

        <!--FOOTER-->
        <footer>
            <p>1D PACMAN 2024</p>
        </footer>
    </body>
</html>
