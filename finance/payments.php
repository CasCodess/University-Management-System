<?php
session_start();
include '../config/db.php';
global $conn;

// Logic: Fetch payments joined with Student names and Invoice titles
$query = "SELECT p.*, s.first_name, s.last_name, i.title as inv_title 
          FROM payments p 
          LEFT JOIN students s ON p.student_id = s.student_id 
          LEFT JOIN invoices i ON p.invoice_id = i.id 
          ORDER BY p.transaction_date DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>

        <div class="container-fluid px-4 py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Payment History</h2>
                <a href="add_payment.php" class="btn btn-success shadow-sm px-4">
                    <i class="fa-solid fa-plus me-2"></i> Record New Payment
                </a>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Ref #</th>
                                <th>Student</th>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4 fw-bold">#PAY-<?= $row['payment_id'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></div>
                                    <small class="text-muted">ID: <?= $row['student_id'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['inv_title'] ?? 'Direct Payment') ?></td>
                                <td><?= date('M d, Y', strtotime($row['transaction_date'])) ?></td>
                                <td class="fw-bold text-success">$<?= number_format($row['amount_paid'], 2) ?></td>
                                <td>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                        <?= ucfirst($row['status'] ?? 'Completed') ?>
                                    </span>
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