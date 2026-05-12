<?php
include 'config/db.php';
global $conn;

$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$sql = "INSERT INTO users (username, password, role)
        VALUES ('$username', '$password', '$role')";

if(mysqli_query($conn, $sql)){
    echo "Admin created successfully. Login: admin / admin123";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>