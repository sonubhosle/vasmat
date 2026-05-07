<?php
require_once __DIR__ . '/../admin/includes/db.php';

echo "--- FACULTY TABLE ---\n";
$res = $conn->query("DESC faculty");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
