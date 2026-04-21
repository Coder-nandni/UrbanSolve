<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "urban_problem";  
$conn = mysqli_connect("localhost","root","","urban_problem");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
