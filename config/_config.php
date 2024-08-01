<?php

// VARIABLE DECLARATION: database information
$servername = getenv('DB_SERVER') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'password123';
$dbname = 'pacman';

// PROCESS: reading the SQL file for db creation
$sqlFile = '../create.sql';
$sql = file_get_contents($sqlFile);

if ($sql === false) {
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

if ($dbExistsResult->num_rows <= 0) { // database doesn't exist

    // OUTPUT:
    echo "Database '$dbname' does not exist. Proceeding with database creation.";

    // PROCESS: splitting SQL content into individual queries
    $sqlQueries = explode(';', $sql);

    // PROCESS: running all SQL creation queries
    foreach ($sqlQueries as $query) {
        $query = trim($query);

        if (!empty($query)) {
            if ($conn->query($query) === false) {
                // OUTPUT:
                echo 'Error executing query: ' . $conn->error;
            }
        }
    }
}

// VARIABLE DECLARATION:
$GLOBALS["appDir"] = resolve_path("app");

/**
 * This function resolves the path to the application directory.
 *
 * @param string $name the name of the directory to resolve
 * @return string the resolved path
 */
function resolve_path(string $name): string
{

    // PROCESS: checking directory
    if ($name == ".") {

        // PROCESS: updating roots
        $publicRoot = $_SERVER["DOCUMENT_ROOT"] . "/..";
        $appRoot = $_SERVER["DOCUMENT_ROOT"];

    } else if ($_SERVER["DOCUMENT_ROOT"] != "") {

        // PROCESS: updating roots
        $publicRoot = $_SERVER["DOCUMENT_ROOT"] . "/../$name";
        $appRoot = $_SERVER["DOCUMENT_ROOT"] . "/$name";

    } else {
        // OUTPUT:
        return "../$name";
    }

    // OUTPUT:
    return file_exists($publicRoot) ? realpath($publicRoot) : realpath($appRoot);

}

// PROCESS: auto-loading classes
spl_autoload_register(function ($fullName) {

    // VARIABLE DECLARATION:
    $parts = explode("\\", $fullName);
    $className = end($parts);
    $modelPath = $GLOBALS["appDir"] . "/models/$className.php";

    // PROCESS: checking for valid path
    if (file_exists($modelPath)) {
        require_once $modelPath;
    }

});

// PROCESS: closing the database connection
//$conn->close();
