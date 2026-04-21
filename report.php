<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_q = mysqli_query($conn, "SELECT full_name FROM members WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_q);

$filename = "";

if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
    
    $filename = time() . "_" . $_FILES['photo']['name'];
    
    move_uploaded_file(
        $_FILES['photo']['tmp_name'],
        "uploads/" . $filename
    );
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

    // IMAGE UPLOAD
    $filename = "";
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $filename = time() . "_" . $_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/" . $filename);
    }

    // INSERT QUERY
    mysqli_query($conn, "INSERT INTO issues 
    (user_id, category, title, description, location, image, priority, status, created_at) 
    VALUES 
    ('$user_id', '$category_id', '$title', '$description', '$location', '$filename', '$priority', 'Pending', NOW())");

    header("Location: dashboard.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Issue - Urban Pulse</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 30px;
        }
        
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .report-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 12px 15px;
        }
        .issue-type-card{
		  cursor:pointer;
		  border:1px solid #e6e6e6;
		  border-radius:12px;
		  padding:22px 18px;
		  text-align:center;
		  transition:.2s;
		  background:#fff;
		  height:100%;
		}
		.issue-type-card:hover{
		  transform: translateY(-2px);
		  box-shadow:0 10px 25px rgba(0,0,0,.08);
		}
		.issue-type-card.active{
		  border:2px solid #0d6efd;
		  box-shadow:0 10px 25px rgba(13,110,253,.18);
		}
		.issue-icon{
		  font-size:34px;
		  margin-bottom:10px;
		}

        
        .photo-preview {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .photo-preview img {
            max-width: 100%;
            max-height: 180px;
            border-radius: 5px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-indicator::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .step.active .step-number {
            background-color: var(--secondary);
            color: white;
        }
        
        .step.completed .step-number {
            background-color: var(--success);
            color: white;
        }
        
        .step-text {
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        
        .confirmation-message {
            text-align: center;
            padding: 40px 20px;
        }
        
        .confirmation-icon {
            font-size: 4rem;
            color: var(--success);
            margin-bottom: 20px;
        }
        
        .report-id {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            margin: 15px 0;
        }
        .step.completed .step-number{
    background-color: #27ae60;
    color: #fff;
}

.step.active .step-number{
    background-color: #0d6efd;
    color: #fff;
}
    </style>
</head>
<body>
    <?php include("header.php");?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-3">Report a City Issue</h1>
            <p class="lead">Help improve your community by reporting problems you encounter</p>
        </div>
    </section>

    <!-- Report Form -->
<section class="container report-container">

  <!-- Step Indicator -->
  <div class="step-indicator">
    <div class="step active" data-step="1">
      <div class="step-number">1</div>
      <div class="step-text">Issue Type</div>
    </div>
    <div class="step" data-step="2">
      <div class="step-number">2</div>
      <div class="step-text">Details & Location</div>
    </div>
    <div class="step" data-step="3">
      <div class="step-number">3</div>
      <div class="step-text">Photo & Review</div>
    </div>
    <div class="step" data-step="4">
      <div class="step-number">4</div>
      <div class="step-text">Confirmation</div>
    </div>
  </div>

   <form id="reportForm" action="save_issue.php" method="POST" enctype="multipart/form-data">

    <!-- ✅ ONE TIME hidden fields (NO DUPLICATES) -->
    <input type="hidden" name="category_id" id="category_id">
	
    <!-- Step 1 -->
	<?php
$iconMap = [
  'Water'        => 'fas fa-tint',
  'Road'         => 'fas fa-road',
  'Street Dogs'  => 'fas fa-dog',
  'Garbage'      => 'fas fa-trash',
  'Waste Management' => 'fas fa-dumpster',
  'Electricity'  => 'fas fa-bolt',
  'Street Light'=> 'fas fa-lightbulb',
  'Other'        => 'fas fa-exclamation-circle'
];
?>

<?php
$catQuery = "SELECT id, category_name, description
             FROM categories
             WHERE status=1
             ORDER BY id DESC";
$catResult = mysqli_query($conn, $catQuery);

?>

<div class="form-section active" id="step1">
  <div class="report-card">
    <h3 class="mb-4">What type of issue are you reporting?</h3>
    <p class="text-muted mb-4">Select the category that best describes the problem</p>
    <div class="row">
      <?php while($cat = mysqli_fetch_assoc($catResult)) { 
       $color = '#0d6efd'; // bootstrap primary
      ?>
        <div class="col-md-4 mb-3">
          <div class="issue-type-card"
               data-id="<?= $cat['id']; ?>"
               style="border-top:4px solid <?= htmlspecialchars($color); ?>;">

				<div class="issue-icon" style="color:<?= htmlspecialchars($color); ?>;">
				  <?php
					$catName = $cat['category_name'];
					$icon = $iconMap[$catName] ?? 'fas fa-exclamation-circle';
				  ?>
				  <i class="<?= $icon ?>"></i>
				</div>

            <h5><?= htmlspecialchars($cat['category_name']); ?></h5>
			<p class="text-muted small"><?= htmlspecialchars($cat['description']); ?></p>
          </div>
        </div>
      <?php } ?>
    </div>

    <div class="navigation-buttons">
      <div></div>
      <button type="button" class="btn btn-primary" id="step1NextBtn">Next</button>
    </div>
  </div>
</div>

    <!-- Step 2 -->
    <div class="form-section" id="step2">
  <div class="report-card">
    <h3 class="mb-4">Report New Issue</h3>
    <p class="text-muted mb-4">Enter issue title and description</p>

    <div class="mb-3">
      <label class="form-label">Issue Title</label>
      <input type="text" class="form-control" name="title" id="issueTitle"
             placeholder="e.g. Water Pipe Leakage" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea class="form-control" id="issueDescription" name="description" rows="4"
        placeholder="Write issue details..." required></textarea>
    </div>

    <div class="navigation-buttons">
      <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">Back</button>
      <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next</button>
    </div>
  </div>
</div>

    <!-- Step 3 -->
    <div class="form-section" id="step3">
  <div class="report-card">
    <h3 class="mb-4">More Details</h3>
    <p class="text-muted mb-4">Location, priority, department and image</p>

    <div class="mb-3">
  <label class="form-label">Location</label>

  <div class="input-group">
    <input type="text" 
           class="form-control" 
           name="location" 
           id="locationText"
           placeholder="Enter Your Location"
           required>

    
  </div>
</div>

<!-- Hidden fields -->
<input type="hidden" name="latitude" id="lat">
<input type="hidden" name="longitude" id="lng">

    <div class="row">
      <div class="col-md-6">
        <label class="form-label">Priority</label>
        <select class="form-select" name="priority" id="prioritySelect" required>
          <option value="Low">Low</option>
          <option value="Medium" selected>Medium</option>
          <option value="High">High</option>
        </select>
      </div>

    </div>

    <div class="mt-3">
      <label class="form-label">Upload Image (Optional)</label>
      <input type="file" class="form-control" id="photoUpload" name="image" accept="image/*">
    </div>

    <div class="navigation-buttons">
      <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">Back</button>
      <button type="submit" class="btn btn-primary">
        Submit Report <i class="fas fa-paper-plane ms-2"></i>
      </button>
    </div>
  </div>
</div>

    <div class="form-section" id="step4">
      <div class="report-card">
        ...
      </div>
    </div>

  </form>
</section>


    <!-- Footer -->
<?php include("footer.php");?>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
	
	// before submit (optional)
document.getElementById("reportForm").addEventListener("submit", function(e){
  const loc = document.getElementById("locationText").value.trim();
  if(!loc){
    alert("Please enter location");
    e.preventDefault();
  }
});

  // category card select
  document.querySelectorAll('.issue-type-card').forEach(card => {
    card.addEventListener('click', function () {
      document.querySelectorAll('.issue-type-card').forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      document.getElementById('category_id').value = this.getAttribute('data-id');
    });
  });

  // step1 next
  document.getElementById("step1NextBtn").addEventListener("click", () => {
    if(!document.getElementById("category_id").value){
      alert("Please select an issue type.");
      return;
    }
    nextStep(2);
  });

});

// steps
function nextStep(step) {

  if (step === 2 && !document.getElementById("category_id").value) {
    alert('Please select an issue type');
    return;
  }

  if(step === 3){
      setTimeout(() => {
          map.invalidateSize();
      }, 200);
  }

  document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
  document.getElementById(`step${step}`).classList.add('active');

  // ✅ ADD THIS LINE

   updateStepIndicator(step);

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {

  document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
  document.getElementById(`step${step}`).classList.add('active');

  // ✅ ADD THIS LINE
  updateStepIndicator(step);
}

    function updateStepIndicator(step){
    document.querySelectorAll(".step").forEach(el => {

        let s = parseInt(el.getAttribute("data-step"));

        el.classList.remove("active","completed");

        if(s < step){
            el.classList.add("completed");
        }
        else if(s === step){
            el.classList.add("active");
        }

    });
}

document.getElementById("getLocationBtn").addEventListener("click", function(){

    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(function(position){

            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            map.setView([lat, lng], 15);

            if(marker){
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(res => res.json())
            .then(data => {
                if(data.display_name){
                    document.getElementById("locationText").value = data.display_name;
                }
            });

        });

    }else{
        alert("Geolocation not supported");
    }

});


// 🌍 INIT MAP
var map = L.map('map').setView([30.9010, 75.8573], 13); // default Ludhiana

// 🗺️ TILE
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

// 📍 Marker
var marker;

// 📌 Click on map
map.on('click', function(e){

    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    // remove old marker
    if(marker){
        map.removeLayer(marker);
    }

    // add new marker
    marker = L.marker([lat, lng]).addTo(map);

    // set hidden fields
    document.getElementById("lat").value = lat;
    document.getElementById("lng").value = lng;

    // 🔥 Reverse geocoding (OpenStreetMap FREE)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
    .then(res => res.json())
    .then(data => {
        if(data.display_name){
            document.getElementById("locationText").value = data.display_name;
        }
    });

});
</script>

</body>
</html>