<?php

ob_implicit_flush(true); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test_db";

$conn = new mysqli($servername, $username, $password);

//Connect to the server
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "<br>");
} else {
    //echo "Connection successful<br>";
}

//Create a database to store preferences
$sql_create_database = "CREATE DATABASE IF NOT EXISTS test_db";

if ($conn->query($sql_create_database) == TRUE) {
    //echo "Database created successfully<br>";
} else {
    echo "Failed to create a database: " . $conn->error . "<br>";
}


//Select the created database
$conn->select_db($dbname);

?>