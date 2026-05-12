<?php
// We assume session_start() is already called in the main dashboard file
$user_name = $_SESSION['user'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'Guest';

// For a nice touch, we'll show a default profile icon if no image exists
$profile_pic = "../assets/img/default-avatar.png"; 
?>

<nav class="navbar navbar-expand-lg top-navbar px-4">
    <div class="container-fluid">
        <!-- Left Side: Page Context (Optional) -->
        <div class="d-none d-md-block">
            <span class="text-muted fw-medium">
                Welcome back, <span class="text-dark fw-bold"><?php echo htmlspecialchars($user_name); ?></span>
            </span>
        </div>

        <!-- Right Side: Profile Dropdown -->
        <div class="ms-auto d-flex align-items-center">
            
            <!-- Notifications (Optional Placeholder) -->
            <div class="me-3 text-muted">
                <i class="fa-regular fa-bell fs-5 cursor-pointer"></i>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <div class="d-flex align-items-center cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                    <div class="text-end me-2 d-none d-sm-block">
                        <div class="fw-bold mb-0 lh-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($user_name); ?></div>
                        <small class="text-muted text-capitalize" style="font-size: 0.75rem;"><?php echo $user_role; ?></small>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=4e73df&color=fff" 
                         alt="Profile" 
                         class="rounded-circle border" 
                         width="40" height="40">
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li class="px-3 py-2">
                        <div class="fw-bold"><?php echo htmlspecialchars($user_name); ?></div>
                        <small class="text-muted">Account ID: #<?php echo $_SESSION['user_id'] ?? '000'; ?></small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <!-- Profile Link -->
                    <li>
    <a class="dropdown-item py-2" href="../students/student_profile.php?id=<?= $_SESSION['student_id'] ?? '' ?>">
    <i class="fa-regular fa-user me-2 text-primary"></i> My Profile
</a>
</li>
                    
                    <!-- Edit Bio/Settings -->
                    <li>
                        <a class="dropdown-item py-2" href="../students/edit_profile.php">
                            <i class="fa-solid fa-pen-to-square me-2 text-success"></i> Edit Bio & Info
                        </a>
                    </li>

                    <?php if($user_role == 'student'): ?>
                    <li>
                        <a class="dropdown-item py-2" href="../students/finances.php">
                            <i class="fa-solid fa-wallet me-2 text-warning"></i> My Finances
                        </a>
                    </li>
                    <?php endif; ?>

                    <li><hr class="dropdown-divider"></li>
                    
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="../logout.php">
                            <i class="fa-solid fa-power-off me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Quick styles for the navbar components */
    .top-navbar {
        height: 70px;
        background: #fff;
        border-bottom: 1px solid #e3e6f0;
    }
    .dropdown-item:hover {
        background-color: #f8f9fc;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>