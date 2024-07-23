<?php

if ($_SERVER["action"] == "sendForm") {
        // Get the form data
        $name = htmlspecialchars($_POST['name']);
        $password = htmlspecialchars($_POST['password']);
        $location = htmlspecialchars($_POST['location']);

        // Process the data (e.g., save to a database, perform validation, etc.)
        // For demonstration, we'll just print the data
        echo "Name: " . $name . "<br>";
        echo "Password: " . $password . "<br>";
        echo "Location: " . $location . "<br>";

        error_log("Name: " . $name);
        error_log("Password: " . $password);
        // Redirect or perform further actions
        // header("Location: success_page.php");
        // exit();
    } else {
        // Handle the case where the form was not submitted via POST
        echo "Invalid request method.";
    }

    ?>