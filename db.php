<?php
// db.php
function db_connect(){
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'alinq';

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die('DB connection error: ' . $conn->connect_error);
    }
    // set charset
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>