<?php include '../config/db.php'; ?>
<?php global $conn; ?>
<?php include '../includes/header.php'; ?>

<div class="d-flex">

<?php include '../includes/sidebar.php'; ?>

<div style="margin-left:250px; width:100%;">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

<h2>Users</h2>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Username</th>
<th>Role</th>
<th>Created</th>
</tr>

<?php

$result = mysqli_query($conn, "SELECT * FROM users");

while($row = mysqli_fetch_assoc($result)){
    echo "
    <tr>
        <td>{$row['id']}</td>
        <td>{$row['username']}</td>
        <td>{$row['role']}</td>
        <td>{$row['created_at']}</td>
    </tr>
    ";
}

?>

</table>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>