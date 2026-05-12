<?php require_once '../config/db.php'; ?>
<?php global $conn; ?>

<?php

// GET ID
$id = $_GET['id'];

// FETCH EXISTING DATA
$query = "SELECT * FROM faculties WHERE id = $id";
$result = mysqli_query($conn, $query);
$faculty = mysqli_fetch_assoc($result);

// UPDATE DATA
if(isset($_POST['update'])){

    $name = $_POST['name'];
    $code = $_POST['code'];

    $update = "UPDATE faculties 
               SET name='$name', code='$code' 
               WHERE id=$id";

    if(mysqli_query($conn, $update)){
        header("Location: view_faculties.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">

    <?php include '../includes/sidebar.php'; ?>

    <div style="margin-left:250px; width:100%;">

        <?php include '../includes/navbar.php'; ?>

        <div class="container mt-4">

            <h2>Edit Faculty</h2>

            <form method="POST">

                <div class="mb-3">
                    <label>Faculty Name</label>
                    <input type="text" name="name"
                           value="<?php echo $faculty['name']; ?>"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Faculty Code</label>
                    <input type="text" name="code"
                           value="<?php echo $faculty['code']; ?>"
                           class="form-control">
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    Update Faculty
                </button>

            </form>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>