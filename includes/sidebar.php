<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
$role = $_SESSION['role'] ?? 'guest';

// Get current page for "active" state highlighting
$current_page = $_SERVER['PHP_SELF'];
?>

<div class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h4 class="mb-0 text-primary fw-bold">
            <i class="fa-solid fa-university me-2"></i>UniERP
        </h4>
    </div>

    <div class="sidebar-menu flex-grow-1">
        <ul class="nav flex-column">

            <!-- ================= ADMIN LINKS ================= -->
            <?php if($role == "admin"){ ?>
                <li class="nav-item">
                    <a href="/university_dashboard/dashboard/dashboard.php" class="sidebar-link <?= strpos($current_page, 'dashboard.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/students/view_students.php" class="sidebar-link <?= strpos($current_page, 'view_students.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-user-graduate"></i> Students
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/lecturers/view_lecturers.php" class="sidebar-link <?= strpos($current_page, 'view_lecturers.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-chalkboard-user"></i> Lecturers
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/faculties/view_faculties.php" class="sidebar-link <?= strpos($current_page, 'view_faculties.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-building-columns"></i> Faculties
                    </a>
                </li>

                <!-- Finance Header & Sub-links (Admin Only) -->
                <li class="nav-item">
                    <a href="/university_dashboard/finance/finance_dashboard.php" class="sidebar-link <?= strpos($current_page, 'finance_dashboard.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-line"></i> Finance Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/finance/invoices.php" class="sidebar-link <?= strpos($current_page, 'invoices.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/finance/payments.php" class="sidebar-link <?= strpos($current_page, 'payments.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-receipt"></i> Payment History
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/finance/add_payment.php" class="sidebar-link <?= strpos($current_page, 'add_payment.php') !== false ? 'active' : '' ?>">
                        <i class="fa-solid fa-plus-circle"></i> Record Payment
                    </a>
                </li>
            <?php } ?>

           <!-- ================= STUDENT LINKS ================= -->
<?php if($role == "student"){ ?>
    <li class="nav-item">
        <a href="/university_dashboard/students/dashboard.php" class="sidebar-link <?= strpos($current_page, 'dashboard.php') !== false ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i> My Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="/university_dashboard/students/student_profile.php?id=<?= $_SESSION['student_no'] ?>" 
   class="sidebar-link <?= strpos($current_page, 'student_profile.php') !== false ? 'active' : '' ?>">
    <i class="fa-solid fa-user-circle"></i> My Profile
</a>
    </li>
    <li class="nav-item">
        <a href="/university_dashboard/courses/view_courses.php" class="sidebar-link">
            <i class="fa-solid fa-book"></i> Courses
        </a>
    </li>
    <li class="nav-item">
        <a href="/university_dashboard/search.php" class="sidebar-link">
            <i class="fa-solid fa-magnifying-glass"></i> Search Users
        </a>
    </li>
<?php } ?>

            <!-- ================= LECTURER LINKS ================= -->
            <?php if($role == "lecturer"){ ?>
                <li class="nav-item">
                    <a href="/university_dashboard/lecturers/dashboard.php" class="sidebar-link">
                        <i class="fa-solid fa-layer-group"></i> My Classes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/lecturers/profile.php" class="sidebar-link">
                        <i class="fa-solid fa-id-card"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/university_dashboard/search.php" class="sidebar-link">
                        <i class="fa-solid fa-magnifying-glass"></i> Search Users
                    </a>
                </li>
            <?php } ?>

            <!-- ================= GUEST ================= -->
            <?php if($role == "guest"){ ?>
                <li class="nav-item">
                    <a href="/university_dashboard/login.php" class="sidebar-link">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </a>
                </li>
            <?php } ?>

        </ul>
    </div>

    <!-- Bottom Logout Link -->
    <div class="p-3 border-top">
        <a href="../logout.php" class="sidebar-link text-danger">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
    </div>
</div>