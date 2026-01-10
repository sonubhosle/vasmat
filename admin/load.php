<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    exit("Unauthorized");
}

if(!isset($_GET['page'])) exit("Page not found");

$page = $_GET['page'];
$allowed = ['dashboard','courses','faculty','notices','gallery','announcements','news'];

if(!in_array($page, $allowed)){
    exit("Invalid page");
}

include "pages/$page.php";
