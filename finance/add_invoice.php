<?php
session_start();
include '../config/db.php';
global $conn;

// Check admin access
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all students for the dropdown
$students_query = "SELECT student_id, first_name, last_name FROM students ORDER BY last_name ASC";
$students_result = mysqli_query($conn, $students_query);

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $insert_query = "INSERT INTO invoices (student_id, title, amount, due_date, status) 
                     VALUES ('$student_id', '$title', '$amount', '$due_date', '$status')";

    if (mysqli_query($conn, $insert_query)) {
        header("Location: invoices.php?msg=Invoice created successfully");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<?php include '../includes/header.php'; ?>
<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <?php include '../includes/navbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-primary">Generate New Student Invoice</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                            
                            <form action="" method="POST">
                                <div class="row">
                                    <!-- Student Selection -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold">Select Student</label>
                                        <select name="student_id" class="form-select" required>
                                            <option value="">-- Choose Student --</option>
                                            <?php while($s = mysqli_fetch_assoc($students_result)): ?>
                                                <option value="<?= $s['student_id'] ?>">
                                                    <?= $s['last_name'] . ", " . $s['first_name'] ?> (ID: <?= $s['student_id'] ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <!-- Invoice Details -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-semibold">Fee Description</label>
                                        <input type="text" name="title" class="form-control" placeholder="e.g. Tuition Fee - Fall 2026" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Amount ($)</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Due Date</label>
                                        <input type="date" name="due_date" class="form-control" required>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <label class="form-label fw-semibold">Initial Status</label>
                                        <select name="status" class="form-select">
                                            <option value="unpaid">Unpaid</option>
                                            <option value="partial">Partial</option>
                                            <option value="paid">Paid (Pre-paid)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="invoices.php" class="btn btn-light px-4">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-5">Generate Invoice</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>