<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 PROTECT PAGE - Only students allowed
if(!isset($_SESSION['user']) || $_SESSION['role'] != "student"){
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch student data with Faculty Name join
$sql = "SELECT s.*, f.name AS faculty_name 
        FROM students s 
        LEFT JOIN faculties f ON s.faculty_id = f.id 
        WHERE s.email = '$email'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

// Fallback if session exists but DB record missing
if (!$student) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$student_id = $student['student_id'];
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            
            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold">Welcome back, <?= htmlspecialchars($student['first_name']) ?>! 👋</h2>
                    <p class="text-muted small">Here is what’s happening with your studies today.</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- 👤 PROFILE QUICK VIEW -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm text-center p-4 h-100">
                        <div class="mb-3">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($student['first_name'] . ' ' . $student['last_name']) ?>&background=0D6EFD&color=fff&size=128" 
                                 class="rounded-circle shadow-sm" alt="Profile">
                        </div>
                        <h4 class="fw-bold mb-0"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h4>
                        <p class="text-muted small mb-3">Student Number: <?= htmlspecialchars($student['student_no']) ?></p>
                        
                        <hr>
                        
                        <div class="text-start mb-4">
                            <div class="small text-muted mb-1">Faculty</div>
                            <div class="fw-bold mb-3"><i class="fa-solid fa-building-columns me-2 text-primary"></i> <?= htmlspecialchars($student['faculty_name'] ?? 'Not Assigned') ?></div>
                            
                            <div class="small text-muted mb-1">Programme</div>
                            <div class="fw-bold"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i> <?= htmlspecialchars($student['programme']) ?></div>
                        </div>

                        <a href="../students/student_profile.php?id=<?= $student['student_no'] ?>" class="btn btn-outline-primary w-100 rounded-pill">
                            View Full Profile
                        </a>
                    </div>
                </div>

                <!-- 📊 DASHBOARD STATS & ACTIONS -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <!-- Billing Status Card -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm p-4 bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Financial Status</h5>
                                    <i class="fa-solid fa-wallet fs-3 opacity-50"></i>
                                </div>
                                <?php
                                // Quick query for balance
                                $bill_res = mysqli_query($conn, "SELECT SUM(amount) as total FROM invoices WHERE student_id = '$student_id'");
                                $pay_res = mysqli_query($conn, "SELECT SUM(amount_paid) as paid FROM payments WHERE student_id = '$student_id'");
                                $total_billed = mysqli_fetch_assoc($bill_res)['total'] ?? 0;
                                $total_paid = mysqli_fetch_assoc($pay_res)['paid'] ?? 0;
                                $balance = $total_billed - $total_paid;
                                ?>
                                <h2 class="fw-bold">$<?= number_format($balance, 2) ?></h2>
                                <p class="small mb-0 opacity-75">Outstanding Balance</p>
                                <a href="../students/view_student_profile.php?id=<?= $student_id ?>#billing" class="text-white mt-3 d-inline-block small fw-bold text-decoration-none">
                                    View Invoices <i class="fa-solid fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Courses Quick Access -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm p-4 h-100">
                                <h5 class="fw-bold mb-3">📚 Registered Courses</h5>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item px-0 py-2 small border-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Introduction to Programming</li>
                                    <li class="list-group-item px-0 py-2 small border-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Database Systems</li>
                                    <li class="list-group-item px-0 py-2 small border-0"><i class="fa-solid fa-circle-check text-success me-2"></i> Web Development</li>
                                </ul>
                                <a href="#" class="btn btn-light btn-sm w-100 border text-primary fw-bold">Enter Classroom</a>
                            </div>
                        </div>

                        <!-- Search Feature -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="fw-bold mb-1">🔍 Networking Directory</h5>
                                        <p class="text-muted small mb-0">Looking for a lecturer or a fellow student? Search the university-wide directory.</p>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a href="../search.php" class="btn btn-dark px-4 rounded-pill">Search Users</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- End Inner Row -->
                </div> <!-- End Col-8 -->
            </div> <!-- End Main Row -->

        </div> <!-- End container -->
    </div> <!-- End main-content -->
</div>

<?php include '../includes/footer.php'; ?>