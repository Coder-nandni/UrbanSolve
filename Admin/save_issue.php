<?php
include("db.php");

if(isset($_POST['submit'])){

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';

    /* IMAGE UPLOAD */
   $uploadDir = __DIR__ . "/uploads/issues/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imageName = time() . "_" . basename($_FILES['image']['name']);
move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);

    if(mysqli_query($conn, $query)){
        echo "
        <script>
            alert('✅ Issue reported successfully');
            window.location='index.php';
        </script>";
    }else{
        echo "
        <script>
            alert('❌ Something went wrong');
            window.history.back();
        </script>";
    }
}
$newId = mysqli_insert_id($conn);

header("Location: dashboard.php?success=1&ticket=".$newId);
exit;

?>
