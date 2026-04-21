<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("db.php");
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile    = mysqli_real_escape_string($conn, $_POST['mobile']);
	$location    = mysqli_real_escape_string($conn, $_POST['location']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // check duplicate email
    $check = mysqli_query($conn, "SELECT id FROM members WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists";
    } else {

        $q = mysqli_query($conn,"
            INSERT INTO members
            (full_name, email, mobile,location, password, status, created_at)
            VALUES
            ('$full_name', '$email', '$mobile','$location', '$password', 'inactive', NOW())
        ");

        if ($q) {
            header("Location: citizens.php?success=1");
            exit();
        } else {
            $error = "Something went wrong!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Citizen | UrbanSolve</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body{background:#f5f6fa}
.card{border-radius:16px}
</style>
</head>
<body>

<div class="container-fluid">
<div class="row">

<?php include_once("includes/leftnav.php"); ?>

<div class="col-lg-9 col-xl-10 p-4">

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h2 class="fw-bold text-primary">Add New Citizen</h2>
    <p class="text-muted mb-0">Create a new citizen account manually</p>
  </div>
  <a href="citizens.php" class="btn btn-outline-dark">
    <i class="fas fa-arrow-left"></i> Back
  </a>
</div>

<?= $msg ?>

<div class="card shadow-sm">
<div class="card-body">

<form method="POST">
  <div class="mb-3">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Mobile</label>
    <input type="text" name="mobile" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Area</label>
    <input type="text" name="location" class="form-control" required>
  </div>
  
  <div class="mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>

  <button class="btn btn-primary">
    <i class="fa fa-user-plus"></i> Add Citizen
  </button>
</form>

</form>

</div>
</div>

<?php include_once("includes/footer.php"); ?>

</div>
</div>
</div>

</body>
</html>
