<?php require_once '../config/db.php'; ?>
<?php global $conn; ?>

<?php

$id = $_GET['id'];

$query = "DELETE FROM faculties WHERE id=$id";

mysqli_query($conn, $query);

header("Location: view_faculties.php");
exit();

?>