<?php include '../config/db.php'; ?>
<?php global $conn; ?>

<?php include '../includes/header.php'; ?>

<?php

if(isset($_POST['submit'])){

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $faculty_id = $_POST['faculty_id'];
    $programme = $_POST['programme'];

    $sql = "
    INSERT INTO students
    (first_name, last_name, email, faculty_id, programme, registration_date)
    VALUES
    ('$first_name', '$last_name', '$email', '$faculty_id', '$programme', NOW())
    ";

    mysqli_query($conn, $sql);

    header("Location: view_student.php");
    exit();
}

?>

<div class="d-flex">

<?php include '../includes/sidebar.php'; ?>

<div style="margin-left:250px; width:100%;">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

<h2>Add Student</h2>

<form method="POST">

<input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required>

<input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required>

<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>

<select name="faculty_id" class="form-control mb-2" required>

<option value="">Select Faculty</option>

<?php
$fac = mysqli_query($conn, "SELECT * FROM faculties");
while($f = mysqli_fetch_assoc($fac)){
    echo "<option value='{$f['id']}'>{$f['id']} - {$f['name']}</option>";
}
?>

</select>

<input type="text" name="programme" class="form-control mb-2" placeholder="Programme" required>

<button type="submit" name="submit" class="btn btn-success">Add Student</button>

</form>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>