<?php
include 'includes/db.php';
$id = $_GET['id'];

// Delete photo
$faculty = $conn->query("SELECT photo FROM faculty WHERE id=$id")->fetch_assoc();
if($faculty['photo']){
    unlink("../assets/uploads/".$faculty['photo']);
}

$conn->query("DELETE FROM faculty WHERE id=$id");
header("Location: faculty.php");
exit();
