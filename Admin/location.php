<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

include("db.php");

// ---- Fetch locations ----
$locations = [];
$q = mysqli_query($conn, "SELECT * FROM locations ORDER BY id DESC");
while($row = mysqli_fetch_assoc($q)){
  $locations[] = $row;
}

// ---- Stats (simple) ----
$totalLocations = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM locations"))['c'];
$hotspots = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM locations WHERE priority='high'"))['c'];
$resolvedAreas = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(resolved_issues) as s FROM locations"))['s'];
if($resolvedAreas < 0) $resolvedAreas = 0;

// Avg. resolution: (simple demo) = total resolved issues / total locations (days text)
$avgResolution = ($totalLocations > 0) ? round($resolvedAreas / $totalLocations, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Locations | UrbanSolve</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
 body{
  background:#f4f6f9;
  font-family:'Poppins',sans-serif;
}

.main-content{
  padding:22px 18px;
}

/* Premium Cards */
.soft-card{
  border:0;
  border-radius:18px;
  box-shadow:0 10px 25px rgba(0,0,0,0.08);
  overflow:hidden;
}

/* KPI Cards */
.kpi-card{
  border:0;
  border-radius:18px;
  color:white;
  box-shadow:0 10px 25px rgba(0,0,0,0.12);
  transition:0.25s;
}

.kpi-card:hover{
  transform:translateY(-3px);
}

.kpi-title{
  font-size:12px;
  letter-spacing:.6px;
  opacity:.85;
}

.kpi-value{
  font-size:26px;
  font-weight:700;
  margin:4px 0;
}

/* Icon Box */
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

/* Map */
#map{
  height:420px;
  width:100%;
  border-radius:18px;
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
  padding:.42rem .6rem;
  font-weight:600;
}

/* Buttons */
.btn-rounded{
  border-radius:12px;
}

/* Search */
#searchBox{
  border-radius:12px;
}

</style>
</head>

<body>

<div class="container-fluid">
  <div class="row">
    <?php include_once("includes/leftnav.php"); ?>

    <div class="col-lg-9 col-xl-10 main-content">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <h2 class="fw-bold text-primary mb-1">Location Management</h2>
          <p class="text-muted mb-0">Track and manage locations on an interactive map</p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
            + Add New Location
          </button>
          <button class="btn btn-outline-primary" onclick="alert('Export feature optional')">
            Export
          </button>
        </div>
      </div>

      <?php if(isset($_GET['success'])){ ?>
        <div class="alert alert-success">Location added successfully!</div>
      <?php } ?>

      <!-- Stats -->
      <div class="row g-3 mb-3">

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#0d6efd,#4f9cff);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">TOTAL LOCATIONS</div>
          <div class="kpi-value"><?php echo $totalLocations; ?></div>
          <small>Saved locations</small>
        </div>
        <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#dc3545,#ff6b6b);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">HOTSPOTS</div>
          <div class="kpi-value"><?php echo $hotspots; ?></div>
          <small>Priority = High</small>
        </div>
        <div class="icon-box"><i class="bi bi-exclamation-triangle"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#198754,#2bb673);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">RESOLVED AREAS</div>
          <div class="kpi-value"><?php echo $resolvedAreas; ?></div>
          <small>Resolved issues</small>
        </div>
        <div class="icon-box"><i class="bi bi-check-circle"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3">
    <div class="card kpi-card" style="background:linear-gradient(135deg,#ffc107,#ffb300);">
      <div class="card-body d-flex justify-content-between">
        <div>
          <div class="kpi-title">AVG. RESOLUTION</div>
          <div class="kpi-value"><?php echo $avgResolution > 0 ? $avgResolution."d" : "—"; ?></div>
          <small>Simple avg</small>
        </div>
        <div class="icon-box"><i class="bi bi-graph-up"></i></div>
      </div>
    </div>
  </div>

