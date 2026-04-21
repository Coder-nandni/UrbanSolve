<?php
include("db.php");

$id = $_POST['id'];
$action = $_POST['action'];

// YES → solved
if($action == "yes"){

    mysqli_query($conn, "
        UPDATE issues 
        SET 
            user_confirmed = 1,
            user_satisfaction = 'Satisfied'
        WHERE id = '$id'
    ");

    echo "Solved updated";
}

// NO → not solved + feedback + rating
if($action == "no"){

    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    $rating = $_POST['rating'] ?? 0;

    mysqli_query($conn, "
        UPDATE issues 
        SET 
            user_confirmed = 2,
            user_satisfaction = 'Not Satisfied',
            user_feedback = '$feedback',
            rating = '$rating',
            status = 'In Progress'
        WHERE id = '$id'
    ");

    echo "Feedback saved";
}
?>