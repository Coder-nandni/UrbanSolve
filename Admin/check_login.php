<?php
session_start();

// Include database connection
require_once 'db.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Get form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Validate inputs
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no errors, check credentials
    if (empty($errors)) {
        // Escape email to prevent SQL injection
        $email = mysqli_real_escape_string($conn, $email);
        
        // Query to check admin
        $sql = "SELECT id, email, password FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            
            // Verify password (using MD5 for compatibility with our SQL insert)
            // For new system, use password_verify() with password_hash()
            if (md5($password) === $admin['password'] || $password === 'admin#2026n' && md5('admin#2026n') === $admin['password']) {
                // Password is correct, start session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['login_time'] = time();
                $_SESSION['LAST_ACTIVITY'] = time(); // Initialize last activity time
                
                // Set remember me cookie if checked (30 days)
                if (isset($_POST['remember'])) {
                    setcookie('admin_email', $email, time() + (30 * 24 * 60 * 60), "/");
                    setcookie('admin_remember', '1', time() + (30 * 24 * 60 * 60), "/");
                } else {
                    // Remove cookies if not checked
                    setcookie('admin_email', '', time() - 3600, "/");
                    setcookie('admin_remember', '', time() - 3600, "/");
                }
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Invalid password
                header("Location: login.php?error=Invalid email or password");
                exit();
            }
        } else {
            // Admin not found
            header("Location: login.php?error=Invalid email or password");
            exit();
        }
    } else {
        // Validation errors
        $error_message = implode("<br>", $errors);
        header("Location: login.php?error=" . urlencode($error_message));
        exit();
    }
} else {
    // If not a POST request, redirect to login
    header("Location: login.php");
    exit();
}
?>