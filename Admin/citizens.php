<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); exit();
}
include("db.php"); 

// 1. Total Citizens
$totalCitizens = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM members"))['c'] ?? 0;

// 2. Active Citizens (Citizens who have submitted at least 1 report)
$activeRes = mysqli_query($conn,"SELECT COUNT(DISTINCT user_id) as c FROM issues");
$activeCitizens = mysqli_fetch_assoc($activeRes)['c'] ?? 0;

// 3. Total Reports
$totalReports = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM issues"))['c'] ?? 0;

// 4. Avg Engagement
$avgEngagement = ($totalCitizens > 0) ? round($totalReports / $totalCitizens, 1) : 0;

// Get Member List with Report Count
$members = [];
$q = mysqli_query($conn,"
    SELECT m.*, COUNT(i.id) AS total_reports 
    FROM members m 
    LEFT JOIN issues i ON i.user_id = m.id 
    GROUP BY m.id 
    ORDER BY m.id DESC
");

while($r = mysqli_fetch_assoc($q)){
    $members[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Citizens | UrbanSolve</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <style>
       body{
    background:#f4f6f9;
    font-family:'Poppins',sans-serif;
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
    margin-top:4px;
}

/* Avatar */
.avatar{
    width:38px;
    height:38px;
    border-radius:12px;
    background:linear-gradient(135deg,#0d6efd,#4f9cff);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:15px;
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

    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include_once("includes/leftnav.php"); ?>

        <div class="col-lg-9 col-xl-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-primary">Citizen Management</h2>
                    <p class="text-muted mb-0">Registered users & reporting activity</p>
                </div>
                <a href="add_citizens.php" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Add Citizen
                </a>
            </div>

            <div class="row g-3 mb-4">

    <div class="col-md-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#0d6efd,#4f9cff);">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <div class="kpi-title">TOTAL CITIZENS</div>
                    <div class="kpi-value"><?= $totalCitizens ?></div>
                </div>
                <div><i class="fa-solid fa-users fa-lg opacity-75"></i></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#198754,#2bb673);">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <div class="kpi-title">ACTIVE CITIZENS</div>
                    <div class="kpi-value"><?= $activeCitizens ?></div>
                </div>
                <div><i class="fa-solid fa-user-check fa-lg opacity-75"></i></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#0dcaf0,#31d2f2);">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <div class="kpi-title">TOTAL REPORTS</div>
                    <div class="kpi-value"><?= $totalReports ?></div>
                </div>
                <div><i class="fa-solid fa-file-lines fa-lg opacity-75"></i></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card kpi-card" style="background:linear-gradient(135deg,#ffc107,#ffb300);">
            <div class="card-body d-flex justify-content-between">
                <div>
                    <div class="kpi-title">AVG ENGAGEMENT</div>
                    <div class="kpi-value"><?= $avgEngagement ?></div>
                </div>
                <div><i class="fa-solid fa-chart-line fa-lg opacity-75"></i></div>
            </div>
        </div>
    </div>

</div>


            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table id="citizensTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Citizen</th>
                                <th>Phone</th>
                                <th>Reports</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sr=1; foreach($members as $c){ 
                                $name = !empty($c['full_name']) ? $c['full_name'] : 'Unknown';
                                $regDate = (!empty($c['created_at']) && $c['created_at'] != '0000-00-00 00:00:00') 
                                           ? date("d M Y", strtotime($c['created_at'])) 
                                           : "Not Available";
                                $isActive = ($c['total_reports'] > 0);
                            ?>
                            <tr>
                                <td><?= $sr++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar"><?= substr($name, 0, 1) ?></div>
                                        <div>
                                            <strong class="d-block text-dark"><?= htmlspecialchars($name) ?></strong>
                                            <small class="text-muted"><?= htmlspecialchars($c['email'] ?? '') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($c['mobile'] ?: '—') ?></td>
                                <td>
									<?php if($c['total_reports'] > 0): ?>
<a href="reports.php?user_id=<?= (int)$c['id']; ?>"
   class="badge bg-info badge-pill text-decoration-none">
    <?= (int)$c['total_reports']; ?> Reports
</a>

									<?php else: ?>
										<span class="badge bg-secondary">0 Reports</span>
									<?php endif; ?>
								</td>
                                <td>
                                    <span class="badge <?= $isActive ? 'bg-success' : 'bg-secondary' ?> badge-pill">
    <?= $isActive ? 'Active' : 'Inactive' ?>
</span>

                                </td>
                                <td><small class="text-muted"><?= $regDate ?></small></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include_once("includes/footer.php"); ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
    $('#citizensTable').DataTable({
        pageLength:10,
        order:[[0,'asc']]
    });
});
</script>
</body>
</html>