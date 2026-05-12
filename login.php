<?php
session_start();
include 'config/db.php';
global $conn;

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Query the users table
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Using password_verify is safer, but keeping your current logic for compatibility
    if ($user && password_verify($password, $user['password'])) {

        if ($role != $user['role']) {
            $error = "Incorrect role selected for this account.";
        } else {
            // ✅ SET CORE SESSIONS
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            // ✅ CRITICAL FIX: Match the ID to your table structure
            if($user['role'] == "student") {
                // If you use the 'username' as the ID (e.g., 123456789)
                $_SESSION['student_no'] = $user['username']; 
                
                // ALTERNATIVE: If 'user_number' is where the 123456789 is stored, use this:
                // $_SESSION['student_no'] = $user['user_number']; 
            }

            // REDIRECTS
            if ($user['role'] == "admin") {
                header("Location: dashboard/dashboard.php");
            } elseif ($user['role'] == "lecturer") {
                header("Location: lecturers/dashboard.php");
            } elseif ($user['role'] == "student") {
                header("Location: students/dashboard.php");
            }
            exit();
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .login-card { border: none; border-radius: 15px; }
        .btn-primary { background-color: #4e73df; border: none; padding: 12px; }
        .btn-primary:hover { background-color: #2e59d9; }
    </style>
</head>
<body class="d-flex align-items-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary"><i class="fa-solid fa-graduation-cap"></i> UniPortal</h2>
                <p class="text-muted">Management Information System</p>
            </div>

            <div class="card p-4 shadow-sm login-card">
                <h4 class="text-center mb-4">Sign In</h4>

                <?php if(isset($_GET['success'])): ?>
                    <div class="alert alert-success small py-2 border-0">Registration successful! Please login.</div>
                <?php endif; ?>

                <?php if($error): ?>
                    <div class="alert alert-danger small py-2 border-0"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">User ID / Student No.</label>
                        <input type="text" name="username" class="form-control" placeholder="e.g. 2026001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Login As</label>
                        <select name="role" class="form-select" required>
                            <option value="">Choose role...</option>
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100 fw-bold shadow-sm">
                        Login to Dashboard
                    </button>
                </form>

                <div class="text-center mt-4 border-top pt-3">
                    <p class="small text-muted mb-0">Don't have an account?</p>
                    <a href="register.php" class="text-decoration-none small fw-bold">Apply for Registration</a>
                </div>
            </div>
            
            <p class="text-center text-muted mt-4" style="font-size: 0.75rem;">
                &copy; 2026 University Management System
            </p>

        </div>
    </div>
</div>

</body>
</html>