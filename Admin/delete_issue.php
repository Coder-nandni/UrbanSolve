<?php
include("db.php");

$id = $_GET['id'];

// image delete (optional but professional)
$q = "SELECT image FROM issues WHERE id='$id'";
$res = mysqli_query($conn, $q);
$row = mysqli_fetch_assoc($res);

if(!empty($row['image']) && file_exists("uploads/".$row['image'])){
    unlink("uploads/".$row['image']);
}

// delete record
$delete = "DELETE FROM issues WHERE id='$id'";
mysqli_query($conn, $delete);

header("Location: view_issue.php");
?>
