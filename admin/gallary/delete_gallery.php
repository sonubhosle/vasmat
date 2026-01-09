<?php
include 'includes/db.php';
$id = $_GET['id'];

// Delete image file
$gallery = $conn->query("SELECT image FROM gallery WHERE id=$id")->fetch_assoc();
if($gallery['image']){
    unlink("../assets/uploads/".$gallery['image']);
}

$conn->query("DELETE FROM gallery WHERE id=$id");
header("Location: gallery.php");
exit();
