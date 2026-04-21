<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
		
		<?php 
				include_once("includes/leftnav.php");
            ?>
            
            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10 main-content">
                <!-- Dashboard Header -->
                <div class="dashboard-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="fw-bold">Welcome Back, Administrator</h1>
                            <p class="mb-0">Here's what's happening with your urban problem management system today.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="weather-widget d-inline-block">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-sun fs-1 me-3"></i>
                                    <div>
                                        <h4 class="mb-0">28°C</h4>
                                        <small>Sunny, Metro City</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Key Metrics -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 col-6">
                        <div class="card stat-card total">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="text-muted">TOTAL ISSUES</h5>
                                        <h2 class="fw-bold">4,128</h2>
                                        <span class="text-success"><i class="bi bi-arrow-up"></i> 12% from last month</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-exclamation-triangle fs-1 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-6">
                        <div class="card stat-card pending">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="text-muted">PENDING</h5>
                                        <h2 class="fw-bold">312</h2>
                                        <span class="text-warning"><i class="bi bi-clock"></i> 24 need attention</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-clock-history fs-1 text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-6">
                        <div class="card stat-card resolved">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="text-muted">RESOLVED</h5>
                                        <h2 class="fw-bold">3,246</h2>
                                        <span class="text-success"><i class="bi bi-check-circle"></i> 78% resolution rate</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-check-circle fs-1 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 col-6">
                        <div class="card stat-card urgent">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="text-muted">URGENT</h5>
                                        <h2 class="fw-bold">42</h2>
                                        <span class="text-danger"><i class="bi bi-exclamation-octagon"></i> Immediate action needed</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-exclamation-octagon fs-1 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts and Quick Stats -->
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Issue Trends Chart -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Issue Trends (Last 7 Days)</h5>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        This Week
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-period="week">This Week</a></li>
                                        <li><a class="dropdown-item" href="#" data-period="month">This Month</a></li>
                                        <li><a class="dropdown-item" href="#" data-period="quarter">Last 3 Months</a></li>
                                        <li><a class="dropdown-item" href="#" data-period="year">This Year</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="issueTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Issues Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Issues Requiring Attention</h5>
                                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th width="100">ID</th>
                                                <th>Issue</th>
                                                <th width="120">Category</th>
                                                <th width="120">Priority</th>
                                                <th width="120">Status</th>
                                                <th width="100">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="priority-high">
                                                <td class="fw-bold">#URB-1024</td>
                                                <td>
                                                    <h6 class="mb-0">Broken Street Light Pole</h6>
                                                    <small class="text-muted">Riverfront Road, about to fall</small>
                                                </td>
                                                <td>
                                                    <i class="bi bi-lightbulb text-warning me-1"></i>
                                                    <span>Street Light</span>
                                                </td>
                                                <td><span class="badge-status badge-urgent">URGENT</span></td>
                                                <td><span class="badge-status badge-in-progress">IN PROGRESS</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary">Assign</button></td>
                                            </tr>
                                            <tr class="priority-medium">
                                                <td class="fw-bold">#URB-1023</td>
                                                <td>
                                                    <h6 class="mb-0">Major Water Pipe Leakage</h6>
                                                    <small class="text-muted">Maple Street & 5th Ave intersection</small>
                                                </td>
                                                <td>
                                                    <i class="bi bi-droplet text-primary me-1"></i>
                                                    <span>Water Leak</span>
                                                </td>
                                                <td><span class="badge-status" style="background-color: #F39C12; color: white;">HIGH</span></td>
                                                <td><span class="badge-status badge-pending">PENDING</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary">View</button></td>
                                            </tr>
                                            <tr class="priority-high">
                                                <td class="fw-bold">#URB-1022</td>
                                                <td>
                                                    <h6 class="mb-0">Aggressive Street Dogs</h6>
                                                    <small class="text-muted">Near City Public School, safety concern</small>
                                                </td>
                                                <td>
                                                    <i class="bi bi-emoji-expressionless text-danger me-1"></i>
                                                    <span>Street Dogs</span>
                                                </td>
                                                <td><span class="badge-status badge-urgent">URGENT</span></td>
                                                <td><span class="badge-status badge-pending">PENDING</span></td>
                                                <td><button class="btn btn-sm btn-outline-danger">Escalate</button></td>
                                            </tr>
                                            <tr class="priority-medium">
                                                <td class="fw-bold">#URB-1021</td>
                                                <td>
                                                    <h6 class="mb-0">Overflowing Garbage Bin</h6>
                                                    <small class="text-muted">Market Street, not collected for 3 days</small>
                                                </td>
                                                <td>
                                                    <i class="bi bi-trash text-success me-1"></i>
                                                    <span>Waste</span>
                                                </td>
                                                <td><span class="badge-status" style="background-color: #F39C12; color: white;">MEDIUM</span></td>
                                                <td><span class="badge-status badge-in-progress">IN PROGRESS</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary">Update</button></td>
                                            </tr>
                                            <tr class="priority-low">
                                                <td class="fw-bold">#URB-1020</td>
                                                <td>
                                                    <h6 class="mb-0">Cracked Sidewalk</h6>
                                                    <small class="text-muted">Downtown Business District</small>
                                                </td>
                                                <td>
                                                    <i class="bi bi-cone-striped text-warning me-1"></i>
                                                    <span>Road Damage</span>
                                                </td>
                                                <td><span class="badge-status" style="background-color: #27AE60; color: white;">LOW</span></td>
                                                <td><span class="badge-status badge-pending">PENDING</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary">Review</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-lightning me-2"></i> Quick Actions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="quick-action-card">
                                            <div class="mb-3">
                                                <i class="bi bi-plus-circle fs-2 text-primary"></i>
                                            </div>
                                            <h6>Report New Issue</h6>
                                            <button class="btn btn-sm btn-primary mt-2 w-100">Create</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action-card">
                                            <div class="mb-3">
                                                <i class="bi bi-people fs-2 text-success"></i>
                                            </div>
                                            <h6>Add Citizen</h6>
                                            <button class="btn btn-sm btn-success mt-2 w-100">Add</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action-card">
                                            <div class="mb-3">
                                                <i class="bi bi-bar-chart fs-2 text-warning"></i>
                                            </div>
                                            <h6>Generate Report</h6>
                                            <button class="btn btn-sm btn-warning mt-2 w-100">Generate</button>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action-card">
                                            <div class="mb-3">
                                                <i class="bi bi-bell fs-2 text-danger"></i>
                                            </div>
                                            <h6>Send Alert</h6>
                                            <button class="btn btn-sm btn-danger mt-2 w-100">Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Category Distribution -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Issue Distribution by Category</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container small-chart">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            <div style="width: 15px; height: 15px; background-color: #F39C12; border-radius: 3px;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <span>Street Lights</span>
                                                <span class="fw-bold">1,320</span>
                                            </div>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-warning" style="width: 32%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            <div style="width: 15px; height: 15px; background-color: #3498DB; border-radius: 3px;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <span>Water Leakage</span>
                                                <span class="fw-bold">990</span>
                                            </div>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-primary" style="width: 24%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-3">
                                            <div style="width: 15px; height: 15px; background-color: #E74C3C; border-radius: 3px;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <span>Street Dogs</span>
                                                <span class="fw-bold">743</span>
                                            </div>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-danger" style="width: 18%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div style="width: 15px; height: 15px; background-color: #27AE60; border-radius: 3px;"></div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <span>Waste Management</span>
                                                <span class="fw-bold">1,075</span>
                                            </div>
                                            <div class="progress mt-1" style="height: 5px;">
                                                <div class="progress-bar bg-success" style="width: 26%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- System Status -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-activity me-2"></i> System Status
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>System Health</span>
                                        <span class="fw-bold text-success">Excellent</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 95%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Response Time</span>
                                        <span class="fw-bold text-success">2.8 hours</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 85%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Citizen Satisfaction</span>
                                        <span class="fw-bold text-warning">4.2/5.0</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 84%"></div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Storage Usage</span>
                                        <span class="fw-bold text-info">68% used</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" style="width: 68%"></div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button class="btn btn-sm btn-outline-primary w-100" id="refreshStatusBtn">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Status
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Stats and Activity -->
                <div class="row mt-4">
                    <div class="col-lg-4">
                        <!-- Top Contributors -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-trophy me-2"></i> Top Contributors
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                JD
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">John Doe</h6>
                                            <small class="text-muted">58 issues reported</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning">Top Contributor</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                JS
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Jane Smith</h6>
                                            <small class="text-muted">32 issues reported</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">Verified</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                RJ
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Robert Johnson</h6>
                                            <small class="text-muted">72 issues reported</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning">Top Contributor</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                MG
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">Maria Garcia</h6>
                                            <small class="text-muted">24 issues reported</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">Verified</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Recent Activity -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clock-history me-2"></i> Recent Activity
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    <div class="activity-item success">
                                        <h6 class="fw-bold">Issue #URB-1018 Resolved</h6>
                                        <p class="text-muted mb-1">Street light repaired on Maple Street</p>
                                        <small class="text-muted">Today, 10:30 AM • By John Doe</small>
                                    </div>
                                    
                                    <div class="activity-item info">
                                        <h6 class="fw-bold">New Issue Reported</h6>
                                        <p class="text-muted mb-1">Water leakage in Downtown area</p>
                                        <small class="text-muted">Today, 9:15 AM • By Citizen App</small>
                                    </div>
                                    
                                    <div class="activity-item warning">
                                        <h6 class="fw-bold">Issue Escalated to Urgent</h6>
                                        <p class="text-muted mb-1">Street dogs issue near school</p>
                                        <small class="text-muted">Today, 8:45 AM • By System</small>
                                    </div>
                                    
                                    <div class="activity-item danger">
                                        <h6 class="fw-bold">System Alert</h6>
                                        <p class="text-muted mb-1">High number of pending issues in Commercial zone</p>
                                        <small class="text-muted">Today, 8:00 AM • By Analytics</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Zone Performance -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-geo-alt me-2"></i> Zone Performance
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="quick-stat">
                                            <h5 class="text-muted">RESIDENTIAL</h5>
                                            <h3 class="fw-bold">85%</h3>
                                            <small class="text-success">Resolution Rate</small>
                                            <div class="mt-2">
                                                <small class="text-muted">142 issues active</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="quick-stat">
                                            <h5 class="text-muted">COMMERCIAL</h5>
                                            <h3 class="fw-bold">72%</h3>
                                            <small class="text-warning">Resolution Rate</small>
                                            <div class="mt-2">
                                                <small class="text-muted">89 issues active</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-stat">
                                            <h5 class="text-muted">INDUSTRIAL</h5>
                                            <h3 class="fw-bold">68%</h3>
                                            <small class="text-danger">Resolution Rate</small>
                                            <div class="mt-2">
                                                <small class="text-muted">64 issues active</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-stat">
                                            <h5 class="text-muted">PUBLIC</h5>
                                            <h3 class="fw-bold">90%</h3>
                                            <small class="text-success">Resolution Rate</small>
                                            <div class="mt-2">
                                                <small class="text-muted">28 issues active</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats Row -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-graph-up me-2"></i> Quick Statistics
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="text-center">
                                            <h2 class="fw-bold text-primary">2,847</h2>
                                            <small class="text-muted">Total Citizens</small>
                                            <div class="mt-1">
                                                <span class="badge-pill badge-trend-up">+8% this month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="text-center">
                                            <h2 class="fw-bold text-success">78%</h2>
                                            <small class="text-muted">Average Resolution Rate</small>
                                            <div class="mt-1">
                                                <span class="badge-pill badge-trend-up">+5% this month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="text-center">
                                            <h2 class="fw-bold text-warning">2.8h</h2>
                                            <small class="text-muted">Avg. Response Time</small>
                                            <div class="mt-1">
                                                <span class="badge-pill badge-trend-down">-18% from last month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-3">
                                        <div class="text-center">
                                            <h2 class="fw-bold text-info">4.2/5</h2>
                                            <small class="text-muted">Citizen Satisfaction</small>
                                            <div class="mt-1">
                                                <span class="badge-pill badge-trend-neutral">±0% change</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php include_once("includes/footer.php"); ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Chart instances
        let issueTrendChart, categoryChart;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize
            updateCurrentDateTime();
            initializeCharts();
            initializeEventListeners();
            
            // Set active nav link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Update current date and time
            function updateCurrentDateTime() {
                const now = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
            }
            
            // Initialize charts
            function initializeCharts() {
                createIssueTrendChart();
                createCategoryChart();
            }
            
            // Initialize event listeners
            function initializeEventListeners() {
                // Period dropdown for trend chart
                document.querySelectorAll('[data-period]').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const period = this.getAttribute('data-period');
                        updateTrendChartPeriod(period);
                        
                        // Update dropdown text
                        const dropdownBtn = document.querySelector('.dropdown-toggle');
                        dropdownBtn.textContent = this.textContent;
                    });
                });
                
                // Quick action buttons
                document.querySelectorAll('.quick-action-card button').forEach(button => {
                    button.addEventListener('click', function() {
                        const action = this.closest('.quick-action-card').querySelector('h6').textContent;
                        alert(`Initiating action: ${action}`);
                    });
                });
                
                // Refresh status button
                document.getElementById('refreshStatusBtn').addEventListener('click', function() {
                    refreshSystemStatus();
                });
                
                // Table action buttons
                document.querySelectorAll('.table button').forEach(button => {
                    button.addEventListener('click', function() {
                        const issueId = this.closest('tr').querySelector('td:first-child').textContent;
                        const action = this.textContent.trim();
                        alert(`${action} action for issue ${issueId}`);
                    });
                });
                
                // Auto-refresh data every 60 seconds
                setInterval(() => {
                    updateRealTimeData();
                }, 60000);
            }
            
            // Create issue trend chart
            function createIssueTrendChart() {
                const ctx = document.getElementById('issueTrendChart').getContext('2d');
                
                issueTrendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [
                            {
                                label: 'Issues Reported',
                                data: [85, 92, 78, 105, 98, 120, 115],
                                borderColor: '#3498DB',
                                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Issues Resolved',
                                data: [65, 70, 62, 85, 78, 95, 90],
                                borderColor: '#27AE60',
                                backgroundColor: 'rgba(39, 174, 96, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Issues'
                                },
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'nearest'
                        }
                    }
                });
            }
            
            // Create category chart
            function createCategoryChart() {
                const ctx = document.getElementById('categoryChart').getContext('2d');
                
                categoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Street Lights', 'Water Leakage', 'Street Dogs', 'Waste Management'],
                        datasets: [{
                            data: [1320, 990, 743, 1075],
                            backgroundColor: [
                                '#F39C12',
                                '#3498DB',
                                '#E74C3C',
                                '#27AE60'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
            
            // Update trend chart based on selected period
            function updateTrendChartPeriod(period) {
                let labels = [];
                let reportedData = [];
                let resolvedData = [];
                
                switch(period) {
                    case 'week':
                        labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        reportedData = [85, 92, 78, 105, 98, 120, 115];
                        resolvedData = [65, 70, 62, 85, 78, 95, 90];
                        break;
                    case 'month':
                        labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                        reportedData = [320, 350, 380, 410];
                        resolvedData = [250, 280, 300, 320];
                        break;
                    case 'quarter':
                        labels = ['Oct', 'Nov', 'Dec'];
                        reportedData = [1250, 1320, 1400];
                        resolvedData = [980, 1050, 1120];
                        break;
                    case 'year':
                        labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        reportedData = [1200, 1250, 1300, 1350, 1400, 1450, 1500, 1550, 1600, 1650, 1700, 1750];
                        resolvedData = [950, 1000, 1050, 1100, 1150, 1200, 1250, 1300, 1350, 1400, 1450, 1500];
                        break;
                }
                
                issueTrendChart.data.labels = labels;
                issueTrendChart.data.datasets[0].data = reportedData;
                issueTrendChart.data.datasets[1].data = resolvedData;
                issueTrendChart.update();
                
                alert(`Chart updated to show data for: ${period}`);
            }
            
            // Refresh system status
            function refreshSystemStatus() {
                // Simulate updating status with random values
                const health = 90 + Math.floor(Math.random() * 10);
                const responseTime = (2.5 + Math.random() * 1).toFixed(1);
                const satisfaction = (4.0 + Math.random() * 0.5).toFixed(1);
                const storage = 65 + Math.floor(Math.random() * 10);
                
                // Update progress bars
                document.querySelectorAll('.progress-bar')[0].style.width = `${health}%`;
                document.querySelectorAll('.progress-bar')[1].style.width = `${(responseTime / 5) * 100}%`;
                document.querySelectorAll('.progress-bar')[2].style.width = `${(satisfaction / 5) * 100}%`;
                document.querySelectorAll('.progress-bar')[3].style.width = `${storage}%`;
                
                // Update labels
                document.querySelectorAll('.fw-bold')[1].textContent = `${health}%`;
                document.querySelectorAll('.fw-bold')[2].textContent = `${responseTime} hours`;
                document.querySelectorAll('.fw-bold')[3].textContent = `${satisfaction}/5.0`;
                document.querySelectorAll('.fw-bold')[4].textContent = `${storage}% used`;
                
                // Update status text
                let healthText = 'Excellent';
                let healthClass = 'text-success';
                
                if (health < 70) {
                    healthText = 'Poor';
                    healthClass = 'text-danger';
                } else if (health < 85) {
                    healthText = 'Good';
                    healthClass = 'text-warning';
                }
                
                document.querySelectorAll('.fw-bold')[0].textContent = healthText;
                document.querySelectorAll('.fw-bold')[0].className = `fw-bold ${healthClass}`;
                
                alert('System status refreshed successfully!');
            }
            
            // Update real-time data
            function updateRealTimeData() {
                // Update current time
                updateCurrentDateTime();
                
                // Update some random stats
                const totalIssues = 4128 + Math.floor(Math.random() * 20) - 10;
                const pendingIssues = 312 + Math.floor(Math.random() * 10) - 5;
                const resolvedIssues = 3246 + Math.floor(Math.random() * 30) - 15;
                const urgentIssues = 42 + Math.floor(Math.random() * 5) - 2;
                
                // Update stat cards
                document.querySelectorAll('.stat-card.total h2')[0].textContent = totalIssues.toLocaleString();
                document.querySelectorAll('.stat-card.pending h2')[0].textContent = pendingIssues;
                document.querySelectorAll('.stat-card.resolved h2')[0].textContent = resolvedIssues.toLocaleString();
                document.querySelectorAll('.stat-card.urgent h2')[0].textContent = urgentIssues;
                
                // Update resolution rate
                const resolutionRate = Math.round((resolvedIssues / totalIssues) * 100);
                document.querySelectorAll('.stat-card.resolved span')[0].innerHTML = `<i class="bi bi-check-circle"></i> ${resolutionRate}% resolution rate`;
                
                console.log('Dashboard data auto-refreshed at', new Date().toLocaleTimeString());
            }
            
            // Simulate initial real-time data update
            setTimeout(() => {
                updateRealTimeData();
            }, 2000);
        });
    </script>
</body>
</html>