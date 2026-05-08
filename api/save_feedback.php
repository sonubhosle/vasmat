<?php
include "../admin/includes/db.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? 'General';
    $rating = $_POST['rating'] ?? 0;
    $comments = $_POST['comments'] ?? '';
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    $user_type = $_POST['user_type'] ?? 'Student';
    
    $stmt = $conn->prepare("INSERT INTO feedback (user_type, category, rating, comments) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $user_type, $category, $rating, $comments);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your valuable feedback!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
    }
}
?>
