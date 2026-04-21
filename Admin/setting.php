<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("db.php");

/* ======================
   FETCH SETTINGS
====================== */
$settings = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM admin_settings LIMIT 1")
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Urban Problem Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
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
        body.dark-mode{
    background:#0f172a;
    color:#e5e7eb;
}

body.dark-mode .card,
body.dark-mode .form-section{
    background:#1e293b;
    color:#e5e7eb;
}

body.dark-mode .table{
    color:#e5e7eb;
}

body.dark-mode .table thead th{
    background:#020617;
    color:#e5e7eb;
}

body.dark-mode .sidebar{
    background:linear-gradient(to bottom,#020617,#020617);
}

body.dark-mode .nav-link{
    color:#cbd5f5;
}

body.dark-mode .nav-link.active{
    background:#1e40af;
}

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        /* Sidebar */
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
        
        /* Main Content */
        .main-content {
            padding: 25px 30px;
            background-color: #f8f9fa;
        }
        
        /* Card Styling */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 3px solid var(--accent-color);
            font-weight: 700;
            padding: 18px 25px;
            border-radius: 12px 12px 0 0 !important;
            color: var(--primary-color);
        }
        
        /* Settings Cards */
        .settings-card {
            border-left: 5px solid;
            transition: all 0.3s;
            height: 100%;
        }
        
        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .settings-card.system { border-left-color: var(--primary-color); }
        .settings-card.user { border-left-color: var(--accent-color); }
        .settings-card.notification { border-left-color: var(--warning-color); }
        .settings-card.integration { border-left-color: var(--success-color); }
        
        /* Settings Navigation */
        .settings-nav {
            position: sticky;
            top: 20px;
        }
        
        .settings-nav .nav-link {
            color: var(--dark-color);
            border-left: 3px solid transparent;
            border-radius: 0;
            padding: 12px 15px;
            margin-bottom: 5px;
        }
        
        .settings-nav .nav-link:hover, .settings-nav .nav-link.active {
            background-color: rgba(52, 152, 219, 0.1);
            border-left-color: var(--accent-color);
            transform: none;
        }
        
        /* Form Styling */
        .form-section {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }
        
        .form-section h5 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        /* Toggle Switch */
        .form-check.form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
        }
        
        .form-check.form-switch .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        /* Badges */
        .badge-pill {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        /* Table Styling */
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
        
        /* API Keys */
        .api-key {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        /* Color Picker */
        .color-picker {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 2px solid #dee2e6;
            cursor: pointer;
        }
        
        /* Log Entries */
        .log-entry {
            padding: 10px 15px;
            border-left: 3px solid #dee2e6;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 0 6px 6px 0;
        }
        
        .log-entry.info { border-left-color: var(--accent-color); }
        .log-entry.warning { border-left-color: var(--warning-color); }
        .log-entry.error { border-left-color: var(--secondary-color); }
        .log-entry.success { border-left-color: var(--success-color); }
        
        /* Backup Cards */
        .backup-card {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .backup-card:hover {
            border-color: var(--accent-color);
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        /* Modal Customization */
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                margin-bottom: 20px;
            }
            
            .main-content {
                padding: 15px;
            }
            
            .settings-nav {
                position: static;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
<?php include_once("includes/leftnav.php"); ?>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10 main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-primary">System Settings</h2>
                        <p class="text-muted mb-0">Configure and customize your urban problem management system</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="saveAllSettingsBtn">
                            <i class="bi bi-check-circle me-2"></i> Save All Changes
                        </button>
                        <button class="btn btn-outline-secondary ms-2" id="resetSettingsBtn">
                            <i class="bi bi-arrow-clockwise me-2"></i> Reset to Defaults
                        </button>
                    </div>
                </div>
                
                <!-- Settings Overview -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card settings-card system">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-cpu fs-1 text-primary"></i>
                                </div>
                                <h5 class="card-title">System</h5>
                                <p class="text-muted small">General configuration and system preferences</p>
                                <span class="badge bg-primary">12 Settings</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card settings-card user">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-people fs-1 text-info"></i>
                                </div>
                                <h5 class="card-title">Users & Roles</h5>
                                <p class="text-muted small">User management and permission controls</p>
                                <span class="badge bg-info">8 Settings</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card settings-card notification">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-bell fs-1 text-warning"></i>
                                </div>
                                <h5 class="card-title">Notifications</h5>
                                <p class="text-muted small">Alerts, emails, and communication settings</p>
                                <span class="badge bg-warning">15 Settings</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <div class="card settings-card integration">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="bi bi-plug fs-1 text-success"></i>
                                </div>
                                <h5 class="card-title">Integrations</h5>
                                <p class="text-muted small">API, third-party services, and data sync</p>
                                <span class="badge bg-success">6 Settings</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Settings Content -->
                <div class="row">
                    <div class="col-lg-3">
                        <!-- Settings Navigation -->
                        <div class="card settings-nav">
                            <div class="card-body p-0">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#" data-section="general">
                                            <i class="bi bi-gear me-2"></i> General Settings
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="appearance">
                                            <i class="bi bi-palette me-2"></i> Appearance
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="users">
                                            <i class="bi bi-people me-2"></i> Users & Roles
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="notifications">
                                            <i class="bi bi-bell me-2"></i> Notifications
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="integrations">
                                            <i class="bi bi-plug me-2"></i> Integrations
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="api">
                                            <i class="bi bi-key me-2"></i> API Settings
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="backup">
                                            <i class="bi bi-download me-2"></i> Backup & Restore
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="logs">
                                            <i class="bi bi-journal-text me-2"></i> System Logs
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-section="advanced">
                                            <i class="bi bi-tools me-2"></i> Advanced
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- System Status -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">System Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>System Health</small>
                                        <small class="text-success">Good</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 92%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Storage Usage</small>
                                        <small class="text-warning">68%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" style="width: 68%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Uptime</small>
                                        <small class="text-success">99.8%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: 99%"></div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button class="btn btn-sm btn-outline-primary w-100" id="systemCheckBtn">
                                        <i class="bi bi-arrow-repeat me-1"></i> Run System Check
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-9">
                        <!-- General Settings (Default Visible) -->
                        <div class="form-section" id="general-section">
                            <h5><i class="bi bi-gear me-2"></i> General Settings</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="systemName" class="form-label">System Name</label>
                                    <input type="text" class="form-control" id="systemName" value="UrbanSolve Problem Management">
                                    <small class="text-muted">Display name for your system</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="systemUrl" class="form-label">System URL</label>
                                    <input type="url" class="form-control" id="systemUrl" value="https://urbansolve.example.com">
                                    <small class="text-muted">Base URL for your application</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone">
                                        <option value="UTC" selected>UTC (Coordinated Universal Time)</option>
                                        <option value="EST">Eastern Time (EST)</option>
                                        <option value="PST">Pacific Time (PST)</option>
                                        <option value="CET">Central European Time (CET)</option>
                                        <option value="IST">Indian Standard Time (IST)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dateFormat" class="form-label">Date Format</label>
                                    <select class="form-select" id="dateFormat">
                                        <option value="MM/DD/YYYY">MM/DD/YYYY (US Format)</option>
                                        <option value="DD/MM/YYYY" selected>DD/MM/YYYY (International)</option>
                                        <option value="YYYY-MM-DD">YYYY-MM-DD (ISO Format)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="defaultLanguage" class="form-label">Default Language</label>
                                    <select class="form-select" id="defaultLanguage">
                                        <option value="en" selected>English</option>
                                        <option value="es">Spanish</option>
                                        <option value="fr">French</option>
                                        <option value="de">German</option>
                                        <option value="hi">Hindi</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="itemsPerPage" class="form-label">Items Per Page</label>
                                    <select class="form-select" id="itemsPerPage">
                                        <option value="10">10 items</option>
                                        <option value="25" selected>25 items</option>
                                        <option value="50">50 items</option>
                                        <option value="100">100 items</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenanceMode" checked>
                                    <label class="form-check-label" for="maintenanceMode">
                                        Enable maintenance mode
                                        <small class="text-muted d-block">When enabled, only administrators can access the system</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="autoSave" checked>
                                    <label class="form-check-label" for="autoSave">
                                        Enable auto-save
                                        <small class="text-muted d-block">Automatically save form data every 30 seconds</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="twoFactorAuth" checked>
                                    <label class="form-check-label" for="twoFactorAuth">
                                        Require two-factor authentication for admins
                                        <small class="text-muted d-block">Adds an extra layer of security for administrator accounts</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Appearance Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="appearance-section">
                            <h5><i class="bi bi-palette me-2"></i> Appearance Settings</h5>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Primary Color</label>
                                    <div class="d-flex align-items-center">
                                        <div class="color-picker me-3" style="background-color: #2C3E50;" data-color="#2C3E50"></div>
                                        <div class="color-picker me-3" style="background-color: #3498DB;" data-color="#3498DB"></div>
                                        <div class="color-picker me-3" style="background-color: #E74C3C;" data-color="#E74C3C"></div>
                                        <div class="color-picker me-3" style="background-color: #27AE60;" data-color="#27AE60"></div>
                                        <div class="color-picker" style="background-color: #9B59B6;" data-color="#9B59B6"></div>
                                    </div>
                                    <small class="text-muted">Click to select primary color scheme</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Theme</label>
                                    <div class="d-flex">
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="radio" name="theme" id="themeLight" checked>
                                            <label class="form-check-label" for="themeLight">
                                                <i class="bi bi-sun me-1"></i> Light
                                            </label>
                                        </div>
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="radio" name="theme" id="themeDark">
                                            <label class="form-check-label" for="themeDark">
                                                <i class="bi bi-moon me-1"></i> Dark
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" id="themeAuto">
                                            <label class="form-check-label" for="themeAuto">
                                                <i class="bi bi-circle-half me-1"></i> Auto
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="logoUpload" class="form-label">System Logo</label>
                                    <input type="file" class="form-control" id="logoUpload" accept="image/*">
                                    <small class="text-muted">Recommended size: 200x60 pixels</small>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-secondary" id="resetLogoBtn">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Reset to Default
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="faviconUpload" class="form-label">Favicon</label>
                                    <input type="file" class="form-control" id="faviconUpload" accept="image/*">
                                    <small class="text-muted">Recommended size: 32x32 pixels</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customCSS" class="form-label">Custom CSS</label>
                                <textarea class="form-control" id="customCSS" rows="4" placeholder="Add custom CSS styles here..."></textarea>
                                <small class="text-muted">Custom styles will be applied to the entire system</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enableAnimations" checked>
                                    <label class="form-check-label" for="enableAnimations">
                                        Enable animations
                                        <small class="text-muted d-block">Smooth transitions and animations throughout the interface</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Users & Roles Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="users-section">
                            <h5><i class="bi bi-people me-2"></i> Users & Roles Management</h5>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>User Roles & Permissions</h6>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                                        <i class="bi bi-plus-circle me-1"></i> Add New Role
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Role Name</th>
                                                <th>Users</th>
                                                <th>Permissions</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Super Administrator</div>
                                                    <small class="text-muted">Full system access</small>
                                                </td>
                                                <td>3</td>
                                                <td>
                                                    <span class="badge bg-primary me-1">All</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                                                    <button class="btn btn-sm btn-outline-secondary ms-1" disabled>Delete</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Administrator</div>
                                                    <small class="text-muted">Manage system operations</small>
                                                </td>
                                                <td>7</td>
                                                <td>
                                                    <span class="badge bg-info me-1">Manage Issues</span>
                                                    <span class="badge bg-info me-1">Manage Users</span>
                                                    <span class="badge bg-info">View Reports</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger ms-1">Delete</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Issue Manager</div>
                                                    <small class="text-muted">Handle issue resolution</small>
                                                </td>
                                                <td>15</td>
                                                <td>
                                                    <span class="badge bg-info me-1">Manage Issues</span>
                                                    <span class="badge bg-info">View Reports</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger ms-1">Delete</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Viewer</div>
                                                    <small class="text-muted">Read-only access</small>
                                                </td>
                                                <td>24</td>
                                                <td>
                                                    <span class="badge bg-secondary me-1">View Issues</span>
                                                    <span class="badge bg-secondary">View Reports</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger ms-1">Delete</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userRegistration" class="form-label">User Registration</label>
                                <select class="form-select" id="userRegistration">
                                    <option value="open">Open Registration</option>
                                    <option value="invite" selected>Invite Only</option>
                                    <option value="admin">Admin Approval Required</option>
                                    <option value="closed">Closed Registration</option>
                                </select>
                                <small class="text-muted">Control how new users can register for the system</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="emailVerification" checked>
                                    <label class="form-check-label" for="emailVerification">
                                        Require email verification
                                        <small class="text-muted d-block">Users must verify their email address before accessing the system</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="passwordPolicy" checked>
                                    <label class="form-check-label" for="passwordPolicy">
                                        Enforce strong password policy
                                        <small class="text-muted d-block">Require passwords with minimum 8 characters including uppercase, lowercase, and numbers</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="sessionTimeout" class="form-label">Session Timeout</label>
                                <select class="form-select" id="sessionTimeout">
                                    <option value="15">15 minutes</option>
                                    <option value="30">30 minutes</option>
                                    <option value="60" selected>1 hour</option>
                                    <option value="120">2 hours</option>
                                    <option value="240">4 hours</option>
                                    <option value="0">Never timeout</option>
                                </select>
                                <small class="text-muted">Automatic logout after inactivity</small>
                            </div>
                        </div>
                        
                        <!-- Notifications Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="notifications-section">
                            <h5><i class="bi bi-bell me-2"></i> Notification Settings</h5>
                            
                            <div class="mb-4">
                                <h6>Notification Channels</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                            <label class="form-check-label" for="emailNotifications">
                                                <i class="bi bi-envelope me-2"></i> Email Notifications
                                                <small class="text-muted d-block">Send notifications via email</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="pushNotifications" checked>
                                            <label class="form-check-label" for="pushNotifications">
                                                <i class="bi bi-bell me-2"></i> Push Notifications
                                                <small class="text-muted d-block">Browser and mobile push notifications</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="smsNotifications">
                                            <label class="form-check-label" for="smsNotifications">
                                                <i class="bi bi-chat-dots me-2"></i> SMS Notifications
                                                <small class="text-muted d-block">Text message notifications</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="inAppNotifications" checked>
                                            <label class="form-check-label" for="inAppNotifications">
                                                <i class="bi bi-app-indicator me-2"></i> In-App Notifications
                                                <small class="text-muted d-block">Notifications within the application</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Notification Types</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="newIssueNotifications" checked>
                                            <label class="form-check-label" for="newIssueNotifications">
                                                New Issue Reports
                                                <small class="text-muted d-block">Notify when new issues are reported</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="issueUpdateNotifications" checked>
                                            <label class="form-check-label" for="issueUpdateNotifications">
                                                Issue Status Updates
                                                <small class="text-muted d-block">Notify when issue status changes</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="assignmentNotifications" checked>
                                            <label class="form-check-label" for="assignmentNotifications">
                                                Assignment Notifications
                                                <small class="text-muted d-block">Notify when assigned to new issues</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="systemNotifications" checked>
                                            <label class="form-check-label" for="systemNotifications">
                                                System Alerts
                                                <small class="text-muted d-block">System maintenance and updates</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notificationEmail" class="form-label">Default Notification Email</label>
                                <input type="email" class="form-control" id="notificationEmail" value="notifications@urbansolve.example.com">
                                <small class="text-muted">Email address used for sending system notifications</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notificationSchedule" class="form-label">Notification Schedule</label>
                                <select class="form-select" id="notificationSchedule">
                                    <option value="immediate" selected>Immediate</option>
                                    <option value="hourly">Hourly Digest</option>
                                    <option value="daily">Daily Digest</option>
                                    <option value="weekly">Weekly Summary</option>
                                </select>
                                <small class="text-muted">How often to send non-urgent notifications</small>
                            </div>
                        </div>
                        
                        <!-- Integration Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="integrations-section">
                            <h5><i class="bi bi-plug me-2"></i> Integration Settings</h5>
                            
                            <div class="mb-4">
                                <h6>Third-Party Integrations</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-google me-2 text-danger"></i> Google Maps
                                                    </h6>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="googleMapsIntegration" checked>
                                                    </div>
                                                </div>
                                                <p class="text-muted small mb-0">Integration for location mapping and geocoding</p>
                                                <button class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#googleMapsModal">
                                                    Configure
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-twitter me-2 text-info"></i> Twitter API
                                                    </h6>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="twitterIntegration">
                                                    </div>
                                                </div>
                                                <p class="text-muted small mb-0">Post updates and receive reports via Twitter</p>
                                                <button class="btn btn-sm btn-outline-primary mt-2">
                                                    Configure
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-envelope me-2 text-primary"></i> Email Service
                                                    </h6>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="emailServiceIntegration" checked>
                                                    </div>
                                                </div>
                                                <p class="text-muted small mb-0">SMTP configuration for sending emails</p>
                                                <button class="btn btn-sm btn-outline-primary mt-2">
                                                    Configure
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">
                                                        <i class="bi bi-cloud me-2 text-success"></i> Cloud Storage
                                                    </h6>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="cloudStorageIntegration" checked>
                                                    </div>
                                                </div>
                                                <p class="text-muted small mb-0">Store attachments and backups in cloud storage</p>
                                                <button class="btn btn-sm btn-outline-primary mt-2">
                                                    Configure
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dataSyncFrequency" class="form-label">Data Sync Frequency</label>
                                <select class="form-select" id="dataSyncFrequency">
                                    <option value="realtime">Real-time</option>
                                    <option value="hourly" selected>Hourly</option>
                                    <option value="daily">Daily</option>
                                    <option value="manual">Manual Only</option>
                                </select>
                                <small class="text-muted">How often to sync data with external services</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="autoUpdateIntegrations" checked>
                                    <label class="form-check-label" for="autoUpdateIntegrations">
                                        Auto-update integrations
                                        <small class="text-muted d-block">Automatically update integration plugins when available</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- API Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="api-section">
                            <h5><i class="bi bi-key me-2"></i> API Settings</h5>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>API Keys</h6>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generateApiKeyModal">
                                        <i class="bi bi-plus-circle me-1"></i> Generate New Key
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Key Name</th>
                                                <th>API Key</th>
                                                <th>Permissions</th>
                                                <th>Created</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Mobile App</div>
                                                    <small class="text-muted">For mobile application</small>
                                                </td>
                                                <td>
                                                    <code class="api-key">sk_live_abc123xyz789...</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info me-1">Read</span>
                                                    <span class="badge bg-info">Write</span>
                                                </td>
                                                <td>2023-09-15</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Copy</button>
                                                    <button class="btn btn-sm btn-outline-danger ms-1">Revoke</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Reporting Service</div>
                                                    <small class="text-muted">For automated reports</small>
                                                </td>
                                                <td>
                                                    <code class="api-key">sk_live_def456uvw890...</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info me-1">Read</span>
                                                </td>
                                                <td>2023-10-01</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Copy</button>
                                                    <button class="btn btn-sm btn-outline-danger ms-1">Revoke</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Webhook Integration</div>
                                                    <small class="text-muted">For third-party services</small>
                                                </td>
                                                <td>
                                                    <code class="api-key">sk_live_ghi789rst012...</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info me-1">Read</span>
                                                    <span class="badge bg-info">Write</span>
                                                </td>
                                                <td>2023-08-20</td>
                                                <td><span class="badge bg-secondary">Inactive</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Copy</button>
                                                    <button class="btn btn-sm btn-outline-success ms-1">Activate</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="apiRateLimit" class="form-label">API Rate Limit</label>
                                <select class="form-select" id="apiRateLimit">
                                    <option value="100">100 requests/hour</option>
                                    <option value="500">500 requests/hour</option>
                                    <option value="1000" selected>1000 requests/hour</option>
                                    <option value="5000">5000 requests/hour</option>
                                    <option value="unlimited">Unlimited</option>
                                </select>
                                <small class="text-muted">Maximum API requests per hour per key</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="apiLogging" checked>
                                    <label class="form-check-label" for="apiLogging">
                                        Enable API logging
                                        <small class="text-muted d-block">Log all API requests for security and debugging</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="apiDocs" checked>
                                    <label class="form-check-label" for="apiDocs">
                                        Enable API documentation
                                        <small class="text-muted d-block">Make API documentation publicly accessible</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backup & Restore Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="backup-section">
                            <h5><i class="bi bi-download me-2"></i> Backup & Restore Settings</h5>
                            
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <div class="backup-card">
                                        <i class="bi bi-cloud-arrow-up fs-1 text-primary mb-3"></i>
                                        <h5>Create Backup</h5>
                                        <p class="text-muted small">Create a complete backup of the system</p>
                                        <button class="btn btn-primary" id="createBackupBtn">
                                            <i class="bi bi-plus-circle me-1"></i> Create Backup Now
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="backup-card">
                                        <i class="bi bi-cloud-arrow-down fs-1 text-success mb-3"></i>
                                        <h5>Restore Backup</h5>
                                        <p class="text-muted small">Restore system from a previous backup</p>
                                        <button class="btn btn-success" id="restoreBackupBtn">
                                            <i class="bi bi-download me-1"></i> Restore from Backup
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Recent Backups</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Backup Name</th>
                                                <th>Date</th>
                                                <th>Size</th>
                                                <th>Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">System Backup - Oct 31</div>
                                                    <small class="text-muted">Complete system backup</small>
                                                </td>
                                                <td>2023-10-31 23:59</td>
                                                <td>245 MB</td>
                                                <td><span class="badge bg-primary">Full</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Download</button>
                                                    <button class="btn btn-sm btn-outline-success ms-1">Restore</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">Database Only - Oct 30</div>
                                                    <small class="text-muted">Database backup only</small>
                                                </td>
                                                <td>2023-10-30 02:00</td>
                                                <td>128 MB</td>
                                                <td><span class="badge bg-info">Database</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Download</button>
                                                    <button class="btn btn-sm btn-outline-success ms-1">Restore</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">System Backup - Oct 29</div>
                                                    <small class="text-muted">Complete system backup</small>
                                                </td>
                                                <td>2023-10-29 23:59</td>
                                                <td>242 MB</td>
                                                <td><span class="badge bg-primary">Full</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary">Download</button>
                                                    <button class="btn btn-sm btn-outline-success ms-1">Restore</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="backupFrequency" class="form-label">Backup Frequency</label>
                                <select class="form-select" id="backupFrequency">
                                    <option value="daily" selected>Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="manual">Manual Only</option>
                                </select>
                                <small class="text-muted">How often to create automatic backups</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="cloudBackup" checked>
                                    <label class="form-check-label" for="cloudBackup">
                                        Enable cloud backup
                                        <small class="text-muted d-block">Automatically upload backups to cloud storage</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="backupRetention" class="form-label">Backup Retention</label>
                                <select class="form-select" id="backupRetention">
                                    <option value="7">7 days</option>
                                    <option value="30" selected>30 days</option>
                                    <option value="90">90 days</option>
                                    <option value="365">1 year</option>
                                    <option value="forever">Keep Forever</option>
                                </select>
                                <small class="text-muted">How long to keep backup files</small>
                            </div>
                        </div>
                        
                        <!-- System Logs (Hidden by Default) -->
                        <div class="form-section d-none" id="logs-section">
                            <h5><i class="bi bi-journal-text me-2"></i> System Logs</h5>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Recent System Activity</h6>
                                    <button class="btn btn-sm btn-outline-secondary" id="clearLogsBtn">
                                        <i class="bi bi-trash me-1"></i> Clear All Logs
                                    </button>
                                </div>
                                
                                <div class="log-entries">
                                    <div class="log-entry info">
                                        <div class="d-flex justify-content-between">
                                            <strong>System Backup Completed</strong>
                                            <small class="text-muted">Today, 02:00 AM</small>
                                        </div>
                                        <p class="mb-0">Automated system backup completed successfully. Size: 245 MB</p>
                                    </div>
                                    
                                    <div class="log-entry success">
                                        <div class="d-flex justify-content-between">
                                            <strong>User Login</strong>
                                            <small class="text-muted">Today, 01:45 AM</small>
                                        </div>
                                        <p class="mb-0">Administrator "John Doe" logged in from IP 192.168.1.100</p>
                                    </div>
                                    
                                    <div class="log-entry warning">
                                        <div class="d-flex justify-content-between">
                                            <strong>API Rate Limit Warning</strong>
                                            <small class="text-muted">Yesterday, 23:30 PM</small>
                                        </div>
                                        <p class="mb-0">API key "Mobile App" reached 80% of rate limit (800/1000 requests)</p>
                                    </div>
                                    
                                    <div class="log-entry error">
                                        <div class="d-flex justify-content-between">
                                            <strong>Email Service Error</strong>
                                            <small class="text-muted">Yesterday, 22:15 PM</small>
                                        </div>
                                        <p class="mb-0">Failed to send notification email: SMTP connection timeout</p>
                                    </div>
                                    
                                    <div class="log-entry info">
                                        <div class="d-flex justify-content-between">
                                            <strong>New Issue Reported</strong>
                                            <small class="text-muted">Yesterday, 20:45 PM</small>
                                        </div>
                                        <p class="mb-0">Citizen "Jane Smith" reported issue #URB-1024 (Street Light Problem)</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="logLevel" class="form-label">Logging Level</label>
                                <select class="form-select" id="logLevel">
                                    <option value="error">Errors Only</option>
                                    <option value="warning" selected>Warnings & Errors</option>
                                    <option value="info">All Information</option>
                                    <option value="debug">Debug (All Details)</option>
                                </select>
                                <small class="text-muted">Level of detail to include in system logs</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="logToFile" checked>
                                    <label class="form-check-label" for="logToFile">
                                        Save logs to file
                                        <small class="text-muted d-block">Store logs in files for long-term retention</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="logToDatabase" checked>
                                    <label class="form-check-label" for="logToDatabase">
                                        Save logs to database
                                        <small class="text-muted d-block">Store logs in database for easy querying</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="logRetention" class="form-label">Log Retention Period</label>
                                <select class="form-select" id="logRetention">
                                    <option value="7">7 days</option>
                                    <option value="30" selected>30 days</option>
                                    <option value="90">90 days</option>
                                    <option value="365">1 year</option>
                                </select>
                                <small class="text-muted">How long to keep log entries before automatic deletion</small>
                            </div>
                        </div>
                        
                        <!-- Advanced Settings (Hidden by Default) -->
                        <div class="form-section d-none" id="advanced-section">
                            <h5><i class="bi bi-tools me-2"></i> Advanced Settings</h5>
                            
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Warning:</strong> These settings are for advanced users only. Incorrect configuration may cause system instability.
                            </div>
                            
                            <div class="mb-3">
                                <label for="cacheStrategy" class="form-label">Cache Strategy</label>
                                <select class="form-select" id="cacheStrategy">
                                    <option value="none">No Caching</option>
                                    <option value="memory" selected>Memory Caching</option>
                                    <option value="redis">Redis Caching</option>
                                    <option value="file">File-based Caching</option>
                                </select>
                                <small class="text-muted">Caching strategy for improved performance</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cacheDuration" class="form-label">Cache Duration</label>
                                <select class="form-select" id="cacheDuration">
                                    <option value="60">1 minute</option>
                                    <option value="300">5 minutes</option>
                                    <option value="900" selected>15 minutes</option>
                                    <option value="3600">1 hour</option>
                                    <option value="86400">24 hours</option>
                                </select>
                                <small class="text-muted">How long to keep data in cache</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="maxUploadSize" class="form-label">Maximum Upload Size</label>
                                <select class="form-select" id="maxUploadSize">
                                    <option value="2">2 MB</option>
                                    <option value="5">5 MB</option>
                                    <option value="10" selected>10 MB</option>
                                    <option value="25">25 MB</option>
                                    <option value="50">50 MB</option>
                                </select>
                                <small class="text-muted">Maximum file size for uploads</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="queryTimeout" class="form-label">Database Query Timeout</label>
                                <select class="form-select" id="queryTimeout">
                                    <option value="10">10 seconds</option>
                                    <option value="30" selected>30 seconds</option>
                                    <option value="60">60 seconds</option>
                                    <option value="120">2 minutes</option>
                                </select>
                                <small class="text-muted">Maximum time for database queries</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="debugMode">
                                    <label class="form-check-label" for="debugMode">
                                        Enable Debug Mode
                                        <small class="text-muted d-block">Show detailed error messages for debugging (not recommended for production)</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="queryLogging">
                                    <label class="form-check-label" for="queryLogging">
                                        Enable Query Logging
                                        <small class="text-muted d-block">Log all database queries (significant performance impact)</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="environment" class="form-label">Environment</label>
                                <select class="form-select" id="environment">
                                    <option value="production" selected>Production</option>
                                    <option value="staging">Staging</option>
                                    <option value="development">Development</option>
                                </select>
                                <small class="text-muted">System environment setting</small>
                            </div>
                            
                            <div class="mt-4">
                                <button class="btn btn-danger" id="clearCacheBtn">
                                    <i class="bi bi-eraser me-2"></i> Clear All Cache
                                </button>
                                <button class="btn btn-outline-danger ms-2" id="resetSystemBtn" data-bs-toggle="modal" data-bs-target="#resetSystemModal">
                                    <i class="bi bi-exclamation-triangle me-2"></i> Reset System
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <footer class="text-center mt-5 pt-4 border-top">
                    <p class="mb-2">UrbanSolve System Configuration &copy; 2023</p>
                    <small class="text-muted">Version 2.4.1 | Last Updated: October 31, 2023 | Settings Modified: 12</small>
                </footer>
            </div>
        </div>
    </div>
    
    <!-- Modals for Settings -->
    
    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoleForm">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" placeholder="e.g., Issue Manager">
                        </div>
                        <div class="mb-3">
                            <label for="roleDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="roleDescription" rows="3" placeholder="Describe the role's responsibilities..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permViewIssues">
                                <label class="form-check-label" for="permViewIssues">View Issues</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permManageIssues">
                                <label class="form-check-label" for="permManageIssues">Manage Issues</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permViewReports">
                                <label class="form-check-label" for="permViewReports">View Reports</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permManageUsers">
                                <label class="form-check-label" for="permManageUsers">Manage Users</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="permSystemSettings">
                                <label class="form-check-label" for="permSystemSettings">System Settings</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveRoleBtn">Save Role</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Generate API Key Modal -->
    <div class="modal fade" id="generateApiKeyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate API Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="apiKeyForm">
                        <div class="mb-3">
                            <label for="apiKeyName" class="form-label">Key Name</label>
                            <input type="text" class="form-control" id="apiKeyName" placeholder="e.g., Mobile App Integration">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="apiRead">
                                <label class="form-check-label" for="apiRead">Read Access</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="apiWrite">
                                <label class="form-check-label" for="apiWrite">Write Access</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="apiDelete">
                                <label class="form-check-label" for="apiDelete">Delete Access</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="apiExpiry" class="form-label">Expiry Date</label>
                            <select class="form-select" id="apiExpiry">
                                <option value="7">7 days</option>
                                <option value="30" selected>30 days</option>
                                <option value="90">90 days</option>
                                <option value="365">1 year</option>
                                <option value="never">Never</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="generateApiKeyBtn">Generate Key</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reset System Modal -->
    <div class="modal fade" id="resetSystemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Reset System</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-octagon me-2"></i> Warning: This action cannot be undone!</h6>
                        <p class="mb-0">Resetting the system will:</p>
                        <ul class="mb-0">
                            <li>Restore all settings to factory defaults</li>
                            <li>Clear all custom configurations</li>
                            <li>Remove all API keys and integrations</li>
                            <li>Reset user permissions and roles</li>
                        </ul>
                    </div>
                    <p>Are you sure you want to reset the system to factory defaults?</p>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmReset">
                        <label class="form-check-label" for="confirmReset">
                            I understand this action cannot be undone
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmResetBtn" disabled>Reset System</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            initializeSettingsNavigation();
            
            // Set active nav link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.hasAttribute('data-section')) {
                        navLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                    }
                });
            });
            
            // Settings navigation
            function initializeSettingsNavigation() {
                const settingsNavLinks = document.querySelectorAll('.settings-nav .nav-link');
                const sections = document.querySelectorAll('.form-section');
                
                settingsNavLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Update active nav link
                        settingsNavLinks.forEach(l => l.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Show selected section
                        const sectionId = this.getAttribute('data-section');
                        sections.forEach(section => {
                            section.classList.add('d-none');
                            if (section.id === `${sectionId}-section`) {
                                section.classList.remove('d-none');
                            }
                        });
                    });
                });
            }
            
            // Color picker functionality
            document.querySelectorAll('.color-picker').forEach(picker => {
                picker.addEventListener('click', function() {
                    const color = this.getAttribute('data-color');
                    document.querySelectorAll('.color-picker').forEach(p => {
                        p.style.borderColor = '#dee2e6';
                    });
                    this.style.borderColor = color;
                    alert(`Primary color set to: ${color}`);
                });
            });
            
            // Save all settings button
	document.getElementById('saveAllSettingsBtn').addEventListener('click', function () {

    const formData = new FormData();

    formData.append('system_name', systemName.value);
    formData.append('system_url', systemUrl.value);
    formData.append('timezone', timezone.value);
    formData.append('date_format', dateFormat.value);
    formData.append('default_language', defaultLanguage.value);
    formData.append('items_per_page', itemsPerPage.value);

    formData.append('maintenance_mode', maintenanceMode.checked ? 1 : 0);
    formData.append('auto_save', autoSave.checked ? 1 : 0);
    formData.append('two_factor_auth', twoFactorAuth.checked ? 1 : 0);

    formData.append('theme',
        document.querySelector('input[name="theme"]:checked')?.id || 'light'
    );

    formData.append('enable_animations', enableAnimations.checked ? 1 : 0);
    formData.append('notification_email', notificationEmail.value);
    formData.append('notification_schedule', notificationSchedule.value);

    fetch("save_settings.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
const toast = document.createElement('div');
toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
toast.innerHTML = '✅ Settings saved successfully';
document.body.appendChild(toast);
setTimeout(()=>toast.remove(),3000);
    })
    .catch(() => {
        alert("❌ Failed to save settings");
    });
});

            
            // Reset settings button
            document.getElementById('resetSettingsBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to reset all settings to their default values?')) {
                    // Reset form values to defaults
                    document.getElementById('systemName').value = 'UrbanSolve Problem Management';
                    document.getElementById('systemUrl').value = 'https://urbansolve.example.com';
                    document.getElementById('timezone').value = 'UTC';
                    document.getElementById('dateFormat').value = 'DD/MM/YYYY';
                    document.getElementById('defaultLanguage').value = 'en';
                    document.getElementById('itemsPerPage').value = '25';
                    document.getElementById('maintenanceMode').checked = true;
                    document.getElementById('autoSave').checked = true;
                    document.getElementById('twoFactorAuth').checked = true;
                    
                    alert('Settings have been reset to default values.');
                }
            });
            
            // System check button
            document.getElementById('systemCheckBtn').addEventListener('click', function() {
                alert('Running system diagnostics...\n\n✓ Database connection: OK\n✓ File system: OK\n✓ API services: OK\n✓ Security checks: OK\n\nAll systems are functioning normally.');
            });
            
            // Reset logo button
            document.getElementById('resetLogoBtn').addEventListener('click', function() {
                if (confirm('Reset logo to default?')) {
                    alert('Logo has been reset to default.');
                }
            });
            
            // Save role button
            document.getElementById('saveRoleBtn').addEventListener('click', function() {
                const roleName = document.getElementById('roleName').value;
                if (!roleName) {
                    alert('Please enter a role name.');
                    return;
                }
                
                alert(`New role "${roleName}" has been created successfully!`);
                document.getElementById('addRoleForm').reset();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('addRoleModal'));
                modal.hide();
            });
            
            // Generate API key button
            document.getElementById('generateApiKeyBtn').addEventListener('click', function() {
                const keyName = document.getElementById('apiKeyName').value;
                if (!keyName) {
                    alert('Please enter a key name.');
                    return;
                }
                
                // Generate a random API key
                const apiKey = 'sk_live_' + Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2);
                
                alert(`API key generated successfully!\n\nKey Name: ${keyName}\nAPI Key: ${apiKey}\n\nPlease copy this key now as it won't be shown again.`);
                document.getElementById('apiKeyForm').reset();
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('generateApiKeyModal'));
                modal.hide();
            });
            
            // Create backup button
            document.getElementById('createBackupBtn').addEventListener('click', function() {
                alert('Creating system backup... This may take a few minutes.\n\nBackup started at ' + new Date().toLocaleTimeString());
            });
            
            // Restore backup button
            document.getElementById('restoreBackupBtn').addEventListener('click', function() {
                alert('Please select a backup file to restore...');
            });
            
            // Clear logs button
            document.getElementById('clearLogsBtn').addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all system logs?')) {
                    document.querySelector('.log-entries').innerHTML = '<div class="alert alert-info">Logs have been cleared.</div>';
                    alert('All system logs have been cleared.');
                }
            });
            
            // Clear cache button
            document.getElementById('clearCacheBtn').addEventListener('click', function() {
                if (confirm('Clear all system cache?')) {
                    alert('System cache has been cleared successfully.');
                }
            });
            
            // Reset system confirmation
            document.getElementById('confirmReset').addEventListener('change', function() {
                document.getElementById('confirmResetBtn').disabled = !this.checked;
            });
            
            // Reset system button
            document.getElementById('confirmResetBtn').addEventListener('click', function() {
                alert('System reset initiated... This will restore all settings to factory defaults.\n\nThe system will restart automatically.');
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('resetSystemModal'));
                modal.hide();
                
                // Simulate system reset
                setTimeout(() => {
                    location.reload();
                }, 2000);
            });
            
            // Copy API key functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('api-key') || e.target.closest('.api-key')) {
                    const apiKeyElement = e.target.classList.contains('api-key') ? e.target : e.target.closest('.api-key');
                    const apiKey = apiKeyElement.textContent;
                    
                    navigator.clipboard.writeText(apiKey).then(() => {
                        alert('API key copied to clipboard!');
                    }).catch(err => {
                        console.error('Failed to copy API key: ', err);
                    });
                }
            });
        });
		// theme toggle
document.querySelectorAll('input[name="theme"]').forEach(radio=>{
    radio.addEventListener('change',()=>{
        if(radio.id === 'themeDark'){
            document.body.classList.add('dark-mode');
            localStorage.setItem('theme','dark');
        }else{
            document.body.classList.remove('dark-mode');
            localStorage.setItem('theme','light');
        }
    });
});

// load saved theme
if(localStorage.getItem('theme') === 'dark'){
    document.body.classList.add('dark-mode');
    document.getElementById('themeDark').checked = true;
}

    </script>
</body>
</html>