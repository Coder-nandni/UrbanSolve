<?php
session_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}include_once("includes/db.php");

// 🔥 SHOW ERRORS (IMPORTANT FOR DEBUG)
error_reporting(E_ALL);
ini_set('display_errors', 1);



$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    exit("<div class='text-danger'>Invalid Issue ID</div>");
}

// ✅ SAFE QUERY + ERROR HANDLING
$sql = "SELECT id, title, description, location, priority, status, image, reported_date 
        FROM issues WHERE id = $id";

$q = mysqli_query($conn, $sql);

if (!$q) {
    exit("<div class='text-danger'>SQL Error: " . mysqli_error($conn) . "</div>");
}

if (mysqli_num_rows($q) === 0) {
    exit("<div class='text-danger'>Issue not found</div>");
}

$row = mysqli_fetch_assoc($q);
?>

<h5 class="fw-bold mb-2"><?= htmlspecialchars($row['title'] ?? 'N/A') ?></h5>

<span class="badge bg-secondary mb-2">
  <?= htmlspecialchars($row['status'] ?? 'N/A') ?>
</span>

<p class="mt-3">
  <strong>Description:</strong><br>
  <?= nl2br(htmlspecialchars($row['description'] ?? 'No description')) ?>
</p>

<p><strong>Location:</strong> <?= htmlspecialchars($row['location'] ?? 'N/A') ?></p>
<p><strong>Priority:</strong> <?= htmlspecialchars($row['priority'] ?? 'N/A') ?></p>

<p><strong>Date:</strong>
<?= !empty($row['reported_date']) 
    ? date("d M Y, h:i A", strtotime($row['reported_date'])) 
    : "N/A"; ?>
</p>

<?php if (!empty($row['image'])): ?>
  <img src="../uploads/<?= htmlspecialchars($row['image']) ?>"
       class="img-fluid rounded mt-2"
       style="max-height:220px">
<?php endif; ?>

<hr>

<a href="reports.php?id=<?= (int)$row['id'] ?>"
   class="btn btn-primary btn-sm">
   View Full Details
</a>