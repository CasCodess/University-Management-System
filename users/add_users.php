<?php include '../config/db.php'; ?>
<?php global $conn; ?>
<?php include '../includes/header.php'; ?>

<?php

$message = "";

if(isset($_POST['submit'])){

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role)
            VALUES ('$username', '$password', '$role')";

    if(mysqli_query($conn, $sql)){
        $message = "User created successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

?>

<div class="d-flex">

<?php include '../includes/sidebar.php'; ?>

<div style="margin-left:250px; width:100%;">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

<h2>Add User</h2>

<?php if($message): ?>
<div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

<input type="text" name="username" class="form-control mb-2" placeholder="Username" required>

<input type="password" name="password" class="form-control mb-2" placeholder="Password" required>

<select name="role" class="form-control mb-2">
    <option value="admin">Admin</option>
    <option value="lecturer">Lecturer</option>
</select>

<button type="submit" name="submit" class="btn btn-success">Create User</button>

</form>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>