</div>


      <!-- Map Card -->
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="fw-semibold">📍 Locations Map</div>
          <div class="d-flex gap-2 align-items-center">
            <select class="form-select form-select-sm" id="zoneFilter" style="width:160px;">
<option value="model-town">Model Town</option>
<option value="civil-lines">Civil Lines</option>
<option value="dugri">Dugri</option>
<option value="shimlapuri">Shimlapuri</option>
<option value="ludhiana-east">Ludhiana East</option>
<option value="ludhiana-west">Ludhiana West</option>
<option value="industrial-area">Industrial Area</option>
            </select>
            <button class="btn btn-sm btn-outline-secondary" id="refreshMapBtn">↻</button>
          </div>
        </div>
        <div class="card-body p-0">
          <div id="map"></div>
        </div>
      </div>

      <!-- Table -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div class="fw-semibold">All Locations</div>
          <input id="searchBox" class="form-control form-control-sm" style="width:260px"
                 placeholder="Search name / address / zone...">
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0" id="locationsTable">
              <thead>
                <tr>
                  <th style="width:60px">Sr.No</th>
                  <th>Location</th>
                  <th style="width:120px">Zone</th>
                  <th style="width:120px">Priority</th>
                  <th style="width:170px">Coordinates</th>
                  <th style="width:220px">Action</th>
                </tr>
              </thead>
              <tbody>
              <?php $sr=1; foreach($locations as $loc): 
                $lat = $loc['latitude'];
                $lng = $loc['longitude'];
                $zone = strtolower($loc['zone'] ?? '');
                $priority = strtolower($loc['priority'] ?? 'medium');
              ?>
                <tr
                  data-name="<?php echo strtolower($loc['name']); ?>"
                  data-address="<?php echo strtolower($loc['address']); ?>"
                  data-zone="<?php echo strtolower($zone); ?>"
                >
                  <td><?php echo $sr++; ?></td>
                  <td>
                    <div class="fw-semibold"><?php echo htmlspecialchars($loc['name']); ?></div>
                    <small class="text-muted"><?php echo htmlspecialchars($loc['address']); ?></small>
                  </td>
                  <td><span class="badge bg-secondary badge-zone"><?php echo htmlspecialchars($zone); ?></span></td>
                  <td>
                    <?php
$p = strtolower($priority);
$color = ($p=='high')?'danger':(($p=='medium')?'warning':'success');
?>

<span class="badge bg-<?php echo $color; ?> badge-pill">
   <?php echo ucfirst($priority); ?>
</span>

					
                  </td>
                  <td class="coord"><?php echo htmlspecialchars($lat); ?>, <?php echo htmlspecialchars($lng); ?></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary btn-rounded"
                      data-bs-toggle="modal"
                      data-bs-target="#viewModal"
                      data-name="<?php echo htmlspecialchars($loc['name']); ?>"
                      data-address="<?php echo htmlspecialchars($loc['address']); ?>"
                      data-zone="<?php echo htmlspecialchars($zone); ?>"
                      data-priority="<?php echo htmlspecialchars($priority); ?>"
                      data-lat="<?php echo htmlspecialchars($lat); ?>"
                      data-lng="<?php echo htmlspecialchars($lng); ?>"
                    >View</button>

                    <button class="btn btn-sm btn-outline-warning btn-rounded"
                      data-bs-toggle="modal"
                      data-bs-target="#mapModal"
                      data-name="<?php echo htmlspecialchars($loc['name']); ?>"
                      data-address="<?php echo htmlspecialchars($loc['address']); ?>"
                      data-lat="<?php echo htmlspecialchars($lat); ?>"
                      data-lng="<?php echo htmlspecialchars($lng); ?>"
                    >Map</button>

                    <a  class="btn btn-sm btn-outline-danger btn-rounded"
                       href="delete_location.php?id=<?php echo (int)$loc['id']; ?>"
                       onclick="return confirm('Delete this location?')"
                    >Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <?php include_once("includes/footer.php"); ?>
    </div>
  </div>
</div>


