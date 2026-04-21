<?php
session_start();
include("db.php");

$email = $_POST['email'];
$pass  = md5($_POST['password']);

$q = mysqli_query($conn,"SELECT * FROM users WHERE email='$email' AND password='$pass'");

if(mysqli_num_rows($q)==1){
    $user = mysqli_fetch_assoc($q);
    $_SESSION['user_id'] = $user['id'];
    header("Location: dashboard.php");
}else{
    echo "<script>alert('Invalid Login');window.location='index.php';</script>";
}
