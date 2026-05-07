<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole(['admin', 'superadmin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id > 0 && in_array($action, ['approve', 'reject'])) {
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update faculty_content table
        $stmt = $conn->prepare("UPDATE faculty_content SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        
        // If approved, sync with specific tables (notes, syllabus, etc.) based on requirement
        // First, get the content details
        $stmt_get = $conn->prepare("SELECT * FROM faculty_content WHERE id = ?");
        $stmt_get->bind_param("i", $id);
        $stmt_get->execute();
        $content = $stmt_get->get_result()->fetch_assoc();
        
        if ($status === 'approved' && $content) {
            // Depending on type, we might want to insert into existing notes/syllabus tables
            // For now, the requirement says "Only approved content should be visible on website"
            // We can either update the existing tables OR modify the public views to also check faculty_content
            
            // To keep it clean, let's assume we use faculty_content for everything new.
            logActivity($conn, $_SESSION['user_id'], 'Approval', 'Admin ' . $action . 'd content ID: ' . $id);
        } else {
            logActivity($conn, $_SESSION['user_id'], 'Rejection', 'Admin ' . $action . 'd content ID: ' . $id);
        }

        // Notify the Faculty Member
        if ($content) {
            $stmt_usr = $conn->prepare("SELECT id FROM users WHERE reference_id = ? AND role = 'faculty'");
            $stmt_usr->bind_param("i", $content['faculty_id']);
            $stmt_usr->execute();
            $user_data = $stmt_usr->get_result()->fetch_assoc();
            if ($user_data) {
                $notif_title = ($status === 'approved') ? 'Submission Approved' : 'Submission Rejected';
                $notif_msg = ($status === 'approved') 
                    ? "Great news! Your submission '{$content['title']}' has been approved and is now live." 
                    : "Your submission '{$content['title']}' was rejected. Please review the guidelines or contact the admin.";
                addNotification($conn, $user_data['id'], $notif_title, $notif_msg);
            }
            $stmt_usr->close();
        }

        $conn->commit();
        $_SESSION['success'] = "Content successfully " . $status . ".";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error processing request: " . $e->getMessage();
    }
}

header("Location: index.php");
exit;
?>