<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vTitle">Location Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input class="form-control" id="vName" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Zone</label>
            <input class="form-control" id="vZone" readonly>
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea class="form-control" id="vAddress" rows="2" readonly></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Priority</label>
            <input class="form-control" id="vPriority" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Coordinates</label>
            <input class="form-control" id="vCoord" readonly>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- MAP MODAL (same page, no google maps) -->
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="modalTitle">Location Map</h5>
          <small class="text-muted" id="modalSub"></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="modalMap"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline-primary" id="modalCenterBtn">Center</button>
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- ADD LOCATION MODAL (simple) -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" method="POST" action="save_location.php">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Add New Location</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Location Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Zone</label>
            <select name="zone" class="form-select" required>
              <option value="">Select</option>
              <option value="residential">Residential</option>
              <option value="commercial">Commercial</option>
              <option value="industrial">Industrial</option>
              <option value="public">Public</option>
              <option value="mixed">Mixed</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" required></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">Latitude</label>
            <input type="number" step="any" name="latitude" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Longitude</label>
            <input type="number" step="any" name="longitude" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Description (optional)</label>
            <input type="text" name="description" class="form-control">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
const locationsData = <?= json_encode($locations); ?>;

let map, markers = [];
let modalMap, modalMarker, currentModalLatLng = null;

// --- tiles (OSM + fallback)
function addBaseTiles(targetMap){
  const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    detectRetina: true
  });

  const carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    subdomains: 'abcd',
    maxZoom: 19,
    detectRetina: true
  });

  osm.addTo(targetMap);
  osm.on('tileerror', function(){
    try{ targetMap.removeLayer(osm); }catch(e){}
    carto.addTo(targetMap);
  });
}

function escapeHtml(str){
  return String(str ?? '').replace(/[&<>"']/g, s => ({
    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
  }[s]));
}

// Marker color by priority
function getMarkerColor(priority){
  priority = (priority || '').toLowerCase();
  if(priority === 'high') return 'red';
  if(priority === 'medium') return 'orange';
  return 'green';
}

