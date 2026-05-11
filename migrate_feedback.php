<?php
require_once "admin/includes/db.php";

// Run migration queries (if needed)
$queries = [
    "ALTER TABLE feedback MODIFY COLUMN category VARCHAR(100)",
    "ALTER TABLE feedback MODIFY COLUMN user_type VARCHAR(50)"
];

foreach ($queries as $q) {
    if ($conn->query($q) === TRUE) {
        echo "<p style=\"color:green;\">Successfully executed: " . htmlspecialchars($q) . "</p>\n";
    } else {
        echo "<p style=\"color:orange;\">Notice: could not execute " . htmlspecialchars($q) . ": " . htmlspecialchars($conn->error) . "</p>\n";
    }
}

// Fetch real feedback data and display in a table
echo "<h2>Feedback rows (latest first)</h2>\n";
try {
    $res = $conn->query("SELECT id, user_type, category, rating, comments, anonymous, created_at FROM feedback ORDER BY created_at DESC LIMIT 1000");
    if ($res && $res !== false) {
        if ($res->num_rows === 0) {
            echo "<p>No feedback rows found.</p>\n";
        } else {
            echo "<table border=1 cellpadding=8 cellspacing=0 style=\"border-collapse:collapse;max-width:100%;width:100%\">\n";
            echo "<thead><tr style=\"background:#f3f4f6;text-align:left\"><th>ID</th><th>User Type</th><th>Category</th><th>Rating</th><th>Anonymous</th><th>Created At</th><th>Comments</th></tr></thead><tbody>\n";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
                echo "<td>" . (empty($row['anonymous']) ? 'No' : 'Yes') . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "<td style=\"max-width:520px;overflow:auto;white-space:pre-wrap;\">" . nl2br(htmlspecialchars($row['comments'])) . "</td>";
                echo "</tr>\n";
            }
            echo "</tbody></table>\n";
        }
    } else {
        echo "<p style=\"color:red\">Unable to query feedback table: " . htmlspecialchars($conn->error) . "</p>\n";
    }
} catch (Throwable $e) {
    echo "<p style=\"color:red\">Error fetching feedback: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>
