<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("db.php");

/* already logged in */

$activeTab = 'signup';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($_POST['action'] == 'login') {
        $activeTab = 'login';
    }
}

/* FORM SUBMIT */
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    /* ================= SIGNUP ================= */
    if ($_POST['action'] == 'signup') {

        $full_name = $_POST['full_name'];
        $mobile    = $_POST['mobile'];
        $email     = $_POST['email'];
        $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = mysqli_query($conn, "SELECT id FROM members WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>alert('Email already exists');</script>";
        } else {
            $q = "INSERT INTO members (full_name, mobile, email, password)
                  VALUES ('$full_name','$mobile','$email','$password')";
            mysqli_query($conn, $q);
            $_SESSION['signup_success'] = true;
        }
    }

    /* ================= LOGIN ================= */
    if ($_POST['action'] == 'login') {

        $email = $_POST['email'];
        $pass  = $_POST['password'];

        $q = mysqli_query($conn, "SELECT * FROM members WHERE email='$email'");

        if (mysqli_num_rows($q) == 1) {
            $row = mysqli_fetch_assoc($q);

            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['full_name'];
				$_SESSION['login_success'] = true;
            } else {
                echo "<script>alert('Wrong password');</script>";
            }
        } else {
            echo "<script>alert('Email not found');</script>";
        }
    }
}
?>

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
		.form-btn {
    background-color: #3498db;
    color: #fff;
    border: none;
}

.form-btn:hover {
    background-color: #2980b9;
}
.toggle-btn {
    background-color: #fff;
    color: #000;
    border: 1px solid black;
    padding: 10px;
    border-radius: 10px;
    transition: 0.3s;
}

.toggle-btn.active {
    background-color: #3498db;
    color: #fff;
}
		#showSignup.active,
#showLogin.active {
    background-color: #3498db;
    color: #fff;
}
@media (max-width: 768px){

    .login-container{
        padding: 20px 10px;
    }

    .login-body{
        padding: 20px;
    }

    .login-header{
        padding: 20px;
    }

    .login-header h2{
        font-size: 20px;
    }
}
@media (max-width: 576px){

    .feature-highlight{
        padding: 15px;
    }

    .feature-item{
        flex-direction: column;
        align-items: flex-start;
    }

    .feature-icon{
        margin-bottom: 10px;
    }
}
.toggle-btn{
    width: 100%;
}

@media (min-width: 768px){
    .toggle-btn{
        width: 50%;
    }
}
/* SUCCESS ANIMATION */
.success-animation {
  margin: 0 auto;
}

.checkmark-circle {
  width: 80px;
  height: 80px;
  position: relative;
  display: inline-block;
  vertical-align: top;
}

.checkmark-circle .background {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #28a745;
  position: absolute;
}

.checkmark-circle .checkmark {
  border-radius: 5px;
}

.checkmark-circle .checkmark.draw:after {
  animation-delay: 0.3s;
  animation-duration: 0.8s;
  animation-timing-function: ease;
  animation-name: checkmark;
  transform: scaleX(-1) rotate(135deg);
}

.checkmark-circle .checkmark:after {
  opacity: 1;
  height: 40px;
  width: 20px;
  transform-origin: left top;
  border-right: 5px solid #28a745;
  border-top: 5px solid #28a745;
  border-radius: 2px !important;
  content: '';
  left: 28px;
  top: 45px;
  position: absolute;
}

@keyframes checkmark {
  0% { height: 0; width: 0; opacity: 1; }
  20% { height: 0; width: 20px; opacity: 1; }
  40% { height: 40px; width: 20px; opacity: 1; }
  100% { height: 40px; width: 20px; opacity: 1; }
}
/* LOADER CIRCLE */
.circle-loader {
  border: 4px solid #eee;
  border-left-color: #28a745;
  animation: loader-spin 1s linear infinite;
  border-radius: 50%;
  width: 70px;
  height: 70px;
  margin: 0 auto;
  position: relative;
}

@keyframes loader-spin {
  100% { transform: rotate(360deg); }
}

/* CHECKMARK AFTER LOAD */
.circle-loader.load-complete {
  animation: none;
  border-color: #28a745;
  transition: border 0.3s ease-out;
}

.circle-loader.load-complete .checkmark {
  display: block;
}

.checkmark {
  display: none;
}

.checkmark.draw:after {
  animation: checkmark 0.6s ease forwards;
  transform: scaleX(-1) rotate(135deg);
}

