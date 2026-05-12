<?php
session_start();
include '../config/db.php';
global $conn;

if(!isset($_SESSION['user']) || $_SESSION['role'] !== 'student'){
    header("Location: ../login.php");
    exit();
}

// Get the student's internal ID
$user_no = $_SESSION['student_no'];
$student_res = mysqli_query($conn, "SELECT student_id FROM students WHERE student_no = '$user_no'");
$student_data = mysqli_fetch_assoc($student_res);
$student_id = $student_data['student_id'];

$message = "";

// --- ENROLL LOGIC ---
if(isset($_POST['enroll'])) {
    $c_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $check = mysqli_query($conn, "SELECT * FROM enrollments WHERE student_id = '$student_id' AND course_id = '$c_id'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO enrollments (student_id, course_id) VALUES ('$student_id', '$c_id')");
        $message = "<div class='alert alert-success border-0 shadow-sm'>Enrolled successfully!</div>";
    }
}

// --- UNENROLL LOGIC ---
if(isset($_POST['unenroll'])) {
    $c_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    mysqli_query($conn, "DELETE FROM enrollments WHERE student_id = '$student_id' AND course_id = '$c_id'");
    $message = "<div class='alert alert-warning border-0 shadow-sm'>Unenrolled successfully.</div>";
}

// --- SEARCH ---
$search = $_GET['search'] ?? '';
$search_clause = "";
if(!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $search_clause = " WHERE course_name LIKE '%$search%' OR course_code LIKE '%$search%'";
}

// Fetch all courses and check if this specific student is already in them
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.course_id AND e.student_id = '$student_id') as is_enrolled 
          FROM courses c $search_clause";
$courses_result = mysqli_query($conn, $query);
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1 p-4">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Course Catalog</h2>
                <form class="d-flex" method="GET" style="max-width: 400px;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control border-0 shadow-sm" placeholder="Search course code or name..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-primary shadow-sm" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </div>
                </form>
            </div>

            <?= $message ?>

            <div class="row g-4">
                <?php if(mysqli_num_rows($courses_result) > 0): ?>
                    <?php while($course = mysqli_fetch_assoc($courses_result)): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3"><?= $course['course_code'] ?></span>
                                        <span class="text-muted small fw-bold"><?= $course['credits'] ?> Units</span>
                                    </div>
                                    <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($course['course_name']) ?></h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?= htmlspecialchars($course['description']) ?>
                                    </p>
                                    
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                                        <?php if($course['is_enrolled'] > 0): ?>
                                            <button type="submit" name="unenroll" class="btn btn-outline-danger w-100 fw-bold">
                                                <i class="fa-solid fa-minus-circle me-1"></i> Unenroll
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="enroll" class="btn btn-primary w-100 fw-bold">
                                                <i class="fa-solid fa-plus-circle me-1"></i> Enroll in Course
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa-solid fa-folder-open fs-1 text-light mb-3"></i>
                        <h4 class="text-muted">No courses found</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>