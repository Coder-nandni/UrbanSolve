
<?php
session_start();
include("db.php");

/* login check */
if (!isset($_SESSION['user_id'])) {
    header("Location: signup.php");
    exit;
}

$user_id = $_SESSION['user_id'];
 $where = "user_id='$user_id'";

if (isset($_GET['status']) && $_GET['status'] != 'all') {
    $map = [
        'pending'     => 'Pending',
        'in-progress' => 'In Progress',
        'resolved'    => 'Resolved'
    ];

    if (isset($map[$_GET['status']])) {
        $status = mysqli_real_escape_string($conn, $map[$_GET['status']]);
        $where .= " AND status='$status'";
    }
}

$currentStatus = $_GET['status'] ?? 'all';
$complaints = mysqli_query(
    $conn,
    "SELECT * FROM issues WHERE $where ORDER BY id DESC"
);

$total = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT COUNT(*) AS c FROM issues WHERE user_id='$user_id'"
))['c'];

$pending = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT COUNT(*) AS c FROM issues WHERE user_id='$user_id' AND status='Pending'"
))['c'];

$progress = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT COUNT(*) AS c FROM issues WHERE user_id='$user_id' AND status='In Progress'"
))['c'];

$resolved = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT COUNT(*) AS c FROM issues WHERE user_id='$user_id' AND status='Resolved'"
))['c'];

/* fetch logged-in user data */
$user_q = mysqli_query($conn, "SELECT * FROM members WHERE id='$user_id'");
$user   = mysqli_fetch_assoc($user_q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Urban Pulse</title>
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
            --info: #17a2b8;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        /* USER NAVBAR */
	.user-navbar{
	  background: linear-gradient(135deg, #1f2933, #111827);
	  padding: 12px 0;
	}

	.user-navbar .navbar-brand{
	  font-size: 1.4rem;
	  letter-spacing: .5px;
	}

	/* Highlighted Report Button */
	.btn-report{
	  background: linear-gradient(135deg, #ff9800, #ff5722);
	  color: #fff !important;
	  border-radius: 30px;
	  padding: 8px 18px;
	  font-weight: 600;
	  box-shadow: 0 4px 12px rgba(255,152,0,.4);
	  transition: all .3s ease;
	}

	.btn-report:hover{
	  transform: translateY(-1px);
	  box-shadow: 0 6px 18px rgba(255,87,34,.6);
	}

	/* Active Dashboard Link */
	.user-navbar .nav-link.active{
	  color: #0d6efd !important;
	  font-weight: 600;
	}

	/* Dropdown */
	.user-navbar .dropdown-menu{
	  border-radius: 12px;
	}

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 30px;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .stats-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }
        
        .stats-card.total {
            background: linear-gradient(135deg, var(--primary) 0%, #34495e 100%);
        }
        
        .stats-card.pending {
            background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
        }
        
        .stats-card.in-progress {
            background: linear-gradient(135deg, var(--info) 0%, #2980b9 100%);
        }
        
        .stats-card.resolved {
            background: linear-gradient(135deg, var(--success) 0%, #2ecc71 100%);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .report-card{
    background:#fff;
    border-radius:14px;
    padding:18px;
    margin-bottom:18px;
    border:1px solid #eef2f7;
    transition:all .25s ease;
    position:relative;
}

.report-card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,.08);
}

.report-card.pending{ border-left:4px solid #f39c12; }
.report-card.in-progress{ border-left:4px solid #17a2b8; }
.report-card.resolved{ border-left:4px solid #27ae60; }

.report-title{
    font-weight:700;
    font-size:16px;
    margin-bottom:4px;
}

.report-location{
    font-size:13px;
    color:#6c757d;
}

.report-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:10px;
}

.report-meta{
    font-size:12px;
    color:#9ca3af;
}

        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning);
        }
        
        .status-in-progress {
            background-color: rgba(23, 162, 184, 0.1);
            color: var(--info);
        }
        
        .status-resolved {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success);
        }
        
        .issue-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary);
            font-size: 1.5rem;
        }
        
        .filter-buttons .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .map-mini {
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        
        .report-details-modal .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }
        
        .status-timeline{
    border-left:2px solid #e5e7eb;
    margin-left:8px;
    padding-left:18px;
}

.timeline-step{
    position:relative;
    margin-bottom:18px;
}

.timeline-step::before{
    content:'';
    position:absolute;
    left:-26px;
    top:3px;
    width:12px;
    height:12px;
    border-radius:50%;
    background:#0d6efd;
}

.timeline-step.completed::before{
    background:#27ae60;
}

