<?php
session_start();
include '../config/db.php';
global $conn;

// Logic: Get Totals
$total_invoiced = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM invoices"))['total'] ?? 0;
$total_paid = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount_paid) as total FROM payments"))['total'] ?? 0;
$pending = $total_invoiced - $total_paid;

// Logic: Monthly Collections for Chart
$months = []; $amounts = [];
$chart_query = mysqli_query($conn, "SELECT DATE_FORMAT(transaction_date, '%b') as m, SUM(amount_paid) as amt FROM payments GROUP BY m ORDER BY transaction_date ASC");
while($row = mysqli_fetch_assoc($chart_query)){
    $months[] = $row['m'];
    $amounts[] = $row['amt'];
}
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include '../includes/navbar.php'; ?>
        
        <div class="container-fluid px-4 py-5">
            <h2 class="page-title mb-4">Financial Overview</h2>

            <!-- Stats Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card custom-card bg-primary text-white border-0">
                        <div class="card-body p-4">
                            <small class="text-white-50 text-uppercase fw-bold">Total Revenue</small>
                            <h2 class="fw-bold">$<?= number_format($total_paid, 2) ?></h2>
                            <i class="fa-solid fa-sack-dollar position-absolute top-50 end-0 translate-middle-y me-4 opacity-25 fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <small class="text-muted text-uppercase fw-bold">Outstanding (Invoiced)</small>
                            <h2 class="fw-bold text-danger">$<?= number_format($pending, 2) ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card custom-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <small class="text-muted text-uppercase fw-bold">Active Invoices</small>
                            <?php $inv_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM invoices WHERE status != 'paid'")); ?>
                            <h2 class="fw-bold"><?= $inv_count ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Revenue Chart -->
                <div class="col-md-8">
                    <div class="card custom-card p-4">
                        <h5 class="fw-bold mb-4">Collection Trend</h5>
                        <canvas id="revenueChart" height="150"></canvas>
                    </div>
                </div>
                <!-- Recent Payments -->
                <div class="col-md-4">
                    <div class="card custom-card">
                        <div class="card-header bg-white fw-bold">Recent Transactions</div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php
                                $recent = mysqli_query($conn, "SELECT p.*, s.first_name FROM payments p JOIN students s ON p.student_id = s.student_id ORDER BY transaction_date DESC LIMIT 5");
                                while($r = mysqli_fetch_assoc($recent)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <div class="small fw-bold"><?= $r['first_name'] ?></div>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?= $r['transaction_date'] ?></div>
                                    </div>
                                    <span class="text-success fw-bold">+$<?= number_format($r['amount_paid'], 2) ?></span>
                                </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                        <div class="card-footer bg-white text-center">
                            <a href="payments.php" class="small text-decoration-none">View All Payments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Payments Received',
            data: <?= json_encode($amounts) ?>,
            borderColor: '#4361ee',
            backgroundColor: 'rgba(67, 97, 238, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { plugins: { legend: { display: false } } }
});
</script>
<?php include '../includes/footer.php'; ?>