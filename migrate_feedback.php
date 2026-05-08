<?php
require_once "admin/includes/db.php";

$queries = [
    "ALTER TABLE feedback MODIFY COLUMN category VARCHAR(100)",
    "ALTER TABLE feedback MODIFY COLUMN user_type VARCHAR(50)"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Successfully executed: $q\n";
    } else {
        echo "Error executing $q: " . $conn->error . "\n";
    }
}
?>
