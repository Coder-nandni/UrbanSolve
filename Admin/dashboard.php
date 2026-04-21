<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: login.php");
  exit();
}

require_once 'includes/auth.php';
include_once 'includes/db.php';
include_once 'includes/status_helpers.php';
include_once 'includes/priority_helpers.php';

$admin_id = $_SESSION['admin_id'];
$admin_email = $_SESSION['admin_email'];

$login_time = isset($_SESSION['login_time']) ? (int)$_SESSION['login_time'] : time();
$_SESSION['login_time'] = $login_time;

// ---------- helpers ----------
function esc($v){ return htmlspecialchars($v ?? "", ENT_QUOTES, "UTF-8"); }

function hasColumn($conn, $table, $col){
  $col = mysqli_real_escape_string($conn, $col);
  $table = mysqli_real_escape_string($conn, $table);
  $q = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$col'");
  return ($q && mysqli_num_rows($q) > 0);
}

$hasCreatedAt = hasColumn($conn, "issues", "created_at");
$hasUserId    = hasColumn($conn, "issues", "user_id");

// lat/lng detect
$hasLat = hasColumn($conn, "issues", "latitude");
$hasLng = hasColumn($conn, "issues", "longitude");

// members table detect
$membersTable = "members";
$membersExists = true;
$testMembers = mysqli_query($conn, "SHOW TABLES LIKE '$membersTable'");
if(!$testMembers || mysqli_num_rows($testMembers) === 0) $membersExists = false;

$hasMemberName  = $membersExists ? hasColumn($conn, $membersTable, "full_name") : false;
$hasMemberImage = $membersExists ? (hasColumn($conn, $membersTable, "image") || hasColumn($conn, $membersTable, "photo")) : false;
$memberImageCol = $hasMemberImage ? (hasColumn($conn, $membersTable, "image") ? "image" : "photo") : "";

// ---------- counts / KPIs ----------
$totalIssuesRow = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM issues"));
$totalIssues = $totalIssuesRow ? (int)$totalIssuesRow['t'] : 0;

$pendingRow = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM issues WHERE status='Pending'"));
$pendingIssues = $pendingRow ? (int)$pendingRow['c'] : 0;

$inprogRow = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM issues WHERE status='In Progress'"));
$inProgressIssues = $inprogRow ? (int)$inprogRow['c'] : 0;

$resolvedRow = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM issues WHERE status='Resolved'"));
$resolvedIssues = $resolvedRow ? (int)$resolvedRow['c'] : 0;

$resolutionRate = ($totalIssues > 0) ? round(($resolvedIssues / $totalIssues) * 100) : 0;



