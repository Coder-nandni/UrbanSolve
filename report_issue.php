<?php
include("db.php");
/* login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report Issue | Urban Pulse</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9;}
.card{border-radius:16px;}
</style>
</head>

<body>

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-8">

<div class="card shadow">
<div class="card-header bg-primary text-white">
<h4>Report a City Issue</h4>
</div>

<div class="card-body">
<form action="save_issue.php" method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label class="form-label">Issue Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Category</label>
<select name="category" class="form-select" required>
  <option value="">Select</option>
  <option value="Waste Management">Waste Management</option>
  <option value="Water Leakage">Water Leakage</option>
  <option value="Road Problem">Road Problem</option>
  <option value="Street Light">Street Light</option>
  <option value="Street Dogs">Street Dogs</option>
</select>

</div>

<div class="mb-3">
<label class="form-label">Description</label>
<textarea name="description" class="form-control" rows="4" required></textarea>
</div>

<div class="mb-3">
<label class="form-label">Priority</label>
<select name="priority" class="form-select">
  <option value="Low">Low</option>
  <option value="Medium" selected>Medium</option>
  <option value="High">High</option>
</select>
</div>

<div class="mb-3">
<label class="form-label">Location (Address)</label>
<input type="text" name="location" class="form-control" required>
</div>

<!-- hidden coords -->

<div class="mb-3">
<label class="form-label">Upload Image (optional)</label>
<input type="file" name="image" class="form-control">
</div>

<button type="submit" name="submit" class="btn btn-success">
Submit Issue
</button>

<a href="index.php" class="btn btn-secondary ms-2">Cancel</a>

</form>
</div>
</div>

</div>
</div>
</div>

<!-- AUTO LOCATION -->
<script>
navigator.geolocation.getCurrentPosition(
    function(pos){
        document.getElementById("latitude").value = pos.coords.latitude;
        document.getElementById("longitude").value = pos.coords.longitude;
    },
    function(){
        console.log("Location permission denied");
    }
);
</script>

</body>
</html>
