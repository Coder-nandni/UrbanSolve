<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}

include("db.php");

// ---------- helpers ----------
function clean($conn, $v){
  return mysqli_real_escape_string($conn, trim((string)$v));
}

// ---------- ACTION HANDLERS ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  // ADD
  if ($action === 'add') {
    $name = clean($conn, $_POST['category_name'] ?? '');
    $priority = clean($conn, $_POST['priority'] ?? 'LOW');

    if ($name !== '') {
      $chk = mysqli_query($conn, "SELECT id FROM categories WHERE category_name='$name' LIMIT 1");
      if ($chk && mysqli_num_rows($chk) > 0) {
        header("Location: categories.php?err=duplicate");
        exit();
      }

      mysqli_query($conn, "INSERT INTO categories (category_name, priority, status) VALUES ('$name','$priority',1)");
      header("Location: categories.php?ok=added");
      exit();
    }
  }

  // EDIT
  if ($action === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $name = clean($conn, $_POST['category_name'] ?? '');
    $priority = clean($conn, $_POST['priority'] ?? 'LOW');
    $old_name = clean($conn, $_POST['old_name'] ?? '');

    if ($id > 0 && $name !== '') {
      $chk = mysqli_query($conn, "SELECT id FROM categories WHERE category_name='$name' AND id!=$id LIMIT 1");
      if ($chk && mysqli_num_rows($chk) > 0) {
        header("Location: categories.php?err=duplicate");
        exit();
      }

      mysqli_query($conn, "UPDATE categories SET category_name='$name', priority='$priority' WHERE id=$id");

      // Optional: rename existing issues.category too
      // (Recommended so dashboard counts remain correct after rename)
      if ($old_name !== '' && $old_name !== $name) {
        mysqli_query($conn, "UPDATE issues SET category='$name' WHERE category='$old_name'");
      }

      header("Location: categories.php?ok=updated");
      exit();
    }
  }

  // TOGGLE STATUS
  if ($action === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    $newStatus = (int)($_POST['new_status'] ?? 1);
    if ($id > 0) {
      mysqli_query($conn, "UPDATE categories SET status=$newStatus WHERE id=$id");
      header("Location: categories.php?ok=status");
      exit();
    }
  }

  // DELETE
  if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
      // Optional: if you want, you can also set issues.category='Other' before delete
      mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
      header("Location: categories.php?ok=deleted");
      exit();
    }
  }
}

// ---------- SUMMARY ----------
$totalCatsRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM categories"));
$totalCats = $totalCatsRow ? (int)$totalCatsRow['t'] : 0;

$activeCatsRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM categories WHERE status=1"));
$activeCats = $activeCatsRow ? (int)$activeCatsRow['t'] : 0;

$disabledCats = $totalCats - $activeCats;

$totalIssuesRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM issues"));
$totalIssues = $totalIssuesRow ? (int)$totalIssuesRow['t'] : 0;

