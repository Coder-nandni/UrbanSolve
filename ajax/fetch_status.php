<?php
include("../db.php");
session_start();

$user_id = $_SESSION['user_id'];

$result = mysqli_query($conn, "
SELECT id, status FROM issues WHERE user_id='$user_id'
");

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[$row['id']] = $row['status'];
}

echo json_encode($data);