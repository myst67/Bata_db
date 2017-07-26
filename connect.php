<?php
session_start();
echo 'ss';
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$_SESSION["current_db"] = "bata_schema";

if($_SESSION["current_db"] !== null)
{
	mysqli_select_db($conn,$_SESSION["current_db"] or die(mysql_error()));
}

?>