<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

include("db.php");
include_once 'includes/priority_helpers.php';

/* ---------------------------
   Helpers
----------------------------*/
function esc($v){
  return htmlspecialchars($v ?? "", ENT_QUOTES, "UTF-8");
}
function sql_like_escape($str){
  return str_replace(['\\','%','_'], ['\\\\','\%','\_'], $str);
}
function getCount($conn, $whereSql, $extraAnd=""){
  // $whereSql already contains " AND ..." if filters exist
  $q = "SELECT COUNT(*) AS c FROM issues WHERE 1 $whereSql $extraAnd";
  $r = mysqli_query($conn, $q);
  if(!$r) return 0;
  $row = mysqli_fetch_assoc($r);
  return (int)($row['c'] ?? 0);
}

/* ---------------------------
   Read Filters (GET)
----------------------------*/
$userId   = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$search   = trim($_GET['search'] ?? "");
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$category = trim($_GET['category'] ?? "");
$status   = trim($_GET['status'] ?? "");
$priority = trim($_GET['priority'] ?? "");
$from     = trim($_GET['from'] ?? "");
$to       = trim($_GET['to'] ?? "");
/* ---------------------------
   Build WHERE Array
----------------------------*/
$where = [];

if ($userId > 0) {
    $where[] = "user_id = '$userId'";
}

if ($search !== "") {
    $s = mysqli_real_escape_string($conn, sql_like_escape($search));
    $where[] = "(title LIKE '%$s%' 
                OR location LIKE '%$s%' 
                OR ticket_no LIKE '%$s%')";
}

if ($category_id > 0) {
    $where[] = "category = '$category_id'";
}

if ($category !== "" && $category !== "all") {
  $cat = mysqli_real_escape_string($conn, $category);
  $where[] = "category = '$cat'";
}

if ($status !== "" && $status !== "all") {
  $st = mysqli_real_escape_string($conn, strtolower($status));
  if ($st === "in-progress" || $st === "in progress") {
    $where[] = "(LOWER(status)='in-progress' OR LOWER(status)='in progress')";
  } else {
    $where[] = "LOWER(status) = '$st'";
  }
}

if ($priority !== "" && $priority !== "all") {
  $pr = mysqli_real_escape_string($conn, strtolower($priority));
  $where[] = "LOWER(priority) = '$pr'";
}

if ($from !== "") {
  $f = mysqli_real_escape_string($conn, $from);
  $where[] = "DATE(reported_date) >= '$f'";
}
if ($to !== "") {
  $t = mysqli_real_escape_string($conn, $to);
  $where[] = "DATE(reported_date) <= '$t'";
}

/* ---------------------------------------------------------
   IMPORTANT: Build $whereSql BEFORE using it in getCount()
----------------------------------------------------------*/
$whereSql = "";
if (count($where) > 0) {
    $whereSql = " AND " . implode(" AND ", $where);
}

/* ---------------------------
   Stats (based on filters)
----------------------------*/
$total    = getCount($conn, $whereSql);
$pending  = getCount($conn, $whereSql, "AND LOWER(status)='pending'");
$progress = getCount($conn, $whereSql, "AND (LOWER(status)='in-progress' OR LOWER(status)='in progress')");
$resolved = getCount($conn, $whereSql, "AND LOWER(status)='resolved'");
$negative = getCount($conn, $whereSql, "AND user_confirmed=2");

/* ---------------------------
   Category list for dropdown
----------------------------*/
$catRes = mysqli_query($conn, "SELECT DISTINCT category_name FROM categories WHERE status=1 ORDER BY category_name ASC");
$catList = [];
if($catRes){
  while($c = mysqli_fetch_assoc($catRes)){
    $catList[] = $c['category_name'];
  }
}

/* ---------------------------
   Export CSV Logic
----------------------------*/
if(isset($_GET['export']) && $_GET['export'] === "1"){
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=issues_report.csv');

  $out = fopen('php://output', 'w');
  fputcsv($out, ['ID','Title','Category','Location','Priority','Status','Department','Reported Date']);

  $expQ = "SELECT id, title, category, location, priority, status, department, reported_date 
           FROM issues WHERE 1 $whereSql ORDER BY reported_date DESC";
  $expR = mysqli_query($conn, $expQ);
  if($expR){
    while($row = mysqli_fetch_assoc($expR)){
      fputcsv($out, [
        $row['id'], $row['title'], $row['category'], $row['location'], 
        $row['priority'], $row['status'], $row['department'], $row['reported_date']
      ]);
    }
  }
  fclose($out);
  exit();
}

