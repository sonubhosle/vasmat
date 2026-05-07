<?php
require_once __DIR__ . '/../admin/includes/db.php';

$queries = [
    "ALTER TABLE faculty ADD COLUMN achievements TEXT AFTER experience",
    "ALTER TABLE faculty ADD COLUMN about TEXT AFTER achievements",
    "ALTER TABLE faculty ADD COLUMN dob DATE AFTER about",
    "ALTER TABLE faculty ADD COLUMN resume VARCHAR(255) AFTER dob"
];

foreach ($queries as $sql) {
    if ($conn->query($sql)) {
        echo "Successfully executed: $sql\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}
?>
