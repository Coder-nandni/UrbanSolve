<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Check for logout message
$logout_message = '';
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $logout_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
        You have been successfully logged out.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

// Check for error messages
$error = '';
if (isset($_GET['error'])) {
    $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($_GET['error']) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

// Check for session timeout message
if (isset($_GET['error']) && strpos($_GET['error'], 'Session expired') !== false) {
    $error = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($_GET['error']) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Urban Problem Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        .login-header {
            background: linear-gradient(to right, #2C3E50, #4A6491);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .login-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #4A6491;
            box-shadow: 0 0 0 0.2rem rgba(74, 100, 145, 0.25);
        }
        
        .input-group-text {
            background-color: transparent;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        
        .form-control:focus + .input-group-text {
            border-color: #4A6491;
        }
        
        .btn-login {
            background: linear-gradient(to right, #2C3E50, #4A6491);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: linear-gradient(to right, #4A6491, #2C3E50);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(74, 100, 145, 0.3);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            font-size: 14px;
            color: #666;
        }
        
        .login-footer a {
            color: #4A6491;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .brand-logo {
            font-size: 24px;
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
        }
        
        .brand-logo span {
            color: #E74C3C;
        }
        
        .password-toggle {
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
        }
        
        .password-toggle:hover {
            color: #4A6491;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">Urban<span>Solve</span></div>
            <h2>Admin Login</h2>
            <p>Urban Problem Management System</p>
        </div>
        
        <div class="login-body">
            <?php echo $logout_message; ?>
            <?php echo $error; ?>
            
            <form action="check_login.php" method="POST" id="loginForm">
                <div class="mb-4">
                    <label for="email" class="form-label fw-bold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <span class="input-group-text password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-login" name="login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </div>
            </form>
        </div>
        
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> Urban Problem Management System. All rights reserved.</p>
            <p>Developed By Code Crushers </p>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            if (!validateEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            return true;
        });
        
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>