// Render markers
function renderMainMarkers(list){
  markers.forEach(m => map.removeLayer(m));
  markers = [];

  list.forEach(loc => {
    const lat = parseFloat(loc.latitude);
    const lng = parseFloat(loc.longitude);
    if(!isFinite(lat) || !isFinite(lng)) return;

    const color = getMarkerColor(loc.priority);

    const icon = L.icon({
      iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${color}.png`,
      iconSize: [25, 41],
      iconAnchor: [12, 41]
    });

    const m = L.marker([lat, lng], {icon}).addTo(map);

    m.bindPopup(`
  <b>${escapeHtml(loc.name)}</b><br>
  ${escapeHtml(loc.address)}<br>
  <b>Priority:</b> ${escapeHtml(loc.priority)}<br><br>

  <a href="https://www.google.com/maps?q=${lat},${lng}" 
     target="_blank" 
     class="btn btn-sm btn-primary">
     📍 Open in Google Maps
  </a>
`);

    markers.push(m);
  });
}

// Fit markers
function fitMainToMarkers(){
  const pts = markers.map(m => m.getLatLng());
  if(pts.length){
    map.fitBounds(L.latLngBounds(pts), { padding:[40,40] });
  } else {
    map.setView([30.9010, 75.8573], 12); // Ludhiana default
  }
}

document.addEventListener('DOMContentLoaded', function(){

  // ✅ INIT MAP (Ludhiana restricted)
  map = L.map('map', {
    maxBounds: [
      [30.75, 75.70],
      [31.05, 76.00]
    ],
    maxBoundsViscosity: 1.0
  }).setView([30.9010, 75.8573], 12);

  addBaseTiles(map);

  renderMainMarkers(locationsData);
  fitMainToMarkers();

  setTimeout(()=> map.invalidateSize(), 400);

  // ✅ CLICK MAP → AUTO LAT LNG
  map.on('click', function(e){
    const lat = e.latlng.lat.toFixed(6);
    const lng = e.latlng.lng.toFixed(6);

    document.querySelector("[name='latitude']").value = lat;
    document.querySelector("[name='longitude']").value = lng;

    if(window.tempMarker){
      map.removeLayer(window.tempMarker);
    }

    window.tempMarker = L.marker([lat, lng]).addTo(map)
      .bindPopup("Selected Location").openPopup();
  });

  // ✅ FILTER ZONE
  document.getElementById('zoneFilter').addEventListener('change', function(){
    const z = this.value;
    const filtered = (z === 'all') ? locationsData : locationsData.filter(x => (x.zone||'').toLowerCase() === z);
    renderMainMarkers(filtered);
    fitMainToMarkers();
  });

  // ✅ REFRESH MAP
  document.getElementById('refreshMapBtn').addEventListener('click', function(){
    renderMainMarkers(locationsData);
    fitMainToMarkers();
  });

  // ✅ SEARCH TABLE
  document.getElementById('searchBox').addEventListener('input', function(){
    const term = this.value.trim().toLowerCase();
    document.querySelectorAll('#locationsTable tbody tr').forEach(tr=>{
      const name = tr.getAttribute('data-name') || '';
      const addr = tr.getAttribute('data-address') || '';
      const zone = tr.getAttribute('data-zone') || '';
      tr.style.display = (name.includes(term) || addr.includes(term) || zone.includes(term)) ? '' : 'none';
    });
  });

  // ✅ VIEW MODAL
  document.getElementById('viewModal').addEventListener('show.bs.modal', function(event){
    const btn = event.relatedTarget;

    document.getElementById('vName').value = btn.getAttribute('data-name') || '';
    document.getElementById('vZone').value = btn.getAttribute('data-zone') || '';
    document.getElementById('vAddress').value = btn.getAttribute('data-address') || '';
    document.getElementById('vPriority').value = btn.getAttribute('data-priority') || '';

    const lat = btn.getAttribute('data-lat') || '';
    const lng = btn.getAttribute('data-lng') || '';
    document.getElementById('vCoord').value = lat + ", " + lng;
  });

  // ✅ MAP MODAL
  const mapModalEl = document.getElementById('mapModal');

  mapModalEl.addEventListener('shown.bs.modal', function (event) {
    const btn = event.relatedTarget;

    const name = btn.getAttribute('data-name') || 'Location';
    const address = btn.getAttribute('data-address') || '';
    const lat = parseFloat(btn.getAttribute('data-lat'));
    const lng = parseFloat(btn.getAttribute('data-lng'));

    currentModalLatLng = [lat, lng];

    document.getElementById('modalTitle').innerText = name + " (Map)";
    document.getElementById('modalSub').innerText = address;

    if(!modalMap){
      modalMap = L.map('modalMap').setView([lat, lng], 15);
      addBaseTiles(modalMap);
    }

    setTimeout(()=> modalMap.invalidateSize(), 250);

    if(modalMarker){
      modalMarker.setLatLng([lat, lng]);
    } else {
      modalMarker = L.marker([lat, lng]).addTo(modalMap);
    }

    modalMap.setView([lat, lng], 15);
    modalMarker.bindPopup(`<b>${escapeHtml(name)}</b><br>${escapeHtml(address)}`).openPopup();
  });

  // CENTER BUTTON
  document.getElementById('modalCenterBtn').addEventListener('click', function(){
    if(modalMap && currentModalLatLng){
      modalMap.setView(currentModalLatLng, 15);
      if(modalMarker) modalMarker.openPopup();
    }
  });

  // ✅ ONLY LUDHIANA VALIDATION
  document.querySelector("#addLocationModal form").addEventListener("submit", function(e){
    const address = document.querySelector("[name='address']").value.toLowerCase();

    if(!address.includes("ludhiana")){
      alert("❌ Only Ludhiana locations allowed");
      e.preventDefault();
    }
  });

});
</script>

</body>
</html>
