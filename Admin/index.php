<?php
session_start();

/* security + caching issues avoid */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION['admin_id'])) {
    header("refresh:1;url=dashboard.php");
} else {
    header("refresh:1;url=login.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>UrbanSolve Admin</title>
    <style>
        body{
            margin:0;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
            font-family:Arial;
            background:linear-gradient(135deg,#0d6efd,#0a58ca);
            color:white;
        }
        .box{
            text-align:center;
        }
        .loader{
            width:45px;
            height:45px;
            border:4px solid rgba(255,255,255,.3);
            border-top:4px solid white;
            border-radius:50%;
            margin:auto;
            animation:spin 1s linear infinite;
        }
        @keyframes spin{
            100%{ transform:rotate(360deg); }
        }
        h2{ margin-top:20px; }
    </style>
</head>
<body>

<div class="box">
    <div class="loader"></div>
    <h2>UrbanSolve Admin Panel</h2>
    <small>Loading secure dashboard...</small>
</div>

</body>
</html>
