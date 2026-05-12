<?php require_once '../config/db.php'; ?>
<?php global $conn; ?>

<?php include '../includes/header.php'; ?>

<?php

$message = "";

if(isset($_POST['submit'])){

    $name = trim($_POST['name']);
    $code = trim($_POST['code']);

    // Basic validation
    if(empty($name)){
        $message = "Faculty name is required!";
    } else {

        // SAFE QUERY (prevents SQL injection)
        $stmt = mysqli_prepare($conn, "INSERT INTO faculties (name, code) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $code);

        if(mysqli_stmt_execute($stmt)){
            $message = "Faculty added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

?>

<div class="d-flex">

    <?php include '../includes/sidebar.php'; ?>

    <div style="margin-left:250px; width:100%;">

        <?php include '../includes/navbar.php'; ?>

        <div class="container mt-4">

            <h2>Add Faculty</h2>

            <?php if($message != ""): ?>
                <div class="alert alert-info">
                    <?= $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label>Faculty Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Faculty Code</label>
                    <input type="text" name="code" class="form-control"
                           placeholder="e.g. FCI, ENG, BUS">
                </div>

                <button type="submit" name="submit" class="btn btn-success">
                    Save Faculty
                </button>

            </form>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>