<?php
// dbconnection.php 

// Define database connection parameters
$servername = "localhost";  // Server name
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "my database";     // Database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



