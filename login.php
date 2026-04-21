<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Urban Pulse</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h2 {
            margin-bottom: 0;
            font-weight: 700;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .login-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 12px;
            font-weight: 600;
            color: var(--dark);
            background-color: #f8f9fa;
        }
        
        .login-tabs .nav-link.active {
            background-color: white;
            color: var(--secondary);
            border-bottom: 3px solid var(--secondary);
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-login {
            background-color: var(--secondary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .login-divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .login-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #ddd;
        }
        
        .login-divider span {
            background-color: white;
            padding: 0 15px;
            color: #777;
        }
        
        .social-login {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .btn-social {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-social:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }
        
        .btn-social i {
            margin-right: 8px;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
        
        .login-footer a {
            color: var(--secondary);
            text-decoration: none;
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 30px 0;
            margin-top: auto;
        }
        
        .feature-highlight {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .feature-icon {
            background-color: var(--secondary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-city me-2"></i>Ludhiana City
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    
                </ul>
                <div class="d-flex">
                    
                    <a href="report.php"><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signupModal">
                        <i class="fas fa-bullhorn me-1"></i> Report Here
                    </button></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="login-card">
                        <div class="login-header">
                            <h2>Create Account</h2>
                            <p class="mb-0">Access your account to report Fill this Details</p>
                        </div>
                        <div class="login-body">
                            
                            
                            <div class="tab-content">
                                <!-- Citizen Login Tab -->
                                <div class="tab-pane fade show active" id="citizenLogin">
                                    <form id="citizenLoginForm">
                                        <div class="mb-3">
                                            <label for="citizenEmail" class="form-label">Email Address</label>
                                            <div class="input-group">
                                                
                                                <input type="email" class="form-control" id="citizenEmail" placeholder="Enter your email" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="citizenPassword" class="form-label">Password</label>
                                            <div class="input-group">
                                               
                                                <input type="password" class="form-control" id="citizenPassword" placeholder="Enter your password" required>
                                            </div>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        <button type="submit" class="btn btn-login mb-3">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login as Citizen
                                        </button>
                                    </form>
                                    
                                    
                                    
                                </div>
                                
                                <!-- Official Login Tab -->
                                <div class="tab-pane fade" id="officialLogin">
                                    <form id="officialLoginForm">
                                        <div class="mb-3">
                                            <label for="officialId" class="form-label">Official ID</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                <input type="text" class="form-control" id="officialId" placeholder="Enter your official ID" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="officialPassword" class="form-label">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control" id="officialPassword" placeholder="Enter your password" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department</label>
                                            <select class="form-select" id="department" required>
                                                <option value="" selected disabled>Select your department</option>
                                                <option value="publicWorks">Public Works</option>
                                                <option value="transportation">Transportation</option>
                                                <option value="parks">Parks & Recreation</option>
                                                <option value="sanitation">Sanitation</option>
                                                <option value="utilities">Utilities</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="officialRemember">
                                            <label class="form-check-label" for="officialRemember">Remember me</label>
                                        </div>
                                        <button type="submit" class="btn btn-login">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login as Official
                                        </button>
                                    </form>
                                    
                                    <div class="login-footer mt-3">
                                        Need help accessing your account? <a href="#">Contact admin</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features Highlight -->
                    <div class="feature-highlight">
                        <h5>Why you Create an Account!</h5>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Track Your Reports</h6>
                                <p class="mb-0">Monitor the status of issues you've reported in real-time</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Get Notifications</h6>
                                <p class="mb-0">Receive updates when your reported issues are resolved</p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
     <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Ludhiana City</h5>
                    <p>This portal is created for Ludhiana citizens to directly report issues in their area. Our goal is to make the city cleaner, safer, and more efficient by connecting people with local authorities</p>
                </div>
                <div class="col-md-2">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light">Home</a></li>
                        <li><a href="report.php" class="text-light">Report Issue</a></li>
                        <li><a href="dashboard.php" class="text-light">Dashboard</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> support@ludhianacity.gov</li>
                        <li><i class="fas fa-phone me-2"></i> +91 98765 43210</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Ludhiana Punjab </li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-3">
            <div class="row">
                <div class="col text-center">
                    <p class="mb-0">&copy; 2025 Ludhiana City. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form submission handlers
            document.getElementById('citizenLoginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // In a real application, this would validate credentials with a backend
                alert('Login successful! Redirecting to citizen dashboard...');
                // Redirect to citizen dashboard
                window.location.href = 'dashboard.php?role=citizen';
            });
            
            document.getElementById('officialLoginForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // In a real application, this would validate credentials with a backend
                alert('Login successful! Redirecting to official dashboard...');
                // Redirect to official dashboard
                window.location.href = 'dashboard.php?role=official';
            });
            
            // Social login buttons
            document.querySelectorAll('.btn-social').forEach(button => {
                button.addEventListener('click', function() {
                    alert('Social login functionality would be implemented here');
                });
            });
        });
    </script>
</body>
</html>