<?php
session_start();
include '../config/db.php';
global $conn;

// 🔐 ADMIN ONLY (Security Check)
if(!isset($_SESSION['user']) || $_SESSION['role'] != "admin"){
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    // The 'id' is the auto-increment Primary Key from the students table
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. We need the alphanumeric 'student_id' (e.g., 2026001) 
    // because that is what links students to payments and invoices.
    $find_query = mysqli_query($conn, "SELECT student_id FROM students WHERE id = '$id'");
    $student_row = mysqli_fetch_assoc($find_query);

    if ($student_row) {
        $student_id_string = $student_row['student_id'];

        // 2. Start Transaction - This ensures "All or Nothing" deletion
        mysqli_begin_transaction($conn);

        try {
            // Step A: Delete Payment Records (Targeting 'student_id')
            mysqli_query($conn, "DELETE FROM payments WHERE student_id = '$student_id_string'");
            
            // Step B: Delete Invoice Records (Targeting 'student_id')
            mysqli_query($conn, "DELETE FROM invoices WHERE student_id = '$student_id_string'");
            
            // Step C: Delete the Student record (Targeting the primary 'id')
            mysqli_query($conn, "DELETE FROM students WHERE id = '$id'");

            // If everything is successful, commit the changes
            mysqli_commit($conn);
            
            // Redirect back with a success message
            header("Location: ../students/view_students.php?msg=" . urlencode("Student and all financial history deleted successfully."));
            exit();

        } catch (Exception $e) {
            // If any query fails, undo all deletions
            mysqli_rollback($conn);
            header("Location: ../students/view_students.php?error=" . urlencode("Database Error: Could not complete deletion."));
            exit();
        }
    } else {
        header("Location: ../students/view_students.php?error=" . urlencode("Student record not found."));
        exit();
    }
} else {
    // If no ID is provided, just send them back
    header("Location: ../students/view_students.php");
    exit();
}