.checkmark:after {
  opacity: 1;
  height: 30px;
  width: 15px;
  transform-origin: left top;
  border-right: 4px solid #28a745;
  border-top: 4px solid #28a745;
  content: '';
  position: absolute;
  left: 22px;
  top: 35px;
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
                <div class="d-flex w-100 justify-content-end">
                    
                    <a href="signup.php"><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signupModal">
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
                <div class="col-lg-5 col-md-7 col-sm-10 col-12">
                    <div class="login-card">
                        <div class="login-header">
                            <h2>Create Account</h2>
                            <p class="mb-0">Access your account to report Fill this Details</p>
                        </div>
                        <div class="login-body">
                            
                            
                            <div class="tab-content">
                                 <!-- Toggle Buttons -->
								<div class="d-flex mb-4">
									<button type="button" class="toggle-btn w-50" id="showSignup">Sign Up</button>
									<button type="button" class="toggle-btn w-50" id="showLogin">Login</button>
								</div>

														
								<!-- ================= SIGNUP FORM ================= -->
								
								<div class="tab-pane fade show active" id="citizenLogin">
									<form method="POST" action="signup.php" id="signupForm"
											  style="<?= ($activeTab=='signup') ? 'display:block;' : 'display:none;' ?>">
											<input type="hidden" name="action" value="signup">
                               
											<div class="row mb-3">
												<div class="col-md-6 col-12">
													<label class="form-label">Full Name</label>
													<input type="text" class="form-control" name="full_name" required>
												</div>
												<div class="col-md-6 col-12">
													<label class="form-label">Mobile</label>
													<input type="text" class="form-control" name="mobile" required>
												</div>
											</div>
																			
											<div class="mb-3">
												<label class="form-label">Email</label>
												<input type="email" class="form-control" name="email" required>
											</div>

											<div class="mb-3">
												<label class="form-label">Password</label>
												<input type="password" class="form-control" name="password" required>
											</div>

										<button type="submit" class="toggle-btn w-100" id="signupSubmit">
											Sign Up
										</button>
										<br>
										<br>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        
                                    </form>                                                                
                                    <!-- ================= LOGIN FORM ================= -->
									<form method="POST" action="signup.php" id="officialLoginForm"
									style="<?= ($activeTab=='login') ? 'display:block;' : 'display:none;' ?>">
										<input type="hidden" name="action" value="login">

										<div class="mb-3">
											<label class="form-label">Email</label>
											<input type="email" class="form-control" name="email" required>
										</div>

										<div class="mb-3">
											<label class="form-label">Password</label>
											<input type="password" class="form-control" name="password" required>
										</div>

										<div class="mb-3 form-check">
											<input type="checkbox" class="form-check-input" name="remember">
											<label class="form-check-label">Remember me</label>
										</div>

										<button type="submit" class="toggle-btn w-100" id="loginSubmit">
											Login
										</button>



									</form>

                                </div>
                                
                                <!-- Official Login Tab -->
                                <div class="tab-pane fade" id="officialLogin">
								<form method="POST" action="signup.php" id="loginForm">                                        
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
<!-- SIGNUP SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">

      <div class="success-animation mb-3">
        <div class="checkmark-circle">
          <div class="checkmark draw"></div>
        </div>
      </div>

      <h4 class="text-success fw-bold">Signup Successful 🎉</h4>
      <p class="text-muted">Your account has been created successfully.</p>

      <button class="btn btn-primary mt-3" data-bs-dismiss="modal">
        Continue to Login
      </button>

    </div>
  </div>
</div>
<!-- LOGIN SUCCESS MODAL -->
<div class="modal fade" id="loginSuccessModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">

      <div class="success-animation mb-3">
        <div class="circle-loader">
          <div class="checkmark draw"></div>
        </div>
      </div>

      <h4 class="text-success fw-bold">Login Successful 🎉</h4>
     <p>Welcome back, <b><?= $_SESSION['user_name'] ?? '' ?></b></p>

    </div>
  </div>
</div>
    <?php 
				include_once("footer.php");
            ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
	<script>
const signupBtn = document.getElementById("showSignup");
const loginBtn = document.getElementById("showLogin");

const signupForm = document.getElementById("signupForm");
const loginForm = document.getElementById("officialLoginForm");

const signupSubmit = document.getElementById("signupSubmit");
const loginSubmit = document.getElementById("loginSubmit");

function activateSignup() {
    signupForm.style.display = "block";
    loginForm.style.display = "none";

    signupBtn.classList.add("active");
    loginBtn.classList.remove("active");

    signupSubmit.classList.add("active");
    loginSubmit.classList.remove("active");
}

function activateLogin() {
    signupForm.style.display = "none";
    loginForm.style.display = "block";

    loginBtn.classList.add("active");
    signupBtn.classList.remove("active");

    loginSubmit.classList.add("active");
    signupSubmit.classList.remove("active");
}

signupBtn.onclick = activateSignup;
loginBtn.onclick = activateLogin;


// PAGE LOAD FIX
window.onload = function () {
    let activeTab = "<?= $activeTab ?>";

    if (activeTab === "login") {
        activateLogin();
    } else {
        activateSignup();
    }
};
document.querySelector('#successModal button').onclick = function(){
    activateLogin();
};
<?php if(isset($_SESSION['signup_success'])): ?>
document.addEventListener("DOMContentLoaded", function(){
    var myModal = new bootstrap.Modal(document.getElementById('successModal'));
    myModal.show();
});
<?php unset($_SESSION['signup_success']); endif; ?>
<?php if(isset($_SESSION['login_success'])): ?>
document.addEventListener("DOMContentLoaded", function(){

    var modal = new bootstrap.Modal(document.getElementById('loginSuccessModal'));
    modal.show();

    let loader = document.querySelector('.circle-loader');

    // ⏳ loader → checkmark
    setTimeout(() => {
        loader.classList.add('load-complete');
    }, 1000);

    // 🚀 redirect after animation
    setTimeout(() => {
        window.location.href = "dashboard.php";
    }, 2000);

});
<?php unset($_SESSION['login_success']); endif; ?>
</script>
</body>
</html>