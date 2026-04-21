<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urban Problem Management - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Urban-themed CSS -->
    <style>
        :root {
            --primary-color: #2C3E50;
            --secondary-color: #E74C3C;
            --accent-color: #3498DB;
            --success-color: #27AE60;
            --warning-color: #F39C12;
            --light-color: #ECF0F1;
            --dark-color: #2C3E50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        /* Urban-themed sidebar */
        .sidebar {
            background: linear-gradient(to bottom, var(--primary-color), #1a2530);
            color: white;
            min-height: 100vh;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header h3 {
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        
        .sidebar-header h3 span {
            color: var(--secondary-color);
        }
        
        .sidebar-header .tagline {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(52, 152, 219, 0.2);
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 25px;
            font-size: 1.2rem;
        }
        
        /* Main content styling */
        .main-content {
            padding: 25px 30px;
            background-color: #f8f9fa;
        }
        
        /* Card styling */
        .card {
            border-radius: 12px;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-7px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 3px solid var(--accent-color);
            font-weight: 700;
            padding: 18px 25px;
            border-radius: 12px 12px 0 0 !important;
            color: var(--primary-color);
        }
        
        /* Stat cards */
        .stat-card {
            border-top: 5px solid var(--accent-color);
            border-radius: 12px;
        }
        
        .stat-card i {
            font-size: 2.8rem;
            opacity: 0.9;
        }
        
        .stat-card.traffic i { color: var(--warning-color); }
        .stat-card.waste i { color: var(--success-color); }
        .stat-card.housing i { color: #9B59B6; }
        .stat-card.infrastructure i { color: var(--accent-color); }
        
        /* Button styling */
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            font-weight: 600;
            padding: 10px 20px;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-danger {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            font-weight: 600;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        /* Badge styling */
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        /* Table styling */
        .table th {
            border-top: none;
            background-color: rgba(44, 62, 80, 0.05);
            color: var(--primary-color);
            font-weight: 700;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        /* Progress bars */
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        
        /* Map placeholder */
        .map-container {
            height: 300px;
            background: linear-gradient(to bottom right, #74b9ff, #0984e3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .map-container::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(to right, rgba(255,255,255,0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        
        /* Activity timeline */
        .activity-item {
            border-left: 3px solid var(--accent-color);
            padding-left: 20px;
            margin-bottom: 25px;
            position: relative;
        }
        
        .activity-item::before {
            content: "";
            position: absolute;
            left: -8px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: var(--accent-color);
        }
        
        /* Issue priority indicators */
        .priority-high { border-left: 5px solid var(--secondary-color); }
        .priority-medium { border-left: 5px solid var(--warning-color); }
        .priority-low { border-left: 5px solid var(--success-color); }
        
        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            margin-top: 40px;
            border-radius: 10px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                margin-bottom: 20px;
            }
            
            .main-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            
<!-- Urban-themed Sidebar -->
            <div class="col-lg-3 col-xl-2 px-0 sidebar">
                <div class="sidebar-header">
                    <h3>Urban<span>Solve</span></h3>
                    <p class="tagline">City Problem Management System</p>
                </div>
                
               <ul class="nav flex-column mt-4">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="bi bi-list-ul"></i> Categories
                        </a>
                    </li>
                    <!--
                    <li class="nav-item">
                        <a class="nav-link" href="location.php">
                            <i class="bi bi-geo-alt"></i> Locations
                        </a>
                    </li>
					-->
                    <li class="nav-item">
                        <a class="nav-link" href="citizens.php">
                            <i class="bi bi-people"></i> Citizens
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="bi bi-bar-chart"></i> Reports
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <!--
                        <a class="nav-link" href="setting.php">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                        -->
						<div class="mt-auto p-3 border-top">
						  <form action="logout.php" method="POST">
							<button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#logoutModal">
						  <i class="fas fa-sign-out-alt me-2"></i> Logout
						</button>
						  </form>
						</div>


                    </li>
            
                </ul>               
            </div>

