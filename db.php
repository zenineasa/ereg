<?php
$user = "ecell";
$host = "localhost";
$pass = "ecell";
$table = "ecell";
global $db_connection;
$db_connection = new mysqli($host, $user, $pass, $table);
// Check connection
if ($db_connection->connect_error) {
    throw new Exception("Failed to connect to MySQL: " . mysqli_connect_error());
    unset($db_connection);
}
