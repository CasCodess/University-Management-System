<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 PROTECT PAGE
if(!isset($_SESSION['user']) || $_SESSION['role'] != "lecturer"){
    header("Location: ../login.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">

<?php include '../includes/sidebar.php'; ?>

<div style="margin-left:250px; width:100%;">

<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">

<h2>👨‍🏫 Lecturer Dashboard</h2>

<hr>

<div class="card p-3 mb-4">

    <h4>My Courses</h4>

    <ul>
        <li>Web Development</li>
        <li>Database Systems</li>
    </ul>

</div>

<div class="card p-3">

    <h4>Enrolled Students</h4>

    <p>
        Coming soon: students enrolled
        in your modules.
    </p>

</div>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>