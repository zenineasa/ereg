<?php
error_reporting(E_ALL);
ini_set('display_errors', True);
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
