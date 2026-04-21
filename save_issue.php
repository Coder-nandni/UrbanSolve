<?php

include("db.php");
session_start();

$user_id = $_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
	$priority = $_POST['priority'];
    $category = $_POST['category_id'];
	$lat = $_POST['latitude'];
$lng = $_POST['longitude'];


    $filename = "";
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . "." . $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "uploads/" . $filename
        );
    }

    // 🔥 STEP 1: INSERT without ticket_no
    $sql = "INSERT INTO issues 
	(user_id, title, category, description, location, latitude, longitude, image, priority, status, reported_date, is_read, created_at) 
	VALUES 
	('$user_id', '$title', '$category', '$description', '$location','$lat', '$lng', '$filename', '$priority', 'Pending', NOW(), 0, NOW())";

    if(mysqli_query($conn, $sql)){

        // 🔥 STEP 2: Get last inserted ID
        $last_id = mysqli_insert_id($conn);

        // 🔥 STEP 3: Generate clean ticket
        $ticket_no = "UP-" . str_pad($last_id, 5, "0", STR_PAD_LEFT);

        // 🔥 STEP 4: Update ticket_no
        mysqli_query($conn, "UPDATE issues SET ticket_no='$ticket_no' WHERE id='$last_id'");

        // 🔥 STEP 5: Redirect
        header("Location: dashboard.php?ticket=" . $ticket_no);

    } else {
        echo "Error: " . mysqli_error($conn);
    }

    exit;
}
?>