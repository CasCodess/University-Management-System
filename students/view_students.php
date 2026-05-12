<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 ADMIN ONLY
if(!isset($_SESSION['user']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

// 🔍 Get Filters
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$faculty_id = mysqli_real_escape_string($conn, $_GET['faculty_id'] ?? '');
$programme = mysqli_real_escape_string($conn, $_GET['programme'] ?? '');

// 📊 Build Query
$query = "SELECT s.*, f.name AS faculty_name 
          FROM students s 
          LEFT JOIN faculties f ON s.faculty_id = f.id 
          WHERE 1=1";

if(!empty($search)){
    $query .= " AND (s.first_name LIKE '%$search%' OR s.last_name LIKE '%$search%' OR s.student_id LIKE '%$search%')";
}
if(!empty($faculty_id)){
    $query .= " AND s.faculty_id = '$faculty_id'";
}
if(!empty($programme)){
    $query .= " AND s.programme LIKE '%$programme%'";
}

$query .= " ORDER BY s.registration_date DESC";
$result = mysqli_query($conn, $query);
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Students Directory</h2>
                    <p class="text-muted small">Manage, filter, and track all registered students.</p>
                </div>
                <a href="add_student.php" class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-user-plus me-2"></i> Register New Student
                </a>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm p-4 mb-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Name or Student ID..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Faculty</label>
                        <select name="faculty_id" class="form-select bg-light">
                            <option value="">All Faculties</option>
                            <?php
                            $fac_res = mysqli_query($conn, "SELECT * FROM faculties");
                            while($f = mysqli_fetch_assoc($fac_res)){
                                $sel = ($faculty_id == $f['id']) ? "selected" : "";
                                echo "<option value='{$f['id']}' $sel>{$f['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Programme</label>
                        <input type="text" name="programme" class="form-control bg-light" placeholder="e.g. Computer Science" value="<?= htmlspecialchars($programme) ?>">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Filter</button>
                        <a href="view_students.php" class="btn btn-outline-secondary" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Alerts for Delete/Success -->
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['msg']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Student Table -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Student ID</th>
                                <th>Full Name</th>
                                <th>Faculty / Programme</th>
                                <th>Registration Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-primary-subtle text-primary fw-bold">
                                            #<?= $row['student_id'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['email']) ?></div>
                                    </td>
                                    <td>
                                        <div class="mb-1 text-dark small"><i class="fa-solid fa-building-columns me-1 text-muted"></i> <?= htmlspecialchars($row['faculty_name'] ?? 'N/A') ?></div>
                                        <span class="badge rounded-pill bg-light text-dark border small fw-normal"><?= htmlspecialchars($row['programme']) ?></span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('M d, Y', strtotime($row['registration_date'])) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <!-- View Full Profile -->
                                            <a href="../students/view_student_profile.php?id=<?= $row['student_id'] ?>" class="btn btn-sm btn-outline-primary" title="View Full Profile">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            
                                            <!-- Edit Info -->
                                            <a href="../students/edit_student.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit Info">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>

                                            <!-- View Finances (Linked to Profile Billing Tab) -->
                                            <a href="../students/view_student_profile.php?id=<?= $row['student_id'] ?>#billing" class="btn btn-sm btn-outline-info" title="View Finances">
                                                <i class="fa-solid fa-wallet"></i>
                                            </a>

                                            <!-- Delete/Deregister -->
                                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $row['id'] ?>)" title="Deregister">
                                                <i class="fa-solid fa-user-xmark"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-user-slash d-block fs-1 mb-3 opacity-25"></i>
                                        No students found matching those criteria.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- End container -->
    </div> <!-- End main-content -->
</div>

<script>
function confirmDelete(id) {
    if(confirm('Are you sure you want to permanently deregister this student? This will also remove their billing and payment history.')) {
        window.location.href = '../students/delete_student.php?id=' + id;
    }
}
</script>

<?php include '../includes/footer.php'; ?>