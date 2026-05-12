<?php
include 'config/db.php';
global $conn;

$message = "";

// Fetch Faculties for the dropdown
$fac_query = mysqli_query($conn, "SELECT id, name FROM faculties ORDER BY name ASC");

if(isset($_POST['register'])){

    // Sanitize Inputs
    $full_name   = mysqli_real_escape_string($conn, $_POST['full_name']);
    $user_number = mysqli_real_escape_string($conn, $_POST['user_number']); 
    $email       = mysqli_real_escape_string($conn, $_POST['email']); 
    $faculty_id  = mysqli_real_escape_string($conn, $_POST['faculty_id']);
    $programme   = mysqli_real_escape_string($conn, $_POST['programme']);
    $role        = mysqli_real_escape_string($conn, $_POST['role']);
    $password    = $_POST['password'];

    // Get Faculty Name - Added a check so it doesn't crash if Faculty is left blank for Admin
    $faculty_name = "N/A";
    if(!empty($faculty_id)) {
        $name_res = mysqli_query($conn, "SELECT name FROM faculties WHERE id = '$faculty_id'");
        $name_data = mysqli_fetch_assoc($name_res);
        $faculty_name = $name_data['name'];
    }

    // Secure Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 1. Insert into USERS table (The login credentials)
    $sql_user = "INSERT INTO users 
    (username, password, role, email, full_name, faculty, programme, user_number) 
    VALUES 
    ('$user_number', '$hashed_password', '$role', '$email', '$full_name', '$faculty_name', '$programme', '$user_number')";

    if(mysqli_query($conn, $sql_user)){

        if($role == "student"){
            // Split name for profile table
            $name_parts = explode(" ", $full_name, 2);
            $fn = $name_parts[0];
            $ln = $name_parts[1] ?? '';

            // 2. Insert into STUDENTS table
            $sql_student = "INSERT INTO students (student_no, first_name, last_name, email, programme, faculty_id, registration_date) 
                            VALUES ('$user_number', '$fn', '$ln', '$email', '$programme', '$faculty_id', NOW())";
            
            mysqli_query($conn, $sql_student);
        }

        header("Location: login.php?success=1");
        exit();
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | University Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; }
        .register-card { border: none; border-radius: 15px; }
    </style>
</head>
<body class="d-flex align-items-center" style="min-height: 100vh;">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4 shadow-sm register-card">
                <h3 class="text-center fw-bold text-primary mb-4">Registration</h3>

                <?php if($message): ?>
                    <div class="alert alert-danger small"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="123456789@nust.na or example@gmail.com" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Faculty</label>
                            <select name="faculty_id" class="form-select">
                                <option value="">Select...</option>
                                <?php while($f = mysqli_fetch_assoc($fac_query)): ?>
                                    <option value="<?= $f['id'] ?>"><?= $f['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Programme</label>
                            <input type="text" name="programme" class="form-control" placeholder="BSc IT">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Student / Lecturer Number</label>
                        <input type="text" name="user_number" class="form-control" placeholder="e.g. 123456789" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="admin">Admin</option> <!-- ✅ Admin option added here -->
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary w-100 fw-bold py-2">Register Account</button>
                    
                    <div class="text-center mt-3 small">
                        <a href="login.php" class="text-decoration-none">Already have an account? Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>