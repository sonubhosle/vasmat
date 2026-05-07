<?php
require_once __DIR__ . '/../admin/includes/db.php';

echo "--- USERS TABLE ---\n";
$res = $conn->query("DESC users");
while($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "\n--- ALL TABLES ---\n";
$res = $conn->query("SHOW TABLES");
while($row = $res->fetch_row()) {
    echo $row[0] . "\n";
}
?>
