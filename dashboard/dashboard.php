<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 ADMIN ONLY
if(!isset($_SESSION['user']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

// 📊 Logic: Fetching stats (keeping PHP at the top)
$queries = [
    'students' => "SELECT COUNT(*) as total FROM students",
    'lecturers' => "SELECT COUNT(*) as total FROM lecturers",
    'faculties' => "SELECT COUNT(*) as total FROM faculties",
    'users' => "SELECT COUNT(*) as total FROM users"
];

$stats = [];
foreach ($queries as $key => $sql) {
    $res = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($res);
    $stats[$key] = $data['total'] ?? 0;
}
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-0">👑 Admin Dashboard</h2>
                    <p class="text-muted">Welcome back! Here's what's happening today.</p>
                </div>
                <button class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-download me-2"></i> Generate Report
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="row g-4 mb-5">
                <!-- Students -->
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-primary text-white shadow-sm">
                        <div class="card-body">
                            <div class="stat-numbers">
                                <h5>Students</h5>
                                <h3><?php echo $stats['students']; ?></h3>
                            </div>
                            <i class="fa-solid fa-user-graduate stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Lecturers -->
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-success text-white shadow-sm">
                        <div class="card-body">
                            <div class="stat-numbers">
                                <h5>Lecturers</h5>
                                <h3><?php echo $stats['lecturers']; ?></h3>
                            </div>
                            <i class="fa-solid fa-chalkboard-user stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Faculties -->
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-warning text-white shadow-sm">
                        <div class="card-body">
                            <div class="stat-numbers">
                                <h5>Faculties</h5>
                                <h3><?php echo $stats['faculties']; ?></h3>
                            </div>
                            <i class="fa-solid fa-building-columns stat-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Users -->
                <div class="col-md-3">
                    <div class="card stat-card bg-gradient-dark text-white shadow-sm">
                        <div class="card-body">
                            <div class="stat-numbers">
                                <h5>System Users</h5>
                                <h3><?php echo $stats['users']; ?></h3>
                            </div>
                            <i class="fa-solid fa-shield-halved stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-card p-4">
                        <h5 class="mb-4 fw-bold text-dark">Quick Management</h5>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="manage_students.php" class="btn btn-outline-primary px-4 py-2">
                                <i class="fa-solid fa-plus me-2"></i> Register New Student
                            </a>
                            <a href="faculty_edit.php" class="btn btn-outline-secondary px-4 py-2">
                                <i class="fa-solid fa-pen-to-square me-2"></i> Edit Faculty Info
                            </a>
                            <a href="finances.php" class="btn btn-outline-info px-4 py-2">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i> View Finances
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End container -->
    </div> <!-- End main-content -->
</div>

<?php include '../includes/footer.php'; ?>