<?php
session_start();
include("db.php");

/* Stats Queries */
$newCount = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM issues WHERE status='Pending'"
))['c'];

 /* Fetch Issues with location */
$issuesMap = [];
$q = mysqli_query($conn, "SELECT title, latitude, longitude, priority FROM issues WHERE latitude IS NOT NULL AND longitude IS NOT NULL");

while($row = mysqli_fetch_assoc($q)){
    $issuesMap[] = $row;
}

$progressCount = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM issues WHERE status='In Progress'"
))['c'];

$resolvedCount = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM issues WHERE status='Resolved'"
))['c'];

/* Success Rate */
$totalIssues = $newCount + $progressCount + $resolvedCount;

$successRate = $totalIssues > 0
    ? round(($resolvedCount / $totalIssues) * 100)
    : 0;

/* Recent Issues */
$recentIssues = mysqli_query($conn,
    "SELECT title, status FROM issues ORDER BY id DESC LIMIT 3"
);
?>


<?php

if(isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Urban Pulse – Citizen Engagement & Data Hub</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
    --primary:#2c3e50;
    --secondary:#3498db;
    --dark:#1f2933;
}
body{background:#f8f9fa;font-family:'Segoe UI',sans-serif;}
.hero-section{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;padding:100px 0;
}
.feature-card{
    border:none;border-radius:14px;
    box-shadow:0 6px 15px rgba(0,0,0,.1);
    transition:.3s;
}
.feature-card:hover{transform:translateY(-6px);}
.feature-icon{font-size:2.5rem;color:var(--secondary);}
.stat-card{
    background:#fff;border-radius:14px;
    text-align:center;padding:25px;
    box-shadow:0 4px 10px rgba(0,0,0,.08);
}
.map-box{
    height: 300px;
    border-radius: 14px;
}

@media (max-width: 768px){
    .map-box{
        height: 250px;
    }
}
.list-group-item{
    font-size: 14px;
}
footer{background:var(--dark);color:#fff;padding:40px 0;}
@media (max-width: 768px){
    .hero-section{
        padding: 60px 15px;
    }

    .hero-section h1{
        font-size: 24px;
    }

    .hero-section .btn{
        width: 100%;
        margin-bottom: 10px;
    }
}
@media (max-width: 576px){

    h2{
        font-size: 20px;
    }

    p{
        font-size: 14px;
    }

    .stat-card{
        padding: 15px;
    }

    .feature-card{
        padding: 20px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
<div class="container">

<a class="navbar-brand fw-bold" href="index.php">
<i class="fas fa-city me-2"></i>Ludhiana City
</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ms-auto align-items-lg-center">
<li class="nav-item me-lg-3 mb-2 mb-lg-0">
<a href="dashboard.php" class="nav-link">Dashboard</a>
</li>

<li class="nav-item">
<a href="report_issue.php" class="btn btn-primary w-100 w-lg-auto">
<i class="fas fa-bullhorn me-1"></i>Report Issue
</a>
</li>
</ul>
</div>

</div>
</nav>

<!-- HERO -->
<section class="hero-section text-center">
<div class="container">
<h1 class="display-5 fw-bold">Connecting Citizens & City Officials</h1>
<p class="lead mt-3">
A smart platform to report, track and resolve urban issues efficiently.
</p>

<a href="report_issue.php" class="btn btn-light btn-lg mt-4 me-3">
Report an Issue
</a>
<a href="dashboard.php" class="btn btn-outline-light btn-lg mt-4">
View Dashboard
</a>
</div>
</section>

<!-- FEATURES -->
<section class="container my-5">
<div class="text-center mb-5">
<h2 class="fw-bold">How Urban Pulse Works</h2>
<p class="text-muted">Simple & transparent problem management system</p>
</div>

<div class="row g-4">
<div class="col-lg-4 col-md-6 col-sm-12">
<div class="card feature-card p-4 text-center">
<div class="feature-icon mb-3"><i class="fas fa-map-marker-alt"></i></div>
<h5>Location Based Reporting</h5>
<p class="text-muted">Pinpoint issues directly on the map.</p>
</div>
</div>

<div class="col-md-4">
<div class="card feature-card p-4 text-center">
<div class="feature-icon mb-3"><i class="fas fa-camera"></i></div>
<h5>Photo Evidence</h5>
<p class="text-muted">Upload images for faster verification.</p>
</div>
</div>

<div class="col-md-4">
<div class="card feature-card p-4 text-center">
<div class="feature-icon mb-3"><i class="fas fa-sync-alt"></i></div>
<h5>Status Tracking</h5>
<p class="text-muted">Track issue progress in real time.</p>
</div>
</div>
</div>
</section>

<!-- DASHBOARD PREVIEW -->
<section class="bg-light py-5">
<div class="container">
<div class="row text-center mb-4">
<h2 class="fw-bold">Dashboard Overview</h2>
<p class="text-muted">Real-time monitoring of city issues</p>
</div>

<div class="row g-4">
<div class="col-lg-3 col-md-6 col-12">    <div class="stat-card">
        <h3><?php echo $newCount; ?></h3>
        <p>New</p>
    </div>
</div>

<div class="col-lg-3 col-md-6 col-12">    <div class="stat-card">
        <h3><?php echo $progressCount; ?></h3>
        <p>In Progress</p>
    </div>
</div>

<div class="col-lg-3 col-md-6 col-12">    <div class="stat-card">
        <h3><?php echo $resolvedCount; ?></h3>
        <p>Resolved</p>
    </div>
</div>

<div class="col-lg-3 col-md-6 col-12">    <div class="stat-card">
        <h3><?php echo $successRate; ?>%</h3>
        <p>Success Rate</p>
    </div>
</div>

</div>

<div class="row mt-4">
<div class="col-lg-8 col-12 mb-3">
<div id="mapPreview" class="map-box"></div>
</div>

<div class="col-lg-4 col-12">
<div class="card shadow">
<div class="card-header bg-primary text-white">Recent Issues</div>
<ul class="list-group list-group-flush">

<?php while($issue = mysqli_fetch_assoc($recentIssues)): ?>

<li class="list-group-item">
    <?php echo htmlspecialchars($issue['title']); ?>
    – 
    <?php echo htmlspecialchars($issue['status']); ?>
</li>

<?php endwhile; ?>

</ul>

</div>
</div>
</div>
</div>
</section>

<!-- FOOTER -->
<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const issuesData = <?= json_encode($issuesMap); ?>;

// 🗺️ Map Init (Ludhiana Locked)
const map = L.map('mapPreview', {
  maxBounds: [
    [30.75, 75.70],
    [31.05, 76.00]
  ],
  maxBoundsViscosity: 1.0
}).setView([30.900965, 75.857277], 12);

// 🌍 Tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19
}).addTo(map);

// 🧠 Safe text
function escapeHtml(text){
  return String(text).replace(/[&<>"']/g, m => ({
    '&':'&amp;',
    '<':'&lt;',
    '>':'&gt;',
    '"':'&quot;',
    "'":'&#039;'
  }[m]));
}

// 🎯 Priority Color
function getMarkerColor(priority){
  priority = (priority || '').toLowerCase();
  if(priority === 'high') return 'red';
  if(priority === 'medium') return 'orange';
  return 'green';
}

// 📦 Group markers (NEW FEATURE)
const markersGroup = L.featureGroup().addTo(map);

// 📊 Counter (NEW)
let totalMarkers = 0;
issuesData.forEach(issue => {

  const lat = parseFloat(issue.latitude);
  const lng = parseFloat(issue.longitude);

  if(isNaN(lat) || isNaN(lng)) return;
  
  // 📍 Ludhiana filter
  //if(lat < 30.75 || lat > 31.05 || lng < 75.70 || lng > 76.00){
  //  return;
  //}

  const color = getMarkerColor(issue.priority);

  const icon = L.icon({
    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-${color}.png`,
    iconSize: [25,41],
    iconAnchor: [12,41]
  });

  // ✅ Marker ADD karo
  const marker = L.marker([lat, lng], {icon}).addTo(map);

  // ✅ Circle bhi group me add karo
  const circle = L.circle([lat, lng], {
    color: color,
    fillColor: color,
    fillOpacity: 0.3,
    radius: 300
  }).addTo(map);

  // ✅ Single popup
  const popupContent = `
    <b>${escapeHtml(issue.title)}</b><br>
    Priority: <b style="color:${color}">${escapeHtml(issue.priority)}</b>
  `;

  marker.bindPopup(popupContent);
  circle.bindPopup(popupContent);

  // 🌐 Google Maps open
  marker.on('click', function(){
    window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
  });

  // ✅ BOTH add in group (IMPORTANT)
  markersGroup.addLayer(marker);
  markersGroup.addLayer(circle);

  totalMarkers++;
});

// 🔍 Auto zoom
if(totalMarkers > 0){
  map.fitBounds(markersGroup.getBounds(), { padding: [40, 40] });
}

// 📍 User current location (NEW 🔥)
map.locate({setView: false});

map.on('locationfound', function(e){
  L.circleMarker(e.latlng, {
    radius: 6,
    color: 'blue'
  }).addTo(map).bindPopup("📍 You are here");
});

// ❌ Location error ignore
map.on('locationerror', function(){
  console.log("Location access denied");
});

// 🎛️ Legend Control (NEW 🔥)
const legend = L.control({position: 'bottomright'});

legend.onAdd = function(){
  const div = L.DomUtil.create('div', 'info legend');
  div.innerHTML = `
    <div style="background:white;padding:8px;border-radius:8px;font-size:12px">
      <b>Priority</b><br>
      🔴 High<br>
      🟠 Medium<br>
      🟢 Low
    </div>
  `;
  return div;
};

legend.addTo(map);

</script>
</body>
</html>
