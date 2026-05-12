<?php
session_start();
include '../config/db.php';
global $conn;

if(!isset($_SESSION['user']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

// 📊 Query with Subqueries (now works because faculty_id exists everywhere)
$query = "SELECT f.*, 
          (SELECT COUNT(*) FROM students WHERE faculty_id = f.id) as student_count,
          (SELECT COUNT(*) FROM lecturers WHERE faculty_id = f.id) as lecturer_count
          FROM faculties f";

$result = mysqli_query($conn, $query);
if(!$result) die("Query Error: " . mysqli_error($conn));
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="page-title mb-1">University Faculties</h2>
                    <p class="text-muted small">Academic structure and population metrics.</p>
                </div>
                <a href="add_faculty.php" class="btn btn-primary px-4 shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i> New Faculty
                </a>
            </div>

            <div class="row g-4">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card custom-card h-100 transition-up">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="icon-box bg-primary-subtle text-primary rounded-3 px-3 py-2">
                                    <i class="fa-solid fa-building-columns fs-4"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0 border-0" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li><a class="dropdown-item" href="edit_faculty.php?id=<?= $row['id'] ?>">Edit Details</a></li>
                                        <li><a class="dropdown-item text-danger" href="#">Archive Faculty</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold text-dark mb-2"><?= htmlspecialchars($row['name']) ?></h5>
                            <p class="text-muted small mb-4" style="min-height: 40px;">
                                <?= htmlspecialchars(substr($row['description'] ?? 'No description provided.', 0, 85)) ?>...
                            </p>
                            
                            <div class="row g-0 border-top pt-3">
                                <div class="col-6 border-end text-center">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Students</small>
                                    <span class="fs-5 fw-bold text-dark"><?= $row['student_count'] ?></span>
                                </div>
                                <div class="col-6 text-center">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem;">Lecturers</small>
                                    <span class="fs-5 fw-bold text-dark"><?= $row['lecturer_count'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>