.timeline-step.pending::before{
    background:#cbd5f5;
}

.timeline-title{
    font-weight:600;
    font-size:14px;
}

.timeline-desc{
    font-size:13px;
    color:#6c757d;
}

        
        .btn-primary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
		.modal-header{
    border-bottom:0;
}

.modal-content{
    border-radius:18px;
    border:0;
}

.issue-detail-label{
    font-size:12px;
    color:#6b7280;
    text-transform:uppercase;
    letter-spacing:.04em;
}
.modal.fade .modal-dialog{
    transform: scale(.8);
    transition: transform .25s ease;
}
.modal.show .modal-dialog{
    transform: scale(1);
}

.issue-detail-value{
    font-weight:600;
}
.sms-modal{
    border-radius:22px;
    overflow:hidden;
    animation: popupScale .35s ease;
}

.success-icon{
    font-size:60px;
    color:#0d6efd;
    margin-bottom:5px;
}

/* SMS CARD */
.sms-card{
    background:#f8fafc;
    border-radius:16px;
    text-align:left;
    overflow:hidden;
    border:1px solid #e5e7eb;
}

.sms-header{
    background:linear-gradient(135deg,#0d6efd,#0b5ed7);
    color:white;
    padding:10px 15px;
    font-weight:600;
    font-size:13px;
    letter-spacing:.3px;
}

.sms-body{
    padding:15px;
    font-size:13.5px;
    color:#374151;
    line-height:1.5;
}

.ticket-box{
    background:#111827;
    color:#fff;
    padding:10px;
    border-radius:10px;
    text-align:center;
    font-size:18px;
    font-weight:800;
    letter-spacing:1px;
    margin:6px 0 10px;
}

.sms-footer{
    font-size:12px;
    color:#6b7280;
}

#thankyouModal .modal-content{
    animation: popupScale .35s ease;
}

@keyframes popupScale{
    from{
        transform: scale(.8);
        opacity:0;
    }
    to{
        transform: scale(1);
        opacity:1;
    }
}

#confettiCanvas{
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    pointer-events:none;
    z-index:9999;
}
.report-card {
    position: relative;
    padding-top: 25px; 
}

