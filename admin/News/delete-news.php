<?php
include 'protect.php';
include '../db.php';
session_start();

$id = $_GET['id'];

$conn->query("DELETE FROM news WHERE id=$id");

$_SESSION['success'] = "News Deleted!";
header("Location: news.php");