/* ---------------------------
   Final Table Query
----------------------------*/
$listQ = "
SELECT issues.*, members.full_name 
FROM issues
LEFT JOIN members ON issues.user_id = members.id
WHERE 1 $whereSql 
ORDER BY reported_date DESC
";
$listQ = "
SELECT issues.*, categories.category_name 
FROM issues 
LEFT JOIN categories 
ON issues.category = categories.id
WHERE 1 $whereSql 
ORDER BY reported_date DESC
";
$listR = mysqli_query($conn, $listQ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Filters | UrbanSolve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
body{
    background:#f4f6f9;
    font-family:'Poppins',sans-serif;
}

/* Page header */
.page-title{
    font-weight:800;
    margin-bottom:2px;
}

.subtitle{
    color:#6c757d;
    margin:0;
}

/* Premium Cards */
.soft-card{
    border:0;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* KPI Cards */
.kpi-card{
    border:0;
    border-radius:18px;
    color:white;
    box-shadow:0 10px 25px rgba(0,0,0,0.12);
    transition:.25s;
}

.kpi-card:hover{
    transform:translateY(-3px);
}

.kpi-title{
    font-size:11px;
    letter-spacing:.6px;
    opacity:.85;
}

.kpi-value{
    font-size:26px;
    font-weight:700;
}

/* Filter Card */
.filter-card{
    border:0;
    border-radius:18px;
    box-shadow:0 8px 22px rgba(0,0,0,0.06);
}

/* Table */
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
    padding:.42rem .65rem;
    font-weight:600;
}

/* Buttons */
.btn-rounded{
    border-radius:12px;
}
.custom-table {
    border-radius: 12px;
    overflow: hidden;
}

.custom-table thead {
    font-size: 13px;
    text-transform: uppercase;
}

.custom-table tbody tr {
    transition: 0.2s ease;
}

.custom-table tbody tr:hover {
    background: #f1f5ff;
    transform: scale(1.01);
}

/* IMAGE STYLE */
.table-img {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #eee;
}

/* NO IMAGE */
.no-img {
    font-size: 11px;
    color: #999;
}
.zoomable-img {
    cursor: pointer;
    transition: 0.3s ease;
}

.zoomable-img:hover {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
.map-btn:hover{
    text-decoration: underline;
    color:#0d6efd;
}
    </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <?php include_once("includes/leftnav.php"); ?>

    <div class="col-lg-9 col-xl-10 main-content p-4">

      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
		  <h2 class="fw-bold text-primary">Reports & Filters</h2>
          <p class="subtitle">Filter issues and export reports</p>
          <?php if($userId > 0): ?>
            <span class="badge bg-info text-dark mt-2">Showing User #<?= $userId ?> <a href="reports.php" class="text-dark ms-2 text-decoration-none">ⓧ</a></span>
          <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary" href="reports.php"><i class="fa-solid fa-rotate-left me-1"></i> Reset</a>
          <a class="btn btn-primary" href="reports.php?<?php echo http_build_query(array_merge($_GET, ['export'=>'1'])); ?>">
            <i class="fa-solid fa-download me-1"></i> Export CSV
          </a>
        </div>
      </div>

<div class="row g-3 mb-4">

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#0d6efd,#4f9cff);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">TOTAL ISSUES</div>
          <div class="kpi-value"><?= $total ?></div>
        </div>
        <div><i class="fa-solid fa-layer-group fa-lg opacity-75"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#ffc107,#ffb300);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">PENDING</div>
          <div class="kpi-value"><?= $pending ?></div>
        </div>
        <div><i class="fa-solid fa-clock fa-lg opacity-75"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#0dcaf0,#31d2f2);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">IN PROGRESS</div>
          <div class="kpi-value"><?= $progress ?></div>
        </div>
        <div><i class="fa-solid fa-spinner fa-lg opacity-75"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#198754,#2bb673);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">RESOLVED</div>
          <div class="kpi-value"><?= $resolved ?></div>
        </div>
        <div><i class="fa-solid fa-check-circle fa-lg opacity-75"></i></div>
      </div>
    </div>
  </div>
</div>

      <div class="card shadow-sm">
        <div class="card-body">
          <form method="GET" class="row g-3">
            <input type="hidden" name="user_id" value="<?= $userId ?>">
            <div class="col-md-4">
              <label class="form-label small-muted">Search</label>
              <input type="text" class="form-control" name="search" placeholder="Keyword..." value="<?= esc($search) ?>">
			  
            </div>
            <div class="col-md-2">
              <label class="form-label small-muted">Category</label>
              <select class="form-select" name="category">
                <option value="all">All</option>
                <?php foreach($catList as $cName): ?>
                  <option value="<?= esc($cName) ?>" <?= ($category===$cName)?'selected':'' ?>><?= esc($cName) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="all">All</option>
                    <option value="pending" <?= ($status=='pending')?'selected':'' ?>>Pending</option>
                    <option value="in-progress" <?= ($status=='in-progress')?'selected':'' ?>>In Progress</option>
                    <option value="resolved" <?= ($status=='resolved')?'selected':'' ?>>Resolved</option>
                </select>
            </div>
			<div class="col-md-2">
			  <label class="form-label small-muted">Priority</label>
			  <select class="form-select" name="priority">
				<option value="all">All</option>
				<option value="low" <?= ($priority=='low')?'selected':'' ?>>Low</option>
				<option value="medium" <?= ($priority=='medium')?'selected':'' ?>>Medium</option>
				<option value="high" <?= ($priority=='high')?'selected':'' ?>>High</option>
			  </select>
			</div>

		<div class="col-md-2 d-grid">
          <button class="btn btn-primary btn-rounded"><i class="bi bi-search me-1"></i> Filter</button>
          <a href="reports.php" class="btn btn-light btn-rounded mt-2"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
        </div>
          </form>
        </div>
      </div>

      <div class="card shadow-lg border-0 rounded-4">
  <div class="card-body table-responsive">

    <table id="reportTable" class="table align-middle custom-table">
      <thead class="table-dark">
        <tr>
          <th>Sr No</th>
          <th>Issue</th>
          <th>Ticket</th>
          <th>Image</th>
          <th>Location</th>
          <th>Category</th>
          <th>Priority</th>
          <th>Status</th>
          <th>User Feedback</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
      <?php if($listR && mysqli_num_rows($listR) > 0): $sr=1; while($row = mysqli_fetch_assoc($listR)): 

        $p = strtolower($row['priority'] ?? '');
        $s = strtolower($row['status'] ?? '');

        $pBadge = ($p==='high') ? 'danger' : (($p==='medium') ? 'warning' : 'success');
        $sBadge = ($s==='resolved') ? 'success' : (($s==='in-progress' || $s==='in progress') ? 'info' : 'secondary');

      ?>
        <tr>

          <td><?= $sr++ ?></td>

          <!-- ISSUE -->
          <td>
            <strong><?= esc($row['title']) ?></strong><br>

              <small class="text-muted">
                👤 <?= esc($row['full_name'] ?? 'Unknown') ?>
              </small><br>

              <small class="text-muted">
                🆔 User <?= $row['user_id'] ?>
              </small>
          </td>

          <!-- TICKET -->
          <td>
            <span class="badge bg-primary">
              <?= esc($row['ticket_no']) ?>
            </span>
          </td>

          <!-- IMAGE -->
          <td>
            <?php if(!empty($row['image'])): ?>
                <img 
                    src="../uploads/<?= $row['image'] ?>" 
                    class="table-img zoomable-img"
                    data-img="../uploads/<?= $row['image'] ?>"
                    alt="issue">
            <?php else: ?>
                <div class="no-img">No Image</div>
            <?php endif; ?>
            </td>

          <!-- LOCATION -->
          <td>
            <span class="map-btn text-primary"
                style="cursor:pointer; font-weight:600;"
                data-lat="<?= $row['latitude'] ?>"
                data-lng="<?= $row['longitude'] ?>"
                data-location="<?= htmlspecialchars($row['location']) ?>">
                
                <i class="fa-solid fa-location-dot text-danger"></i>
                <?= htmlspecialchars($row['location']) ?>
            </span>
        </td>

          <!-- CATEGORY -->
          <td><?= esc($row['category_name'] ?? 'N/A') ?></td>

          <!-- PRIORITY -->
          <td>
            <span class="badge bg-<?= $pBadge ?>">
              <?= ucfirst($p ?: 'Low') ?>
            </span>
          </td>

          <!-- STATUS -->
          <td>
            <span class="badge bg-<?= $sBadge ?>">
              <?= ucfirst($s ?: 'Pending') ?>
            </span>
          </td>

          <!-- USER FEEDBACK -->
          <td>
            <?php
$confirm = (int)($row['user_confirmed'] ?? 0);

if($confirm === 1){
    echo '<span class="badge bg-success">Satisfied</span>';
}
elseif($confirm === 2){
    echo '<span class="badge bg-danger">Not Solved</span>';

    if(!empty($row['user_feedback'])){
        echo '<br><small class="text-muted">'.htmlspecialchars($row['user_feedback']).'</small>';
    }
}
else{
    echo '<span class="text-muted">Waiting for user</span>';
}
?>
          </td>

          <!-- DATE -->
          <td><?= date("d M Y", strtotime($row['reported_date'])) ?></td>

          <!-- ACTION -->
          <td>
            <button class="btn btn-sm btn-dark rounded-pill"
                data-bs-toggle="modal"
                data-bs-target="#statusModal"
                data-id="<?= $row['id'] ?>"
                data-status="<?= $row['status'] ?>">
                Update
            </button>
          </td>

        </tr>

      <?php endwhile; else: ?>
        <tr>
          <td colspan="11" class="text-center py-4">
            No data found
          </td>
        </tr>
      <?php endif; ?>
      </tbody>

    </table>
  </div>
</div>
			  <?php include_once("includes/footer.php"); ?>

    </div>
  </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="update_issue_status.php">
      <div class="modal-header"><h5 class="modal-title">Update Status</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" name="id" id="m_issue_id">
        <div class="mb-3">
          <label class="form-label">New Status</label>
          <select class="form-select" name="status" id="m_status">
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
    </form>
  </div>
</div>
<div class="modal fade" id="detailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Issue Details</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="detailsContent">
        <div class="text-center py-4">
          <div class="spinner-border"></div>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- IMAGE ZOOM MODAL -->
<div class="modal fade" id="imageZoomModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark border-0">

      <div class="modal-body text-center p-2">
        <img id="zoomedImage" src="" style="max-width:100%; border-radius:10px;">
      </div>

    </div>
  </div>
</div>

  <!-- MAP MODAL -->
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Location Map</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">

        <!-- MAP FRAME -->
        <iframe id="mapFrame"
            width="100%"
            height="400"
            style="border:0;"
            loading="lazy">
        </iframe>

      </div>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function(){
    $('#reportTable').DataTable();
    
    var statusModal = document.getElementById('statusModal');
    statusModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('m_issue_id').value = button.getAttribute('data-id');
        document.getElementById('m_status').value = button.getAttribute('data-status');
    });
});
document.querySelectorAll(".viewDetailsBtn").forEach(btn => {
    btn.addEventListener("click", function(){

        let id = this.dataset.id;

        let modal = new bootstrap.Modal(document.getElementById("detailsModal"));
        modal.show();

        fetch("admin_issue_details.php?id=" + id)
        .then(res => res.text())
        .then(data => {
            document.getElementById("detailsContent").innerHTML = data;
        });

    });
});
document.addEventListener("click", function(e){
    if(e.target.classList.contains("reopenBtn")){

        let id = e.target.dataset.id;

        fetch("reopen_issue.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id
        })
        .then(() => {
            alert("Issue Reopened!");
            location.reload();
        });
    }
});

      // IMAGE ZOOM CLICK

document.querySelectorAll(".zoomable-img").forEach(img => {

    img.addEventListener("click", function(){

        let src = this.getAttribute("data-img");

        document.getElementById("zoomedImage").src = src;

        let modal = new bootstrap.Modal(
            document.getElementById("imageZoomModal")
        );

        modal.show();
    });

});

            // MAP MODEL //

document.querySelectorAll(".map-btn").forEach(el => {

    el.addEventListener("click", function(){

        let lat = this.dataset.lat;
        let lng = this.dataset.lng;
        let location = this.dataset.location;

        let mapUrl = "";

        // ✅ If coordinates available
        if(lat && lng && lat !== "NULL"){
            mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
        }
        // ❌ fallback to location text
        else{
            mapUrl = `https://maps.google.com/maps?q=${encodeURIComponent(location)}&output=embed`;
        }

        document.getElementById("mapFrame").src = mapUrl;

        let modal = new bootstrap.Modal(
            document.getElementById("mapModal")
        );

        modal.show();
    });

});
</script>
</body>
</html>