.ticket-badge {
    position: absolute;
    top: 39px;
    right: 25px;
    background: linear-gradient(45deg, #007bff, #00c6ff);
    color: #fff;
    padding: 4px 10px;
    font-size: 11px;
    border-radius: 15px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
@media (max-width: 576px){

    .report-card .d-flex{
        flex-direction: column;
        gap: 10px;
    }

    .report-img-container img,
    .report-img-placeholder{
        width: 100% !important;
        height: 150px !important;
    }

    .report-footer{
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .ticket-badge{
        position: static;
        display: inline-block;
        margin-top: 5px;
    }

    .report-title{
        font-size: 15px;
    }
}
@media (max-width: 992px){
    .stats-card{
        margin-bottom: 15px;
    }
}

@media (max-width: 768px){
    .stats-number{
        font-size: 2rem;
    }
}

@media (max-width: 576px){
    .col-md-3{
        width: 100%;
    }
}
@media (max-width: 576px){

    .filter-buttons{
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-buttons .btn{
        flex: 1 1 48%;
        font-size: 13px;
        padding: 6px;
    }
}
@media (max-width: 768px){

    .btn-report{
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
    }

    .user-navbar .navbar-collapse{
        margin-top: 10px;
    }
}
@media (max-width: 576px){

    .page-header{
        padding: 40px 10px;
    }

    .page-header h1{
        font-size: 24px;
    }

    .page-header p{
        font-size: 14px;
    }
}
@media (max-width: 576px){

    .modal-dialog{
        margin: 10px;
    }

    .modal-content{
        border-radius: 12px;
    }

    .issue-detail-value{
        font-size: 14px;
    }
}
html {
    scroll-behavior: smooth;
}

.dashboard-card{
    padding: 20px;
}

@media (max-width: 576px){
    .dashboard-card{
        padding: 15px;
    }
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark user-navbar sticky-top">
  <div class="container">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <i class="fas fa-city me-2"></i>
      <strong>Ludhiana City</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- LEFT LINKS -->
      <ul class="navbar-nav me-auto align-items-center">

        <!-- Highlighted CTA -->
        <li class="nav-item me-2">
          <a class="btn btn-report" href="report.php">
            <i class="fas fa-plus-circle me-1"></i> Report Issue
          </a>
        </li>

        <!-- Normal link -->
        <li class="nav-item">
          <a class="nav-link active" href="dashboard.php">
            <i class="fas fa-chart-line me-1"></i> Dashboard
          </a>
        </li>

      </ul>

      <!-- USER DROPDOWN -->
      <div class="dropdown">
        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           href="#"
           data-bs-toggle="dropdown">

<img
    src="https://ui-avatars.com/api/?name=<?= urlencode($user['full_name'] ?? 'User') ?>&background=0D6EFD&color=fff"
    class="rounded-circle me-2"
    width="34"
    height="34">

<span class="fw-semibold">
    <?= htmlspecialchars($user['full_name'] ?? 'User') ?>
</span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li>
            <a class="dropdown-item text-danger" href="logout.php">
              <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
          </li>
        </ul>
      </div>

    </div>
  </div>
</nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-3">My Dashboard</h1>
            <p class="lead">Track your reported issues and their status</p>
        </div>
    </section>

    <!-- Dashboard Content -->
    <section class="container dashboard-container">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card total">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                   <div class="stats-number"><?= $total ?></div>
                    <div class="stats-label">Total Reports</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card pending">
                    <i class="fas fa-clock fa-2x"></i>
                   <div class="stats-number"><?= $pending ?></div>
                    <div class="stats-label">Pending</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card in-progress">
                    <i class="fas fa-tools fa-2x"></i>
                  <div class="stats-number"><?= $progress ?></div>
                    <div class="stats-label">In Progress</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card resolved">
                    <i class="fas fa-check-circle fa-2x"></i>
                   <div class="stats-number"><?= $resolved ?></div>
                    <div class="stats-label">Resolved</div>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">My Reports</h3>
                <div class="filter-buttons">
                    <button class="btn btn-outline-primary <?= $currentStatus=='all'?'active':'' ?>" data-filter="all">All</button>
                   <button class="btn btn-outline-warning <?= $currentStatus=='pending'?'active':'' ?>" data-filter="pending">Pending</button>
                    <button class="btn btn-outline-info <?= $currentStatus=='in-progress'?'active':'' ?>" data-filter="in-progress">In Progress</button>
					<button class="btn btn-outline-success <?= $currentStatus=='resolved'?'active':'' ?>" data-filter="resolved">Resolved</button>
                </div>
            </div>
            
			  <div id="reportsList">
				<?php if(mysqli_num_rows($complaints) > 0): ?>

					<?php while($row = mysqli_fetch_assoc($complaints)): ?>
						<?php
							// STATUS → CSS CLASS MAP
							$classMap = [
								'Pending'     => 'pending',
								'In Progress' => 'in-progress',
								'Resolved'    => 'resolved'
							];

							$statusClass = $classMap[$row['status']] ?? '';
						?>

<div class="report-card <?= $statusClass ?>">
    <div class="d-flex gap-3">
        <?php if (!empty($row['image'])): ?>
            <div class="report-img-container">
                <img src="uploads/<?php echo $row['image']; ?>" 
                     alt="Issue Image" 
                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
            </div>
        <?php else: ?>
            <div class="report-img-placeholder" 
                 style="width: 80px; height: 80px; background: #f0f2f5; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                <i class="fas fa-image text-muted"></i>
            </div>
        <?php endif; ?>

        <div class="flex-grow-1">
            <div class="report-title">
                <?= htmlspecialchars($row['title']); ?>
            </div>
            <div class="report-location">
                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                <?= htmlspecialchars($row['location']); ?>
            </div>
        </div>
    </div>

    <div class="report-footer d-flex justify-content-between align-items-end">

        <!-- LEFT SIDE -->
        <div>
            <span class="badge bg-<?= $statusClass == 'pending' ? 'warning' : ($statusClass == 'in-progress' ? 'info' : 'success') ?>">
                <?= $row['status'] ?>
            </span>
			<?php if($row['status'] == 'Resolved' && $row['user_confirmed'] == 0): ?>

    <div class="mt-2">
        <button class="btn btn-success btn-sm confirmBtn"
            data-id="<?= $row['id']; ?>"
            data-action="yes">
            👍 Yes, Solved
        </button>

        <button class="btn btn-danger btn-sm confirmBtn"
            data-id="<?= $row['id']; ?>"
            data-action="no">
            👎 Not Solved
        </button>
    </div>

<?php elseif($row['user_confirmed'] == 1): ?>

    <div class="mt-2 text-success fw-semibold">
        ✔ Issue successfully resolved. Thank you for confirming!
    </div>

<?php elseif($row['user_confirmed'] == 2): ?>

    <div class="mt-2 text-danger fw-semibold">
        ❗ Issue re-opened. Admin is working on it again.
    </div>

<?php endif; ?>
        </div>

        <!-- RIGHT SIDE -->
        <div class="text-end">
            
            <!-- 🔵 Ticket Highlight -->
            <div class="ticket-badge mb-1">
                Ticket: UP-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?>
            </div>

            <!-- 🔘 Button -->
            <button class="btn btn-sm btn-outline-primary viewDetailsBtn" data-id="<?= $row['id']; ?>">
                <i class="fas fa-eye me-1"></i> View Details
            </button>

        </div>

    </div>
</div>

					<?php endwhile; ?>

				<?php else: ?>

					<div class="empty-state">
						<i class="fas fa-inbox"></i>
						<h4>No reports found</h4>
						<p>You haven't submitted any reports yet.
							<a href="report.php">Report an issue</a>
						</p>
					</div>

				<?php endif; ?>
			</div>         
        </div>
    </section>

    <!-- Footer -->
    <?php include("footer.php");?>
	<div class="modal fade" id="reportDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Report Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="reportDetailsContent">
        <div class="text-center py-5">
          <div class="spinner-border text-primary"></div>
          <p class="mt-2">Loading details...</p>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ===============================
   FILTER BUTTONS (PHP BASED)
   =============================== */
document.querySelectorAll(".filter-buttons button").forEach(btn => {
    btn.addEventListener("click", () => {
        let status = btn.getAttribute("data-filter");
        window.location.href = "dashboard.php?status=" + status;
    });
});


/* ===============================
   SUCCESS MESSAGE AFTER REPORT SUBMIT
   =============================== */
document.addEventListener("DOMContentLoaded", function(){

    const params = new URLSearchParams(window.location.search);
    const ticket = params.get("ticket");

    if(ticket){

        const ticketEl = document.getElementById("ticketNumber");

        if(ticketEl){
            ticketEl.innerText = ticket.toString().padStart(5,'0');
        }

        const modalEl = document.getElementById("thankyouModal");

        if(modalEl){

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            startConfetti();

            /* 🔔 Play Sound */
            const sound = document.getElementById("successSound");

            if(sound){

                const tryPlay = () => {
                    sound.currentTime = 0;

                    sound.play()
                        .then(() => console.log("Sound Played ✅"))
                        .catch(() => console.log("Autoplay Blocked ❌"));
                };

                tryPlay();
                document.addEventListener("click", tryPlay, { once:true });
            }

            setTimeout(()=>{
                modal.hide();
            }, 6500);
        }

        window.history.replaceState({}, document.title, window.location.pathname);
    }

});



</script>
<script>
function startConfetti(){

    const canvas = document.getElementById("confettiCanvas");
    const ctx = canvas.getContext("2d");

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const pieces = [];

    for(let i=0;i<150;i++){
        pieces.push({
            x: Math.random()*canvas.width,
            y: Math.random()*canvas.height - canvas.height,
            r: Math.random()*6 + 2,
            d: Math.random()*150,
            color: `hsl(${Math.random()*360},100%,50%)`,
            tilt: Math.random()*10 - 10
        });
    }

    function draw(){
        ctx.clearRect(0,0,canvas.width,canvas.height);

        pieces.forEach(p=>{
            ctx.beginPath();
            ctx.fillStyle = p.color;
            ctx.fillRect(p.x, p.y, p.r, p.r);
        });

        update();
    }

    function update(){
        pieces.forEach(p=>{
            p.y += Math.cos(p.d) + 2;
            p.x += Math.sin(p.d);

            if(p.y > canvas.height){
                p.y = -10;
                p.x = Math.random()*canvas.width;
            }
        });
    }

    let confettiInterval = setInterval(draw, 20);

    /* Stop after 3 sec */
    setTimeout(()=>{
        clearInterval(confettiInterval);
        ctx.clearRect(0,0,canvas.width,canvas.height);
    },6000);
}
</script>

<script>
document.querySelectorAll(".viewDetailsBtn").forEach(btn => {
  btn.addEventListener("click", function () {
    let reportId = this.getAttribute("data-id");

    let modal = new bootstrap.Modal(
      document.getElementById("reportDetailsModal")
    );
    modal.show();

    fetch("view_report_ajax.php?id=" + reportId)
      .then(res => res.text())
      .then(data => {
        document.getElementById("reportDetailsContent").innerHTML = data;
      });
  });
});
document.addEventListener("click", function(e){

    if(e.target.classList.contains("confirmBtn")){

        let id = e.target.dataset.id;
        let action = e.target.dataset.action;

        // YES
        if(action === "yes"){
    fetch("user_confirm.php", {
        method: "POST",
        headers: { 
            "Content-Type": "application/x-www-form-urlencoded" 
        },
        body: "id=" + id + "&action=yes"
    })
    .then(res => res.text())
    .then(response => {
        console.log(response); // debug ke liye

        showToast("Marked as solved ✅");

        // page reload so UI update ho jaye
        setTimeout(() => {
            location.reload();
        }, 1000);
    })
    .catch(err => {
        console.error("Error:", err);
    });
}

        // NO
        if(action === "no"){
            document.getElementById("feedbackIssueId").value = id;

            let modal = new bootstrap.Modal(
                document.getElementById("feedbackModal")
            );
            modal.show();
        }

    }

});
// Submit feedback
document.addEventListener("click", function(e){

    if(e.target && e.target.id === "submitFeedback"){

    let id = document.getElementById("feedbackIssueId").value;
    let feedback = document.getElementById("feedbackText").value;
    let rating = document.getElementById("ratingValue").value;

    if(feedback.trim() === ""){
        alert("Please enter feedback!");
        return;
    }

    fetch("user_confirm.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "id=" + id +
              "&action=no" +
              "&feedback=" + encodeURIComponent(feedback) +
              "&rating=" + rating
    })
    .then(res => res.text())
    .then(() => {
        showToast("Feedback submitted 👍");
        location.reload(); // optional
    });
}

});
let selectedRating = 0;

// ⭐ STAR CLICK WORKING FIX
document.addEventListener("click", function(e){

    if(e.target.classList.contains("star")){

        let value = e.target.getAttribute("data-value");

        // hidden input me save
        document.getElementById("ratingValue").value = value;

        // sab stars reset
        let stars = document.querySelectorAll(".star");
        stars.forEach((s, index) => {

            if(index < value){
                s.classList.remove("far");
                s.classList.add("fas");
            } else {
                s.classList.remove("fas");
                s.classList.add("far");
            }

        });
    }
});

function showToast(msg="Success!"){

    const toastEl = document.getElementById("successToast");
    toastEl.querySelector(".toast-body").innerText = msg;

    let toast = new bootstrap.Toast(toastEl);
    toast.show();
}



</script>


<!-- LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Are you sure you want to logout?
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="logout.php" class="btn btn-danger">Yes, Logout</a>
      </div>

    </div>
  </div>
</div>
<!-- THANK YOU MODAL -->
<div class="modal fade" id="thankyouModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg sms-modal">

      <div class="modal-body text-center p-4">

        <div class="success-icon">
          <i class="fas fa-check-circle"></i>
        </div>

        <h4 class="fw-bold mt-2">Complaint Registered</h4>

        <!-- SMS RECEIPT CARD -->
        <div class="sms-card mt-3">

          <div class="sms-header">
            Urban Pulse System
          </div>

          <div class="sms-body">

            <p>Dear Citizen,</p>

            <p>
              Thank you for reporting the issue.
              Your complaint has been successfully registered.
            </p>

            <p class="mb-1">
              <strong>Reference No:</strong>
            </p>

            <div class="ticket-box">
              <span id="ticketNumber"></span>
            </div>

            <p class="sms-footer">
              We will keep you updated on the progress.
            </p>

          </div>
        </div>

      </div>

    </div>
  </div>
</div>

<canvas id="confettiCanvas"></canvas>
<audio id="successSound" preload="auto">
   <source src="assets/notification.mp3" type="audio/mpeg">
</audio>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Rate & Feedback</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- ⭐ STAR RATING -->
        <div class="text-center mb-3">
          <div id="starRating" class="fs-3 text-warning">
            <i class="far fa-star star" data-value="1"></i>
            <i class="far fa-star star" data-value="2"></i>
            <i class="far fa-star star" data-value="3"></i>
            <i class="far fa-star star" data-value="4"></i>
            <i class="far fa-star star" data-value="5"></i>
          </div>
          <small class="text-muted">Tap to rate</small>
        </div>

        <textarea id="feedbackText" class="form-control"
          placeholder="Explain why problem is not solved..."
          rows="3"></textarea>

        <input type="hidden" id="feedbackIssueId">
        <input type="hidden" id="ratingValue" value="0">

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger" id="submitFeedback">Submit</button>
      </div>

    </div>
  </div>
</div>


        <!-- TOAST SUCCESS MESSAGE -->

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
  <div id="successToast" class="toast align-items-center text-bg-success border-0">
    <div class="d-flex">
      <div class="toast-body">
        ✅ Action completed successfully!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
        data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
</body>
</html>