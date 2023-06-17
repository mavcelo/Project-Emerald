<?php
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', 'your_db_password');
    define('DB_NAME', 'phplogin');


    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die('Connection Failed ' . $conn->connect_error);
    }

?>