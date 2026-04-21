<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

include("db.php");

if(isset($_POST['name'], $_POST['zone'], $_POST['address'], $_POST['latitude'], $_POST['longitude'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $zone = mysqli_real_escape_string($conn, $_POST['zone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $lat = $_POST['latitude'];
$lng = $_POST['longitude'];

// Ludhiana boundary check
if ($lat < 30.75 || $lat > 31.05 || $lng < 75.70 || $lng > 76.00) {
    die("Only Ludhiana locations allowed");
}

    $priority = isset($_POST['priority']) ? mysqli_real_escape_string($conn, $_POST['priority']) : "medium";
    $desc = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : "";

    // student-level defaults
    $active_issues = 0;
    $resolved_issues = 0;
    $problem_heat = "low";  // low/medium/high

    $sql = "INSERT INTO locations
        (name, zone, address, latitude, longitude, active_issues, resolved_issues, problem_heat, priority, description, created_at)
        VALUES
        ('$name', '$zone', '$address', $lat, $lng, $active_issues, $resolved_issues, '$problem_heat', '$priority', '$desc', NOW())";

    if(mysqli_query($conn, $sql)){
        header("Location: location.php?success=1");
        exit;
    }else{
        echo "DB Error: " . mysqli_error($conn);
    }
}else{
    echo "Invalid Request!";
}
?>