// Top category by usage
$topCat = null;
$topQ = mysqli_query($conn, "
  SELECT category, COUNT(*) as c
  FROM issues
  WHERE category IS NOT NULL AND category!=''
  GROUP BY category
  ORDER BY c DESC
  LIMIT 1
");
if ($topQ && mysqli_num_rows($topQ) > 0) {
  $topCat = mysqli_fetch_assoc($topQ);
}

// ---------- LIST ----------
$listQ = mysqli_query($conn, "
  SELECT c.*,
         (SELECT COUNT(*) FROM issues i WHERE i.category = c.id) as issue_count
  FROM categories c
  ORDER BY c.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Category Management | Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
body{
  background:#f4f6f9;
}

/* Premium cards */
.soft-card{
  border:0;
  border-radius:18px;
  box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* KPI cards */
.kpi-card{
  border:0;
  border-radius:18px;
  color:white;
  box-shadow:0 10px 25px rgba(0,0,0,0.12);
}

.kpi-title{
  font-size:12px;
  opacity:.85;
  letter-spacing:.6px;
}

.kpi-value{
  font-size:26px;
  font-weight:700;
}

/* Icon circle */
.icon-box{
  width:42px;
  height:42px;
  border-radius:12px;
  background:rgba(255,255,255,0.18);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:18px;
}

/* Table polish */
.table thead th{
  font-size:13px;
  font-weight:600;
}

.table-hover tbody tr:hover{
  background:#f7f9ff;
}

/* Badges */
.badge-pill{
  border-radius:999px;
  padding:.42rem .6rem;
  font-weight:600;
}

/* Buttons */
.btn-rounded{
  border-radius:12px;
}
  </style>
</head>

<body>
<?php include_once("includes/leftnav.php"); ?>

<div class="col-lg-9 col-xl-10 main-content">

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-primary mb-1">Category Management</h2>
      <p class="text-muted mb-0">Manage categories used across dashboard cards & issue reporting</p>
    </div>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
      <i class="fa-solid fa-plus me-2"></i> Add Category
    </button>
  </div>

  <!-- ALERTS -->
  <?php if(isset($_GET['ok'])): ?>
    <div class="alert alert-success d-flex align-items-center gap-2">
      <i class="fa-solid fa-circle-check"></i>
      <div>Changes saved successfully.</div>
    </div>
  <?php endif; ?>

  <?php if(isset($_GET['err']) && $_GET['err']==='duplicate'): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <div>Duplicate category not allowed. Please use a different name.</div>
    </div>
  <?php endif; ?>

  <!-- KPI CARDS -->
<div class="row g-3 mb-3">

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#0d6efd,#4f9cff);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">TOTAL CATEGORIES</div>
          <div class="kpi-value"><?php echo $totalCats; ?></div>
        </div>
        <div class="icon-box"><i class="fa-solid fa-tags"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#198754,#2bb673);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">ACTIVE</div>
          <div class="kpi-value"><?php echo $activeCats; ?></div>
        </div>
        <div class="icon-box"><i class="fa-solid fa-toggle-on"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#6c757d,#495057);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">DISABLED</div>
          <div class="kpi-value"><?php echo $disabledCats; ?></div>
        </div>
        <div class="icon-box"><i class="fa-solid fa-toggle-off"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#ffc107,#ffb300);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">TOTAL ISSUES</div>
          <div class="kpi-value"><?php echo $totalIssues; ?></div>
        </div>
        <div class="icon-box"><i class="fa-solid fa-triangle-exclamation"></i></div>
      </div>
    </div>
  </div>

</div>

  <!-- TOP CATEGORY HIGHLIGHT -->
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-pill"><i class="fa-solid fa-chart-line"></i></div>
        <div>
          <div class="fw-semibold">Most Reported Category</div>
          <div class="text-muted small">
            <?php if($topCat): ?>
              <?php echo htmlspecialchars($topCat['category']); ?> — <?php echo (int)$topCat['c']; ?> issues
            <?php else: ?>
              No issue data yet.
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="chip">
        <i class="fa-solid fa-lightbulb"></i>
        <span>Tip: Keep category names short & unique</span>
      </div>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h5 class="mb-0"><i class="fa-solid fa-list-check me-2"></i> Category List</h5>
      <small class="text-muted">Dashboard cards automatically update from here</small>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="catTable">
          <thead class="table-light">
            <tr>
              <th style="width:70px;">ID</th>
              <th>Category</th>
              <th style="width:130px;">Priority</th>
              <th style="width:130px;">Status</th>
              <th style="width:120px;">Issues</th>
              <th style="width:230px;">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if($listQ && mysqli_num_rows($listQ)>0): ?>
            <?php while($c = mysqli_fetch_assoc($listQ)): ?>
              <tr>
                <td><?php echo (int)$c['id']; ?></td>

                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span class="icon-pill" style="width:34px;height:34px;border-radius:10px;">
                      <i class="fa-solid fa-tag"></i>
                    </span>
                    <div class="fw-semibold"><?php echo htmlspecialchars($c['category_name']); ?></div>
                  </div>
                </td>

                <td>
                  <?php
					$p = strtoupper($c['priority']);
					$color = ($p==='HIGH')?'danger':(($p==='MEDIUM')?'warning':'success');
					?>

					<span class="badge bg-<?php echo $color; ?> badge-pill">
					  <?php echo htmlspecialchars($c['priority']); ?>
					</span>
                </td>

                <td>
                  <?php if((int)$c['status'] === 1): ?>
                    <span class="badge bg-success">Active</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Disabled</span>
                  <?php endif; ?>
                </td>

                <td>
				  <a href="reports.php?category_id=<?php echo (int)$c['id']; ?>" class="text-decoration-none">
					<span class="badge bg-dark"><?php echo (int)$c['issue_count']; ?></span>
				  </a>
				</td>

                <td>
                  <button class="btn btn-sm btn-outline-primary"
                          data-bs-toggle="modal"
                          data-bs-target="#editModal"
                          data-id="<?php echo (int)$c['id']; ?>"
                          data-name="<?php echo htmlspecialchars($c['category_name']); ?>"
                          data-priority="<?php echo htmlspecialchars($c['priority']); ?>">
                    Edit
                  </button>

                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                    <input type="hidden" name="new_status" value="<?php echo ((int)$c['status']===1)?0:1; ?>">
                    <button type="submit" class="btn btn-sm btn-outline-dark">
                      <?php echo ((int)$c['status']===1) ? 'Disable' : 'Enable'; ?>
                    </button>
                  </form>

                  <form method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Add Category</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="action" value="add">

        <div class="mb-3">
          <label class="form-label">Category Name</label>
          <input type="text" name="category_name" class="form-control" required>
        </div>

        <div class="mb-2">
          <label class="form-label">Priority</label>
          <select name="priority" class="form-select" required>
            <option value="LOW">LOW</option>
            <option value="MEDIUM">MEDIUM</option>
            <option value="HIGH">HIGH</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST">
      <div class="modal-header">
        <h5 class="modal-title">Edit Category</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="e_id">
        <input type="hidden" name="old_name" id="e_old_name">

        <div class="mb-3">
          <label class="form-label">Category Name</label>
          <input type="text" name="category_name" id="e_name" class="form-control" required>
        </div>

        <div class="mb-2">
          <label class="form-label">Priority</label>
          <select name="priority" id="e_priority" class="form-select" required>
            <option value="LOW">LOW</option>
            <option value="MEDIUM">MEDIUM</option>
            <option value="HIGH">HIGH</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>
</div>

<?php include_once("includes/footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function(){
  $('#catTable').DataTable({
    pageLength: 8,
    lengthChange: false
  });
});

document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
  const btn = event.relatedTarget;
  const name = btn.getAttribute('data-name');
  const pr = btn.getAttribute('data-priority');

  document.getElementById('e_id').value = btn.getAttribute('data-id');
  document.getElementById('e_name').value = name;
  document.getElementById('e_old_name').value = name;
  document.getElementById('e_priority').value = pr;
});
</script>

</body>
</html>
