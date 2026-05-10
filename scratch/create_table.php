<?php
require_once 'c:/xampp/htdocs/vasmat/admin/includes/db.php';

$sql = "CREATE TABLE IF NOT EXISTS academic_calendars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table academic_calendars created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
