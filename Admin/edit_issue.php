<?php
include("db.php");

$id = $_GET['id'];

$query = "SELECT * FROM issues WHERE id='$id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){
    $title = $_POST['title'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $department = $_POST['department'];

    $update = "UPDATE issues SET 
        title='$title',
        category='$category',
        location='$location',
        priority='$priority',
        status='$status',
        department='$department'
        WHERE id='$id'";

    if(mysqli_query($conn, $update)){
        header("Location: view_issue.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h3>Edit Issue</h3>

    <form method="post" class="card p-4 shadow">
        <label>Title</label>
        <input type="text" name="title" value="<?= $row['title'] ?>" class="form-control mb-3" required>

        <label>Category</label>
        <input type="text" name="category" value="<?= $row['category'] ?>" class="form-control mb-3">

        <label>Location</label>
        <input type="text" name="location" value="<?= $row['location'] ?>" class="form-control mb-3">

        <label>Priority</label>
        <select name="priority" class="form-control mb-3">
            <option value="low" <?= $row['priority']=='low'?'selected':'' ?>>Low</option>
            <option value="medium" <?= $row['priority']=='medium'?'selected':'' ?>>Medium</option>
            <option value="high" <?= $row['priority']=='high'?'selected':'' ?>>High</option>
        </select>

        <label>Status</label>
        <select name="status" class="form-control mb-3">
            <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
            <option value="resolved" <?= $row['status']=='resolved'?'selected':'' ?>>Resolved</option>
        </select>

        <label>Department</label>
        <input type="text" name="department" value="<?= $row['department'] ?>" class="form-control mb-3">

        <button name="update" class="btn btn-success">Update Issue</button>
        <a href="view_issue.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
