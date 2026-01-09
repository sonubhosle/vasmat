<?php
include 'includes/db.php';
$id = $_GET['id'];

// Delete file
$notice = $conn->query("SELECT file FROM notices WHERE id=$id")->fetch_assoc();
if($notice['file']){
    unlink("../assets/uploads/".$notice['file']);
}

$conn->query("DELETE FROM notices WHERE id=$id");
header("Location: notices.php");
exit();
