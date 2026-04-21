<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

include("db.php");
include_once 'includes/status_helpers.php';
include_once 'includes/priority_helpers.php';

$search   = mysqli_real_escape_string($conn, trim($_GET['search'] ?? ''));
$f_cat    = mysqli_real_escape_string($conn, trim($_GET['category'] ?? ''));
$f_status = mysqli_real_escape_string($conn, trim($_GET['status'] ?? ''));
$f_pri    = mysqli_real_escape_string($conn, trim($_GET['priority'] ?? ''));

$where = "WHERE 1=1";
if($search !== ''){
  $where .= " AND (title LIKE '%$search%' OR location LIKE '%$search%' OR issue_code LIKE '%$search%')";
}
if($f_cat !== '') $where .= " AND category='$f_cat'";
if($f_status !== '') $where .= " AND status='$f_status'";
if($f_pri !== '') $where .= " AND priority='$f_pri'";

$issuesQ = mysqli_query($conn, "SELECT * FROM issues $where ORDER BY id DESC");

// filters dropdown data
$catQ = mysqli_query($conn, "SELECT category_name FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Issues | Admin</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

  <style>
    .card{ border-radius:18px; }
    .table td, .table th{ padding:14px; }
    .table-hover tbody tr:hover{ background:#f8fafc; }
    .thumb{ width:46px; height:46px; border-radius:10px; object-fit:cover; border:1px solid #eee; }
    .loc-btn{ text-decoration:none; }
  </style>
</head>
<body>

<?php include_once("includes/leftnav.php"); ?>

<div class="col-lg-9 col-xl-10 main-content">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-primary mb-1">All Issues</h2>
      <p class="text-muted mb-0">Search, filter, update status & view map</p>
    </div>
    <a class="btn btn-primary" href="add_issue.php">
      <i class="fa-solid fa-plus me-2"></i> Report New Issue
    </a>
  </div>

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <form class="row g-2" method="GET">
        <div class="col-md-4">
          <input class="form-control" name="search" placeholder="Search title / location / code..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        </div>

        <div class="col-md-3">
		<select class="form-select" name="category">
    <option value="">All Categories</option>

    <?php while($row = mysqli_fetch_assoc($catQ)){ ?>
        <option value="<?= htmlspecialchars($row['category_name']) ?>"
            <?= (($_GET['category'] ?? '') == $row['category_name']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['category_name']) ?>
        </option>
    <?php } ?>

</select>


        </div>

        <div class="col-md-2">
          <select class="form-select" name="status">
            <option value="">All Status</option>
            <?php foreach(["Pending","In Progress","Resolved"] as $st): ?>
              <option value="<?php echo $st; ?>" <?php if(($_GET['status'] ?? '')===$st) echo 'selected'; ?>><?php echo $st; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-2">
          <select class="form-select" name="priority">
            <option value="">All Priority</option>
            <?php foreach(["HIGH","MEDIUM","LOW"] as $p): ?>
              <option value="<?php echo $p; ?>" <?php if(($_GET['priority'] ?? '')===$p) echo 'selected'; ?>><?php echo $p; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-1 d-grid">
          <button class="btn btn-outline-primary"><i class="fa-solid fa-filter me-1"></i></button>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fa-solid fa-triangle-exclamation me-2"></i> Issues List</h5>
      <small class="text-muted">Click location to open map</small>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="issuesTable">
          <thead class="table-light">
            <tr>
			  <th>S No</th>
              <th>Title</th>
              <th>Category</th>
              <th>Location</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Image</th>
              <th style="width:210px;">Action</th>
            </tr>
          </thead>
		<?php $sn = 1; ?>
          <tbody>
          <?php if($issuesQ && mysqli_num_rows($issuesQ)>0): ?>
            <?php while($r=mysqli_fetch_assoc($issuesQ)): ?>
				<?php
				  $img = trim((string)($r['image'] ?? ''));
				?>

              <tr>
				<td class="fw-semibold"><?php echo $sn++; ?></td>
                <td><?php echo htmlspecialchars($r['title']); ?></td>
                <td><?php echo htmlspecialchars($r['category']); ?></td>

                <td>
                  <a href="javascript:void(0)"
                     class="loc-btn"
                     data-bs-toggle="modal"
                     data-bs-target="#mapModal"
                     data-location="<?php echo htmlspecialchars($r['location']); ?>"
                     data-lat="<?php echo htmlspecialchars($r['latitude']); ?>"
                     data-lng="<?php echo htmlspecialchars($r['longitude']); ?>">
                    <i class="fa-solid fa-location-dot me-1"></i>
                    <?php echo htmlspecialchars($r['location']); ?>
                  </a>
                </td>

                <td>
                  <span class="badge bg-<?php echo ($r['priority']=='High')?'danger':(($r['priority']=='Medium')?'warning':'success'); ?>">
                    <?php echo htmlspecialchars($r['priority']); ?>
                  </span>
                </td>
				
                <td>
                  <span class="badge bg-info"><?php echo htmlspecialchars($r['status']); ?></span>
                </td>

                <td>
                  <?php if($img !== ''): ?>
                    <img class="thumb"
                         src="../uploads/<?php echo htmlspecialchars($img); ?>"
                         alt="image"
                         role="button"
                         data-bs-toggle="modal"
                         data-bs-target="#imageModal"
                         data-img="../uploads/<?php echo htmlspecialchars($img); ?>"
                         onerror="this.style.display='none';">
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>

                <td class="d-flex gap-2 flex-wrap">
                  <a class="btn btn-sm btn-outline-primary" href="view_issue.php?id=<?php echo (int)$r['id']; ?>">View</a>

                  <button class="btn btn-sm btn-outline-dark"
                          data-bs-toggle="modal"
                          data-bs-target="#statusModal"
                          data-id="<?php echo (int)$r['id']; ?>"
                          data-status="<?php echo htmlspecialchars($r['status']); ?>">
                    Update Status
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-center text-muted py-4">No issues found.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- STATUS UPDATE MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="update_issue_status.php">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fas fa-sync-alt me-2"></i> Update Issue Status
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- Hidden Issue ID -->
        <input type="hidden" name="id" id="m_issue_id">

        <!-- Status -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Status</label>
          <select class="form-select" name="status" id="m_status" required>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
          </select>
        </div>

        <!-- Admin Message -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Admin Message / Remark</label>
          <textarea
            name="message"
            id="m_message"
            class="form-control"
            rows="3"
            placeholder="Enter status update message (optional)"></textarea>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i> Save Changes
        </button>
      </div>

    </form>
  </div>
</div>


<!-- MAP MODAL -->
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Issue Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2 text-muted small" id="mapAddress">Loading...</div>
        <div id="map" style="height:420px;border-radius:14px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- IMAGE MODAL -->
<div class="modal fade" id="imageModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Issue Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="bigImg" src="" style="max-width:100%;border-radius:14px;">
      </div>
    </div>
  </div>
</div>

<?php include_once("includes/footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
 
if (!$.fn.DataTable.isDataTable('#issuesTable')) {
  $('#issuesTable').DataTable();
}

// Status modal fill
document.getElementById('statusModal').addEventListener('show.bs.modal', function (e) {
  const btn = e.relatedTarget;
  document.getElementById('m_issue_id').value = btn.getAttribute('data-id');
document.getElementById('m_status').value   = btn.getAttribute('data-status');

});

// Image modal
document.getElementById('imageModal').addEventListener('show.bs.modal', function (e) {
  const btn = e.relatedTarget;
  document.getElementById('bigImg').src = btn.getAttribute('data-img');
});

let map, marker;

async function geocodeByText(text){
  const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(text)}&limit=1`;
  const res = await fetch(url, { headers: { "Accept":"application/json" }});
  if(!res.ok) return null;
  const data = await res.json();
  if(!data || !data.length) return null;
  return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
}

document.getElementById('mapModal').addEventListener('shown.bs.modal', async function (e) {
  const btn = e.relatedTarget;
  const locationText = btn.getAttribute('data-location') || '';
  let lat = parseFloat(btn.getAttribute('data-lat') || '');
  let lng = parseFloat(btn.getAttribute('data-lng') || '');

  document.getElementById('mapAddress').textContent = locationText || 'Location';

  if (!map) {
    map = L.map('map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
    }).addTo(map);
  }

  // if lat/lng not present -> try geocode
  if (!isFinite(lat) || !isFinite(lng) || lat===0 || lng===0) {
    const pos = await geocodeByText(locationText);
    if(pos){ lat = pos.lat; lng = pos.lng; }
  }

  if (isFinite(lat) && isFinite(lng)) {
    map.setView([lat, lng], 15);
    if (marker) marker.remove();
    marker = L.marker([lat, lng]).addTo(map).bindPopup(locationText).openPopup();
    setTimeout(()=>{ map.invalidateSize(); }, 200);
  } else {
    map.setView([20.5937, 78.9629], 4); // India fallback
    if (marker) marker.remove();
    setTimeout(()=>{ map.invalidateSize(); }, 200);
  }
});

// cleanup marker on close
document.getElementById('mapModal').addEventListener('hidden.bs.modal', function () {
  if (marker) { marker.remove(); marker = null; }
});
</script>

</body>
</html>
