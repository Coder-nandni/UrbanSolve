<?php
session_start();
include_once 'includes/db.php';

if(!isset($_SESSION['admin_id'])){
  header("Location: login.php");
  exit();
}

$hasCreatedAt = false;
$colCheck = mysqli_query($conn, "SHOW COLUMNS FROM issues LIKE 'created_at'");
if ($colCheck && mysqli_num_rows($colCheck) > 0) $hasCreatedAt = true;

$orderBy = $hasCreatedAt ? "ORDER BY created_at DESC" : "ORDER BY id DESC";
$q = mysqli_query($conn, "SELECT * FROM issues $orderBy LIMIT 200");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Urban Issues Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print { .no-print { display:none; } }
    body{ padding:20px; }
  </style>
</head>
<body>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">Urban Issues Report</h3>
      <small class="text-muted">Generated on: <?php echo date("d M Y, h:i A"); ?></small>
    </div>
    <button class="btn btn-dark no-print" onclick="window.print()">Print / Save PDF</button>
  </div>

  <table class="table table-bordered table-sm">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Category</th>
        <th>Location</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Reported</th>
      </tr>
    </thead>
    <tbody>
      <?php if($q && mysqli_num_rows($q)>0): ?>
        <?php while($r=mysqli_fetch_assoc($q)): ?>
          <tr>
            <td>ISS-<?php echo (int)$r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['title'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['category'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['location'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['priority'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['status'] ?? ''); ?></td>
            <td>
              <?php
              if($hasCreatedAt && !empty($r['created_at'])) echo date("d M Y, h:i A", strtotime($r['created_at']));
              else echo "—";
              ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted">No data</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
