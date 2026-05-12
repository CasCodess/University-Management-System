<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 PROTECT PAGE
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}

$target_no = mysqli_real_escape_string($conn, $_GET['id'] ?? $_SESSION['student_no'] ?? '');

if (empty($target_no)) {
    die("<div class='container mt-5 alert alert-warning'>No Student Number provided.</div>");
}

$query = "SELECT s.*, f.name as faculty_name 
          FROM students s 
          LEFT JOIN faculties f ON s.faculty_id = f.id 
          WHERE s.student_no = '$target_no'";

$student_q = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($student_q);

if(!$student) {
    die("<div class='container mt-5 alert alert-danger'>
            <h4>Student record not found.</h4>
            <p>Could not find a student with Number: <b>$target_no</b></p>
            <a href='../students/dashboard.php' class='btn btn-primary btn-sm'>Return Home</a>
         </div>");
}

// 🔐 SECURITY: Prevent students from peeking at other students' profiles
if($_SESSION['role'] == 'student' && $_SESSION['student_no'] != $student['student_no']){
    header("Location: ../students/dashboard.php?error=unauthorized");
    exit();
}

/**
 * 3. FINANCIAL SUMMARY
 * We use the internal primary key ($student['student_id']) to link to other tables.
 */
$internal_id = $student['student_id'];

// --- COURSE COUNT ---
$course_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM enrollments WHERE student_id = '$internal_id'");
$course_count = mysqli_fetch_assoc($course_count_query)['total'] ?? 0;

$inv_total_query = mysqli_query($conn, "SELECT SUM(amount) as total FROM invoices WHERE student_id = '$internal_id'");
$inv_total = mysqli_fetch_assoc($inv_total_query)['total'] ?? 0;

$pay_total_query = mysqli_query($conn, "SELECT SUM(amount_paid) as total FROM payments WHERE student_id = '$internal_id'");
$pay_total = mysqli_fetch_assoc($pay_total_query)['total'] ?? 0;

$balance = $inv_total - $pay_total;
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            <!-- Header/Back Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="../students/view_students.php" class="text-decoration-none small text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Directory
                </a>
                
                <?php if($_SESSION['role'] == 'admin'): ?>
                <div class="dropdown">
                    <button class="btn btn-dark btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-gears me-1"></i> Quick Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item small" href="../students/edit_student.php?id=<?= $student['student_id'] ?>"><i class="fa-solid fa-user-pen me-2 text-muted"></i>Edit Personal Info</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small text-primary" href="#"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Generate Invoice</a></li>
                        <li><a class="dropdown-item small text-success" href="#"><i class="fa-solid fa-hand-holding-dollar me-2"></i>Record Payment</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <!-- Left Column: Personal Info -->
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm text-center p-4 mb-4">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow" style="width: 100px; height: 100px; font-size: 2.2rem; font-weight: 700;">
                            <?= strtoupper($student['first_name'][0] . $student['last_name'][0]) ?>
                        </div>
                        <h4 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h4>
                        <p class="text-muted small mb-3">Student No: <span class="fw-bold"><?= htmlspecialchars($student['student_no']) ?></span></p>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 border border-success-subtle">Active Student</span>
                    </div>

                    <div class="card border-0 shadow-sm p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Academic & Contact</h6>
                        <div class="mb-3">
                            <label class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">Email Address</label>
                            <div class="small fw-semibold"><?= htmlspecialchars($student['email']) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">Faculty</label>
                            <div class="small fw-semibold"><?= htmlspecialchars($student['faculty_name'] ?? 'Not Assigned') ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">Programme</label>
                            <div class="small fw-semibold"><?= htmlspecialchars($student['programme']) ?></div>
                        </div>
                        <div class="mb-0">
                            <label class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem;">Registration Date</label>
                            <div class="small fw-semibold"><?= date('D, M d, Y', strtotime($student['registration_date'])) ?></div>
                        </div>

                        <!-- Inside the Left Column: Personal Info (col-xl-4) -->
<div class="card border-0 shadow-sm text-center p-4 mb-4">
    <!-- ... avatar and name ... -->
    
    <!-- ADD THIS BIO SECTION -->
    <?php if(!empty($student['bio'])): ?>
        <p class="mt-3 small text-muted fst-italic">
            "<?= htmlspecialchars($student['bio']) ?>"
        </p>
    <?php endif; ?>

    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 border border-success-subtle mb-3">Active Student</span>

    <!-- ADD THIS EDIT BUTTON (Only visible to the student owner) -->
    <?php if($_SESSION['role'] == 'student' && $_SESSION['student_no'] == $student['student_no']): ?>
        <a href="edit_profile.php" class="btn btn-outline-primary btn-sm w-100 mt-2">
            <i class="fa-solid fa-user-pen me-1"></i> Edit Profile
        </a>
    <?php endif; ?>
</div>
                    </div>
                </div>

                <!-- Right Column: Financial & Academic Summary -->
                <div class="col-xl-8">
                    <!-- Financial Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm p-3 text-center bg-white">
                                <small class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Total Billed</small>
                                <div class="fs-4 fw-bold text-dark">$<?= number_format($inv_total, 2) ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm p-3 text-center bg-white">
                                <small class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Amount Settled</small>
                                <div class="fs-4 fw-bold text-success">$<?= number_format($pay_total, 2) ?></div>
                            </div>
                        </div>
                        <div class="col-md-3">
    <div class="card border-0 shadow-sm p-3 text-center bg-white">
        <small class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Courses Enrolled</small>
        <div class="fs-4 fw-bold text-primary"><?= $course_count ?></div>
    </div>
</div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm p-3 text-center bg-white border-start border-4 <?= ($balance > 0) ? 'border-danger' : 'border-success' ?>">
                                <small class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Current Balance</small>
                                <div class="fs-4 fw-bold <?= ($balance > 0) ? 'text-danger' : 'text-success' ?>">
                                    $<?= number_format($balance, 2) ?>
                                </div>
                            </div>

                            
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-light border-0 pt-3">
                            <ul class="nav nav-pills card-header-pills" id="profileTabs">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold small" data-bs-toggle="tab" href="#billing">Billing History</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold small" data-bs-toggle="tab" href="#courses">Courses</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Billing Tab -->
                                <div class="tab-pane fade show active" id="billing">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr class="text-muted small border-bottom">
                                                    <th>Ref Date</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th class="text-end">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="border-0">
                                                <?php
                                                // Again, using $internal_id to get invoices
                                                $billing = mysqli_query($conn, "SELECT * FROM invoices WHERE student_id = '$internal_id' ORDER BY id DESC");
                                                if(mysqli_num_rows($billing) > 0):
                                                    while($b = mysqli_fetch_assoc($billing)):
                                                ?>
                                                <tr>
                                                    <td class="small text-muted"><?= date('M d, Y', strtotime($b['created_at'] ?? $student['registration_date'])) ?></td>
                                                    <td class="fw-semibold small"><?= htmlspecialchars($b['title']) ?></td>
                                                    <td class="small fw-bold">$<?= number_format($b['amount'], 2) ?></td>
                                                    <td class="text-end">
                                                        <span class="badge bg-<?= ($b['status'] == 'paid') ? 'success' : 'warning' ?>-subtle text-<?= ($b['status'] == 'paid') ? 'success' : 'dark' ?> px-2 py-1" style="font-size: 0.65rem;">
                                                            <?= strtoupper($b['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endwhile; else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted small">No billing records found.</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Courses Tab -->
                               <div class="tab-pane fade" id="courses">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr class="text-muted small border-bottom">
                    <th>Code</th>
                    <th>Course Name</th>
                    <th>Credits</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $my_courses = mysqli_query($conn, "SELECT c.* FROM courses c 
                                                   JOIN enrollments e ON c.course_id = e.course_id 
                                                   WHERE e.student_id = '$internal_id'");
                
                if(mysqli_num_rows($my_courses) > 0):
                    while($course = mysqli_fetch_assoc($my_courses)):
                ?>
                <tr>
                    <td><span class="badge bg-secondary-subtle text-secondary"><?= $course['course_code'] ?></span></td>
                    <td class="fw-semibold small"><?= htmlspecialchars($course['course_name']) ?></td>
                    <td class="small"><?= $course['credits'] ?> Units</td>
                    <td class="text-end">
                        <a href="../courses/view_courses.php" class="btn btn-sm btn-light border" style="font-size: 0.7rem;">Manage</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <i class="fa-solid fa-book-open fs-2 text-light mb-2"></i>
                        <p class="text-muted small">You are not enrolled in any courses yet.</p>
                        <a href="../courses/view_courses.php" class="btn btn-primary btn-sm">Browse Catalog</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>