<?php
session_start();
include '../config/db.php';
global $conn;

// Fetch active invoices for the dropdown
$invoices = mysqli_query($conn, "SELECT i.id, i.amount, i.title, s.first_name, s.last_name 
                                FROM invoices i 
                                JOIN students s ON i.student_id = s.student_id 
                                WHERE i.status != 'paid'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_id = $_POST['invoice_id'];
    $amount_paid = $_POST['amount_paid'];
    $transaction_date = $_POST['transaction_date'];
    $status = 'Completed';

    // Get student_id from the invoice
    $inv_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT student_id FROM invoices WHERE id = '$invoice_id'"));
    $student_id = $inv_data['student_id'];

    // Insert using your specific columns: invoice_id, student_id, amount_paid, transaction_date, status
    $sql = "INSERT INTO payments (invoice_id, student_id, amount_paid, transaction_date, status) 
            VALUES ('$invoice_id', '$student_id', '$amount_paid', '$transaction_date', '$status')";

    if (mysqli_query($conn, $sql)) {
        // Automatically mark the invoice as paid
        mysqli_query($conn, "UPDATE invoices SET status = 'paid' WHERE id = '$invoice_id'");
        header("Location: payments.php");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>
        
        <div class="container-fluid py-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-4">
                        <h4 class="fw-bold mb-4">Record New Payment</h4>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Select Invoice</label>
                                <select name="invoice_id" class="form-select bg-light" required>
                                    <option value="">-- Select Pending Invoice --</option>
                                    <?php while($row = mysqli_fetch_assoc($invoices)): ?>
                                        <option value="<?= $row['id'] ?>">
                                            #<?= $row['id'] ?> - <?= $row['first_name'] ?> (<?= $row['title'] ?>: $<?= $row['amount'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Amount Paid ($)</label>
                                    <input type="number" step="0.01" name="amount_paid" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Date</label>
                                    <input type="date" name="transaction_date" class="form-control bg-light" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Confirm Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>