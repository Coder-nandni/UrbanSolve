<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id     = $_SESSION['user_id'];
    $category    = $_POST['category'];
    $description = $_POST['description'];

    $q = "INSERT INTO complaints (user_id, category, description)
          VALUES ('$user_id', '$category', '$description')";

    mysqli_query($conn, $q);

    header("Location: dashboard.php");
    exit;
}