// ---------- category cards (optional) ----------
$cardQ = mysqli_query($conn, "
  SELECT c.category_name, COUNT(i.id) as total
  FROM categories c
  LEFT JOIN issues i ON i.category = c.category_name
  GROUP BY c.id
");

// ---------- Filters ----------
$search     = isset($_GET['search']) ? trim($_GET['search']) : '';
$f_status   = isset($_GET['status']) ? trim($_GET['status']) : '';
$f_priority = isset($_GET['priority']) ? trim($_GET['priority']) : '';

$where = "WHERE 1";
if ($search !== '') {
  $s = mysqli_real_escape_string($conn, $search);
  $where .= " AND (i.title LIKE '%$s%' OR i.location LIKE '%$s%' OR i.id LIKE '%$s%')";
}
if ($f_status !== '') {
  $st = mysqli_real_escape_string($conn, $f_status);
  $where .= " AND i.status='$st'";
}
if ($f_priority !== '') {
  $pr = mysqli_real_escape_string($conn, $f_priority);
  $where .= " AND i.priority='$pr'";
}

$orderBy = $hasCreatedAt ? "ORDER BY i.created_at DESC" : "ORDER BY i.id DESC";

// ---------- Active issues list (Top 6) with user name/photo ----------
$selectUser = "";
$joinUser   = "";

if($hasUserId && $membersExists){
  $selectUser .= $hasMemberName ? ", m.full_name as reporter_name" : ", '' as reporter_name";
  $selectUser .= $hasMemberImage ? ", m.`$memberImageCol` as reporter_image" : ", '' as reporter_image";
  $joinUser = "LEFT JOIN `$membersTable` m ON m.id = i.user_id";
} else {
  $selectUser .= ", '' as reporter_name, '' as reporter_image";
}

$selectLatLng = "";
$selectLatLng .= $hasLat ? ", i.latitude" : ", NULL as latitude";
$selectLatLng .= $hasLng ? ", i.longitude" : ", NULL as longitude";

$sqlIssues = "SELECT 
                i.id,
                i.ticket_no,
                i.title,
                i.location,
                i.priority,
                i.status,
                i.reported_date,
                i.image"
          . $selectLatLng
          . $selectUser
          . " FROM issues i
              $joinUser
              $where
              ORDER BY i.id DESC
              LIMIT 6";

$issues = mysqli_query($conn, $sqlIssues);
// --------Map Locations-------
$mapData = [];
$qMap = mysqli_query($conn,"
  SELECT id, title, latitude, longitude, priority, status
  FROM issues
  WHERE latitude IS NOT NULL AND longitude IS NOT NULL
");

if($qMap){
  while($r=mysqli_fetch_assoc($qMap)){
    $mapData[] = $r;
  }
}

// ---------- Top Locations ----------
$qTopLoc = mysqli_query($conn, "
  SELECT location, COUNT(*) as c
  FROM issues
  WHERE location IS NOT NULL AND location != ''
  GROUP BY location
  ORDER BY c DESC
  LIMIT 5
");

// ---------- Chart Data ----------
$qCat = mysqli_query($conn, "SELECT category, COUNT(*) as c FROM issues GROUP BY category");
$catLabels = [];
$catCounts = [];
if($qCat){
  while($r = mysqli_fetch_assoc($qCat)){
    $catLabels[] = $r['category'] ?: "Unknown";
    $catCounts[] = (int)$r['c'];
  }
}

$qStatus = mysqli_query($conn, "SELECT status, COUNT(*) as c FROM issues GROUP BY status");
$statusLabels = [];
$statusCounts = [];
if($qStatus){
  while($r = mysqli_fetch_assoc($qStatus)){
    $statusLabels[] = $r['status'] ?: "Unknown";
    $statusCounts[] = (int)$r['c'];
  }
}

// monthly trend (only if created_at exists)
$monthLabels = [];
$monthCounts = [];
if($hasCreatedAt){
  $qTrend = mysqli_query($conn, "
    SELECT DATE_FORMAT(created_at, '%b %Y') as m, COUNT(*) as c
    FROM issues
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
  ");
  if($qTrend){
    while($r=mysqli_fetch_assoc($qTrend)){
      $monthLabels[] = $r['m'];
      $monthCounts[] = (int)$r['c'];
    }
  }
}

// Priority badge mapper
function priorityBadge($p){
  $p = trim((string)$p);
  $u = strtoupper($p);
  if($u === 'HIGH' || $u === 'H' || $p === 'High') return ['danger', 'High'];
  if($u === 'MEDIUM' || $u === 'M' || $p === 'Medium') return ['warning', 'Medium'];
  if($u === 'LOW' || $u === 'L' || $p === 'Low') return ['success', 'Low'];
  return ['secondary', $p ?: 'N/A'];
}

$qUnread = mysqli_query($conn,
    "SELECT COUNT(*) as c FROM issues WHERE is_read = 0"
);

$unreadCount = ($qUnread)
    ? (int)(mysqli_fetch_assoc($qUnread)['c'] ?? 0)
    : 0;
  $qUnsatisfied = mysqli_query($conn,"
  SELECT COUNT(*) as c 
  FROM issues 
  WHERE user_confirmed = 2
");

$unsatisfiedCount = ($qUnsatisfied)
  ? (int)(mysqli_fetch_assoc($qUnsatisfied)['c'] ?? 0)
  : 0;
?>



<!-- ✅ UI: Fonts/Icons/Chart -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<style>
  body{ font-family:'Poppins',sans-serif; background:#f4f6f9; }
  .main-content{ padding:22px 18px; }

  .topbar{
    background:#fff;
    border-radius:16px;
    box-shadow:0 10px 26px rgba(16,24,40,.08);
    padding:14px 16px;
    margin-bottom:16px;
  }

  .kpi-card{
    border:0;
    border-radius:18px;
    color:#fff;
    overflow:hidden;
    position:relative;
    box-shadow:0 10px 26px rgba(16,24,40,.12);
  }
  .kpi-card .card-body{ padding:18px; }
  .kpi-icon{
    position:absolute; right:16px; top:14px;
    font-size:34px; opacity:.18;
  }
  .kpi-title{ opacity:.9; font-size:13px; margin:0; }
  .kpi-value{ font-size:28px; font-weight:700; margin:6px 0 0; }

  .soft-card{
    border:0;
    border-radius:18px;
    box-shadow:0 10px 26px rgba(16,24,40,.08);
  }
  .soft-card .card-header{ background:#fff; border:0; border-radius:18px 18px 0 0; }

  .badge-pill{ border-radius:999px; padding:.42rem .6rem; font-weight:600; }
  .table thead th{ font-size:13px; }
  .table tbody td{ vertical-align:middle; }
  .table-hover tbody tr:hover{ background:#f7f9ff; }

  .mini-list li{ padding:10px 0; border-bottom:1px solid #eef2f7; }
  .mini-list li:last-child{ border-bottom:0; }

  .btn-rounded{ border-radius:12px; }

  .issue-img{
  width:55px;
  height:55px;
  object-fit:cover;
  border-radius:10px;
  cursor:pointer;
  transition:.3s;
}
.issue-img:hover{
  transform:scale(1.1);
  box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

#modalImg{
  transition: transform .3s ease;
}
#modalImg:hover{
  transform: scale(1.05);
}

</style>
<?php include_once("includes/leftnav.php"); ?>
<div class="col-lg-9 col-xl-10 main-content">

  <!-- ✅ TOP BAR (Header + Bell + Admin) -->
  <div class="topbar d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <h4 class="fw-bold text-primary mb-0">Urban Problem Dashboard</h4>
      <small class="text-muted">Monitor and manage city-wide issues & complaints</small>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="export_report.php" target="_blank" class="btn btn-outline-dark btn-rounded">
        <i class="bi bi-printer me-2"></i> Export / Print
      </a>

      <a href="add_issue.php" class="btn btn-primary btn-rounded">
        <i class="bi bi-plus-circle me-2"></i> Report New Issue
      </a>

      <button type="button"
        class="btn btn-light btn-rounded position-relative"
        data-bs-toggle="modal"
        data-bs-target="#notificationModal">

    <i class="bi bi-bell"></i>

    <span id="notifBadge"
      class="badge rounded-pill bg-danger"
      style="<?php echo ($unreadCount ? '' : 'display:none;'); ?>">
    <?php echo $unreadCount; ?>
</span>

</button>

    </div>
  </div>
  <?php if($unsatisfiedCount > 0): ?>
<div class="alert alert-danger d-flex justify-content-between align-items-center mb-3 shadow-sm rounded-3">
  <div>
    <i class="bi bi-emoji-frown me-2"></i>
    <strong><?php echo $unsatisfiedCount; ?> users are NOT satisfied</strong><br>
    <small>Issues reopened or rejected by users</small>
  </div>
  <a href="reports.php?user_confirmed=2" class="btn btn-light btn-sm btn-rounded">
    View Issues
  </a>
</div>
<?php endif; ?>

  <!-- ✅ KPI CARDS -->
  <div class="row g-3 mb-3">
    <div class="col-md-6 col-lg-3">
      <div class="card kpi-card" style="background:linear-gradient(135deg,#0d6efd,#4f9cff);">
        <div class="card-body">
          <i class="bi bi-list-task kpi-icon"></i>
          <p class="kpi-title">Total Issues</p>
          <div class="kpi-value"><?php echo (int)$totalIssues; ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card kpi-card" style="background:linear-gradient(135deg,#ffc107,#ffb300);">
        <div class="card-body">
          <i class="bi bi-hourglass-split kpi-icon"></i>
          <p class="kpi-title">Pending</p>
          <div class="kpi-value"><?php echo (int)$pendingIssues; ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card kpi-card" style="background:linear-gradient(135deg,#0dcaf0,#00a3c4);">
        <div class="card-body">
          <i class="bi bi-arrow-repeat kpi-icon"></i>
          <p class="kpi-title">In Progress</p>
          <div class="kpi-value"><?php echo (int)$inProgressIssues; ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card kpi-card" style="background:linear-gradient(135deg,#198754,#2bb673);">
        <div class="card-body">
          <i class="bi bi-check2-circle kpi-icon"></i>
          <p class="kpi-title">Resolved</p>
          <div class="kpi-value"><?php echo (int)$resolvedIssues; ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ FILTER BAR -->
  <div class="card soft-card mb-3">
    <div class="card-body">
      <form class="row g-2 align-items-center" method="GET">
        <div class="col-md-5">
          <input type="text" name="search" class="form-control"
                 placeholder="Search title / location / id..."
                 value="<?php echo esc($search); ?>">
        </div>

        <div class="col-md-3">
          <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="Pending" <?php if($f_status==='Pending') echo 'selected'; ?>>Pending</option>
            <option value="In Progress" <?php if($f_status==='In Progress') echo 'selected'; ?>>In Progress</option>
            <option value="Resolved" <?php if($f_status==='Resolved') echo 'selected'; ?>>Resolved</option>
          </select>
        </div>

        <div class="col-md-2">
          <select name="priority" class="form-select">
            <option value="">All Priority</option>
            <option value="HIGH" <?php if($f_priority==='HIGH') echo 'selected'; ?>>HIGH</option>
            <option value="MEDIUM" <?php if($f_priority==='MEDIUM') echo 'selected'; ?>>MEDIUM</option>
            <option value="LOW" <?php if($f_priority==='LOW') echo 'selected'; ?>>LOW</option>
          </select>
        </div>

        <div class="col-md-2 d-grid">
          <button class="btn btn-primary btn-rounded"><i class="bi bi-search me-1"></i> Filter</button>
          <a href="dashboard.php" class="btn btn-light btn-rounded mt-2"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- ✅ RECENT ISSUES TABLE -->
  <div class="card soft-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0 text-primary">
        <i class="bi bi-exclamation-triangle me-2"></i> Recent Issues
      </h5>
      <a href="reports.php" class="btn btn-sm btn-outline-primary btn-rounded">View All</a>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive" style="max-height:70vh; overflow:auto;">
        <table class="table table-hover mb-0 align-middle" id="issuesTable">
          <thead class="table-light">
            <tr>
              <th style="width:60px;">Sr.No</th>
              <th>Issue</th>
              <th style="width:180px;">Reporter</th>
              <th style="width:200px;">Location</th>
              <th style="width:200px;">Image</th>
              <th style="width:170px;">Reported</th>
              <th style="width:110px;">Priority</th>
              <th style="width:130px;">Status</th>
              <th style="width:130px;">Action</th>
            </tr>
          </thead>

          <tbody>
          <?php $sn = 1; ?>
          <?php if($issues && mysqli_num_rows($issues)>0): ?>
            <?php while($row = mysqli_fetch_assoc($issues)): ?>
              <?php [$pClass, $pText] = priorityBadge($row['priority']); ?>
              <tr>
                <td class="fw-semibold"><?php echo $sn++; ?></td>
                <td>
  <strong><?php echo esc($row['title']); ?></strong><br>
  <small class="text-muted">Ticket: <?php echo esc($row['ticket_no']); ?></small>
</td>

                <td>
                  <div class="d-flex align-items-center gap-2">
                    <?php if(!empty($row['reporter_image'])): ?>
                      <img src="../uploads/<?php echo esc($row['reporter_image']); ?>"
                           style="width:34px;height:34px;border-radius:50%;object-fit:cover;"
                           onerror="this.style.display='none';">
                    <?php endif; ?>
                    <div>
                      <div class="fw-semibold">
                        <?php echo !empty($row['reporter_name']) ? esc($row['reporter_name']) : "Citizen"; ?>
                      </div>
                      <small class="text-muted">Issue ID: <?php echo (int)$row['id']; ?></small>
                    </div>
                  </div>
                </td>

                <td>
                  <?php
                    $locText = trim((string)$row['location']);
                    $lat = isset($row['latitude']) ? trim((string)$row['latitude']) : '';
                    $lng = isset($row['longitude']) ? trim((string)$row['longitude']) : '';
                    $btnLabel = ($locText !== '') ? $locText : "Open Map";
                  ?>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary btn-rounded"
                    data-bs-toggle="modal"
                    data-bs-target="#mapModal"
                    data-location="<?php echo esc($locText); ?>"
                    data-lat="<?php echo esc($lat); ?>"
                    data-lng="<?php echo esc($lng); ?>"
                  >
                    <i class="bi bi-geo-alt me-1"></i> <?php echo esc($btnLabel); ?>
                  </button>
                </td>
                <td>
<?php if(!empty($row['image'])): ?>
  <img src="../uploads/<?php echo esc($row['image']); ?>"
       class="issue-img"
       data-img="../uploads/<?php echo esc($row['image']); ?>">
<?php else: ?>
  <span class="text-muted">No Image</span>
<?php endif; ?>
</td>

<td>
  <?php if(!empty($row['reported_date'])): ?>
    <?php echo date("d M Y", strtotime($row['reported_date'])); ?>
    <br>
    <small class="text-muted">
      <?php echo date("h:i A", strtotime($row['reported_date'])); ?>
    </small>
  <?php else: ?>
    <span class="text-muted">—</span>
  <?php endif; ?>
</td>

                <td>
                  <span class="badge bg-<?php echo $pClass; ?> badge-pill"><?php echo esc($pText); ?></span>
                </td>

                <td>
                  <span class="badge <?php echo esc(statusBadgeClass($row['status'])); ?> badge-pill">
                    <?php echo esc($row['status']); ?>
                  </span>
                </td>

                <td>
                  <button class="btn btn-sm btn-outline-primary btn-rounded viewIssueBtn"
                          data-id="<?php echo $row['id']; ?>">
                    <i class="bi bi-eye me-1"></i> View
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="9" class="text-center py-4 text-muted">No issues found.</td></tr>
          <?php endif; ?>
          </tbody>

        </table>
      </div>
    </div>
  </div>
	  <?php include_once("includes/footer.php"); ?>

</div>

<!-- ✅ ISSUE MODAL -->
<div class="modal fade" id="issueModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Issue Overview</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="issueModalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="notificationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="bi bi-bell me-2"></i> Notifications
        </h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
       <p class="text-muted mb-2">Unread Issues</p>

<div class="alert alert-info">
  <strong><?php echo (int)$unreadCount; ?></strong> unread issues.
</div>

        <a href="reports.php" class="btn btn-primary w-100 btn-rounded">
          View All Issues
        </a>
      </div>

    </div>
  </div>
</div>
<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">📍 Issue Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="modalMap" style="height:400px;border-radius:10px;"></div>
      </div>

    </div>
  </div>
</div>
<!-- Image Zoom Modal -->
<div class="modal fade" id="imgModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-body text-center p-0">
        <img id="modalImg" src="" style="width:100%;border-radius:10px;">
      </div>
    </div>
  </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Chart Data from PHP
const catLabels = <?php echo json_encode($catLabels); ?>;
const catCounts = <?php echo json_encode($catCounts); ?>;

const statusLabels = <?php echo json_encode($statusLabels); ?>;
const statusCounts = <?php echo json_encode($statusCounts); ?>;

const trendLabels = <?php echo json_encode($monthLabels); ?>;
const trendCounts = <?php echo json_encode($monthCounts); ?>;

// ✅ Category Chart
const ctxCat = document.getElementById('categoryChart');
if (ctxCat) {
  new Chart(ctxCat, {
    type: 'bar',
    data: {
      labels: catLabels,
      datasets: [{ label: 'Issues', data: catCounts }]
    },
    options: {
      responsive:true,
      plugins:{ legend:{ display:false } },
      scales:{ y:{ beginAtZero:true } }
    }
  });
}

// ✅ Status Chart
const ctxStatus = document.getElementById('statusChart');
if (ctxStatus) {
  new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
      labels: statusLabels,
      datasets: [{ data: statusCounts }]
    },
    options: { responsive:true, plugins:{ legend:{ position:'bottom' } } }
  });
}

// ✅ Trend Chart
const ctxTrend = document.getElementById('trendChart');
if (ctxTrend && trendLabels.length) {
  new Chart(ctxTrend, {
    type: 'line',
    data: {
      labels: trendLabels,
      datasets: [{ label:'Issues', data: trendCounts, tension:0.35 }]
    },
    options: {
      responsive:true,
      plugins:{ legend:{ display:false } },
      scales:{ y:{ beginAtZero:true } }
    }
  });
}

// view Map
let modalMap;
let marker;
let circle;

const mapModal = document.getElementById('mapModal');

mapModal.addEventListener('shown.bs.modal', function (event) {

  const button = event.relatedTarget;

  const lat = parseFloat(button.getAttribute('data-lat'));
  const lng = parseFloat(button.getAttribute('data-lng'));
  const locationText = button.getAttribute('data-location');

  // 🧹 Reset old map (VERY IMPORTANT)
  if (modalMap) {
    modalMap.remove();
    modalMap = null;
  }

  // 🎯 Default fallback (Ludhiana)
  let finalLat = (!isNaN(lat)) ? lat : 30.900965;
  let finalLng = (!isNaN(lng)) ? lng : 75.857277;

  // 🗺️ Create Map
  modalMap = L.map('modalMap').setView([finalLat, finalLng], 16);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
  }).addTo(modalMap);

  // 📍 Marker
  marker = L.marker([finalLat, finalLng]).addTo(modalMap);

  // 🟢 Area Highlight (NEW 🔥)
  circle = L.circle([finalLat, finalLng], {
    color: 'blue',
    fillColor: '#0d6efd',
    fillOpacity: 0.25,
    radius: 200   // 👉 200 meters highlight
  }).addTo(modalMap);

  // 📌 Popup
  const popupText = locationText ? `<b>${locationText}</b>` : "Issue Location";

  marker.bindPopup(popupText).openPopup();
  circle.bindPopup(popupText);
	 marker.on('click', function(){
    window.open(`https://www.google.com/maps?q=${finalLat},${finalLng}`, '_blank');
  });
  // 🔧 Fix blank map issue (IMPORTANT)
  setTimeout(() => {
    modalMap.invalidateSize();
  }, 300);

});

       // View Issue ajax
$(document).on("click", ".viewIssueBtn", function(){

    let id = $(this).data("id");

    console.log("Clicked ID:", id); // 👈 check

    // loader show
    $("#issueModalBody").html(
        '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>'
    );

    // modal open (IMPORTANT 🔥)
   let modal = new bootstrap.Modal(document.getElementById('issueModal'));
modal.show();

    $.ajax({
        url: "view_issue_ajax.php",
        type: "GET",
        data: { id: id },

        success: function(response){
            console.log("SUCCESS:", response);
            $("#issueModalBody").html(response);
        },

        error: function(xhr){
            console.log("ERROR:", xhr.responseText);
            $("#issueModalBody").html(
                '<div class="text-danger text-center">Failed to load data</div>'
            );
        }
    });

});
//✅  REALTIME BELL NOTIFICATIONS (Polling)
let notifPolling = setInterval(updateNotif, 5000);

function updateNotif(){
  fetch("check_notifications.php")
    .then(res => res.text())
    .then(count => {
      const badge = document.getElementById("notifBadge");
      if(!badge) return;
		
      count = parseInt(count, 10) || 0;

      if(count > 0){
        badge.innerText = count;
        badge.style.display = "inline-block";
      } else {
        badge.style.display = "none";
      }
    });
}
updateNotif();

// Modal open -> stop polling + mark read
const notifModal = document.getElementById('notificationModal');
if(notifModal){
  notifModal.addEventListener('shown.bs.modal', function () {
    clearInterval(notifPolling);

    fetch("mark_notifications_read.php")
      .then(() => {
        const badge = document.getElementById("notifBadge");
        if(badge){
          badge.style.display = "none";
          badge.innerText = "0";
        }
      });
  });

  // Modal close -> resume polling
  notifModal.addEventListener('hidden.bs.modal', function () {
    updateNotif();
    notifPolling = setInterval(updateNotif, 5000);
  });
}

const issues = <?php echo json_encode($mapData); ?>;

if(document.getElementById("issueMap")){
  const map = L.map('issueMap').setView([22.9734, 78.6569], 5); // India Center

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  issues.forEach(issue => {

    if(issue.latitude && issue.longitude){

      let color = "blue";

const p = (issue.priority || "").toUpperCase();

if(p === "HIGH") color = "red";
if(p === "MEDIUM") color = "orange";
if(p === "LOW") color = "green";

      if(issue.priority === "LOW") color = "green";

      const marker = L.circleMarker(
        [issue.latitude, issue.longitude],
        {
          radius: 8,
          fillColor: color,
          color: "#fff",
          weight: 2,
          fillOpacity: 0.9
        }
      ).addTo(map);

      marker.bindPopup(`
        <strong>${issue.title}</strong><br>
        Issue ID: ${issue.id}<br>
        Priority: ${issue.priority}<br>
        Status: ${issue.status}
      `);
    }
  });
}

$(document).on("click", ".issue-img", function(){
  let src = $(this).data("img");
  $("#modalImg").attr("src", src);
  new bootstrap.Modal(document.getElementById("imgModal")).show();
});

</script>

</body>
</html>
