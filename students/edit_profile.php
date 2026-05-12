<?php
session_start();
include '../config/db.php';
global $conn;

if(!isset($_SESSION['user']) || $_SESSION['role'] !== 'student'){
    header("Location: ../login.php");
    exit();
}

$student_no = $_SESSION['student_no'];

// 1. Fetch current data
$query = "SELECT * FROM students WHERE student_no = '$student_no'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

// 2. Handle Form Submission
$message = "";
if (isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);

    $update_sql = "UPDATE students SET 
                   first_name = '$first_name', 
                   last_name = '$last_name', 
                   phone = '$phone', 
                   bio = '$bio' 
                   WHERE student_no = '$student_no'";

    if (mysqli_query($conn, $update_sql)) {
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
        // Refresh data
        header("Refresh:1");
    } else {
        $message = "<div class='alert alert-danger'>Error updating profile.</div>";
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Edit Personal Profile</h5>
                        </div>
                        <div class="card-body p-4">
                            <?= $message ?>
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= $student['first_name'] ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= $student['last_name'] ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="<?= $student['phone'] ?? '' ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">About Me (Bio)</label>
                                    <textarea name="bio" class="form-control" rows="4" placeholder="Tell us a bit about yourself..."><?= $student['bio'] ?? '' ?></textarea>
                                </div>
                                <div class="d-flex justify-content-between pt-3">
                                    <a href="student_profile.php?id=<?= $student_no ?>" class="btn btn-light border">Cancel</a>
                                    <button type="submit" name="update_profile" class="btn btn-primary px-4">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>