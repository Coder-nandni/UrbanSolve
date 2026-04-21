<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}

include("db.php");

$catQ = mysqli_query($conn, "SELECT category_name, priority FROM categories WHERE status=1 ORDER BY category_name DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report New Issue</title>

  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .card{ border-radius:18px; }
  </style>
</head>
<body>

<?php include_once("includes/leftnav.php"); ?>

<div class="col-lg-9 col-xl-10 main-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-primary mb-1">Report New Issue</h2>
      <p class="text-muted mb-0">Create a new complaint with location + image</p>
    </div>
    <a href="reports.php" class="btn btn-outline-dark">
      <i class="fa-solid fa-list me-2"></i> All Issues
    </a>
  </div>

  <div class="card p-4 shadow-sm border-0">
    <form action="save_issue.php" method="POST" enctype="multipart/form-data">

      <div class="row g-3 mb-2">
        <div class="col-md-6">
          <label class="form-label">Issue Title</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Water Pipe Leakage" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Category</label>
          <select name="category" id="categorySelect" class="form-select" required>
            <option value="">Select Category</option>
            <?php if($catQ): while($c=mysqli_fetch_assoc($catQ)): ?>
              <option
                value="<?php echo htmlspecialchars($c['category_name']); ?>"
                data-priority="<?php echo htmlspecialchars($c['priority']); ?>"
                data-department="<?php echo htmlspecialchars($c['category_name']); ?>"
				>
                <?php echo htmlspecialchars($c['category_name']); ?>
              </option>
            <?php endwhile; endif; ?>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Write issue details..." required></textarea>
      </div>

      <div class="row g-3 mb-2">
        <div class="mb-3">
          <label class="form-label">Location (Text)</label>
          <input type="text" name="location" id="locationText" class="form-control" placeholder="e.g. Shimlapuri, Ludhiana" required>
          <small class="text-muted"></small>
        </div>
        
      </div>

      <!-- lat/lng hidden -->
      <input type="hidden" name="latitude" id="lat">
      <input type="hidden" name="longitude" id="lng">

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Priority</label>
          <input type="text" id="priorityText" class="form-control" readonly>
          <input type="hidden" name="priority" id="priority">
        </div>
        <div class="col-md-6">
          <label class="form-label">Department</label>
          <input type="text" id="departmentText" class="form-control" readonly>
          <input type="hidden" name="department" id="department">
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label">Upload Image (Optional)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>

      <button type="submit" name="submit_issue" class="btn btn-primary">
        <i class="fa-solid fa-paper-plane me-2"></i> Submit Issue
      </button>

      <a href="dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
        <h5>Issue reported successfully!</h5>
      </div>
      <div class="modal-footer justify-content-center">
        <button class="btn btn-success" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// category -> priority/department auto
document.getElementById("categorySelect").addEventListener("change", function () {
  const option = this.options[this.selectedIndex];
  const priority = option.getAttribute("data-priority") || '';
  const dept = option.getAttribute("data-department") || '';

  document.getElementById("priority").value = priority;
  document.getElementById("department").value = dept;

  document.getElementById("priorityText").value = priority;
  document.getElementById("departmentText").value = dept;
});

// Geolocation + reverse geocode
async function reverseGeocode(lat, lng){
  // OpenStreetMap Nominatim (client-side)
  const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
  const res = await fetch(url, { headers: { "Accept":"application/json" } });
  if(!res.ok) return "";
  const data = await res.json();
  return data.display_name || "";
}

<?php if(isset($_GET['success'])): ?>
document.addEventListener("DOMContentLoaded", function () {
  new bootstrap.Modal(document.getElementById('successModal')).show();
});
<?php endif; ?>
</script>

</body>
</html>
