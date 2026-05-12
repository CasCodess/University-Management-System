<?php
session_start();
include '../config/db.php';
global $conn;

// 1. Get the current user's info from the database based on their session
$session_user = $_SESSION['user']; // This is the username/ID they logged in with
$query = "SELECT * FROM students WHERE student_no = '$session_user'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Error: Could not find student record in database.");
}

$internal_id = $student['student_id']; // The hidden 11-digit ID
$message = "";

if (isset($_POST['update_profile'])) {
    // Sanitize everything
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $bio        = mysqli_real_escape_string($conn, $_POST['bio']);

    // Use the student_id for the WHERE clause to be 100% precise
    $update_sql = "UPDATE students SET 
                   first_name = '$first_name', 
                   last_name = '$last_name', 
                   phone = '$phone', 
                   bio = '$bio' 
                   WHERE student_id = '$internal_id'";

    if (mysqli_query($conn, $update_sql)) {
        $message = "<div class='alert alert-success shadow-sm border-0'>
                        <i class='fa-solid fa-circle-check me-2'></i> Profile updated successfully! Redirecting...
                    </div>";
        echo "<script>setTimeout(function(){ window.location.href = 'student_profile.php?id=" . $student['student_no'] . "'; }, 1500);</script>";
    } else {
        $message = "<div class='alert alert-danger'>Update failed: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="d-flex">

<?php include '../includes/sidebar.php'; ?>

<div style="margin-left:250px; width:100%;">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

<h2>Edit Student</h2>

<form method="POST">

<input type="text" name="first_name" value="<?= $student['first_name'] ?>" class="form-control mb-2">

<input type="text" name="last_name" value="<?= $student['last_name'] ?>" class="form-control mb-2">

<input type="email" name="email" value="<?= $student['email'] ?>" class="form-control mb-2">

<select name="faculty_id" class="form-control mb-2">

<?php
$fac = mysqli_query($conn, "SELECT * FROM faculties");
while($f = mysqli_fetch_assoc($fac)){

$selected = ($student['faculty_id'] == $f['id']) ? "selected" : "";

echo "<option value='{$f['id']}' $selected>{$f['id']} - {$f['name']}</option>";
}
?>

</select>

<input type="text" name="programme" value="<?= $student['programme'] ?>" class="form-control mb-2">

<button type="submit" name="update" class="btn btn-success">Update</button>

</form>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>