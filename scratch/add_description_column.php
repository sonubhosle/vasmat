<?php
require_once __DIR__ . '/../admin/includes/db.php';

$sql = "ALTER TABLE faculty_content ADD COLUMN description TEXT AFTER title";

if ($conn->query($sql)) {
    echo "Column 'description' added successfully to 'faculty_content'.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}
?>
