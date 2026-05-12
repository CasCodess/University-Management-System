<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 Admin Security
if(!isset($_SESSION['user']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

// 🔍 Get Filter Inputs
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$faculty_id = mysqli_real_escape_string($conn, $_GET['faculty_id'] ?? '');

// 📊 Build Query with the newly created faculty_id link
$query = "SELECT l.*, f.name AS faculty_name 
          FROM lecturers l 
          LEFT JOIN faculties f ON l.faculty_id = f.id 
          WHERE 1=1";

if(!empty($search)){
    $query .= " AND (l.first_name LIKE '%$search%' OR l.last_name LIKE '%$search%' OR l.email LIKE '%$search%')";
}
if(!empty($faculty_id)){
    $query .= " AND l.faculty_id = '$faculty_id'";
}

$result = mysqli_query($conn, $query);
if(!$result) die("Query Error: " . mysqli_error($conn));
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="page-title mb-1">Faculty Staff</h2>
                    <p class="text-muted small">Manage lecturer profiles and faculty assignments.</p>
                </div>
                <a href="add_lecturer.php" class="btn btn-primary shadow-sm">
                    <i class="fa-solid fa-user-tie me-2"></i> Add New Lecturer
                </a>
            </div>

            <!-- Filter Bar -->
            <div class="card custom-card p-4 mb-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted">Search</label>
                        <input type="text" name="search" class="form-control bg-light" placeholder="Name or Email..." value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Faculty</label>
                        <select name="faculty_id" class="form-select bg-light">
                            <option value="">All Faculties</option>
                            <?php
                            $fac_list = mysqli_query($conn, "SELECT * FROM faculties");
                            while($f = mysqli_fetch_assoc($fac_list)){
                                $sel = ($faculty_id == $f['id']) ? "selected" : "";
                                echo "<option value='{$f['id']}' $sel>{$f['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100">Apply Filters</button>
                        <a href="view_lecturers.php" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="card custom-card shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase">
                            <tr style="font-size: 0.75rem; letter-spacing: 1px;">
                                <th class="ps-4 py-3">Lecturer Details</th>
                                <th>Email Address</th>
                                <th>Faculty</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:45px; height:45px; font-size: 1.2rem;">
                                                <i class="fa-solid fa-chalkboard-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= $row['first_name'] . ' ' . $row['last_name'] ?></div>
                                                <small class="text-muted">Staff ID: #<?= $row['id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small"><?= $row['email'] ?></span></td>
                                    <td>
                                        <?php if($row['faculty_name']): ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">
                                                <?= $row['faculty_name'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border rounded-pill px-3">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm border rounded">
                                            <a href="edit_lecturer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-white text-secondary px-3 border-end" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-sm btn-white text-danger px-3" title="Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted small">No staff members found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if(confirm('Warning: This will remove this staff member from the system. Continue?')) {
        window.location.href = 'delete_lecturer.php?id=' + id;
    }
}
</script>

<?php include '../includes/footer.php'; ?>