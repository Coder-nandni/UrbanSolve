<?php
$conn = mysqli_connect("localhost","root","","urban_problem");

if(!$conn){
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
