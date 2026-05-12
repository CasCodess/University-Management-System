<?php
session_start();
include '../config/db.php';
global $conn;

// Fetch invoices with student names
$query = "SELECT i.*, s.first_name, s.last_name 
          FROM invoices i 
          JOIN students s ON i.student_id = s.student_id 
          ORDER BY i.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Student Invoices</h2>
                <button class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Generate New Invoice</button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Inv #</th>
                                <th>Student</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#<?= $row['id'] ?></td>
                                <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
                                <td><?= $row['title'] ?></td>
                                <td class="fw-bold text-dark">$<?= number_format($row['amount'], 2) ?></td>
                                <td><?= date('M d, Y', strtotime($row['due_date'])) ?></td>
                                <td>
                                    <?php 
                                    $status = $row['status'];
                                    $badgeClass = ($status == 'paid') ? 'bg-success-subtle text-success' : (($status == 'partial') ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger');
                                    ?>
                                    <span class="badge <?= $badgeClass ?> text-uppercase" style="font-size: 0.7rem;">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-eye"></i></button>
                                        <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-print"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>