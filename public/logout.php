<?php

/*
 * Author: Amy Huang & Anoushka Jawale
 * Creation Date: August 1, 2024
 * Last Updated: August 4, 2024
 * Description: This PHP file contains the exit logic to destroy the session info for 1D Pacman.
 */

session_start();

session_unset();
session_destroy();
header("Location: index.php"); //redirecting to index.php

exit();
