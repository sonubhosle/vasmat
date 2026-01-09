<?php
include 'protect.php';
include '../db.php';
session_start();

$id = $_GET['id'];

$conn->query("DELETE FROM announcements WHERE id=$id");

$_SESSION['success'] = "Announcement Deleted!";
header("Location: announcements.php");
