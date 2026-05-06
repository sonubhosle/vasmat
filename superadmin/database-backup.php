<?php
require_once __DIR__ . '/../includes/auth_helper.php';
checkRole('superadmin');

if (isset($_GET['download'])) {
    $tables = array();
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $return = "";
    foreach ($tables as $table) {
        $result = $conn->query("SELECT * FROM " . $table);
        $num_fields = $result->field_count;

        $return .= "DROP TABLE IF EXISTS " . $table . ";";
        $row2 = $conn->query("SHOW CREATE TABLE " . $table)->fetch_row();
        $return .= "\n\n" . $row2[1] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = $result->fetch_row()) {
                $return .= "INSERT INTO " . $table . " VALUES(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) { $return .= '"' . $row[$j] . '"'; } else { $return .= '""'; }
                    if ($j < ($num_fields - 1)) { $return .= ','; }
                }
                $return .= ");\n";
            }
        }
        $return .= "\n\n\n";
    }

    // Save file
    $filename = 'db-backup-' . time() . '.sql';
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"" . $filename . "\"");
    echo $return;
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-300">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-950 min-h-screen p-6 flex flex-col fixed h-full border-r border-slate-800">
            <h1 class="text-white font-black text-sm uppercase tracking-tight mb-12">System Control</h1>
            <nav class="flex-1 space-y-2">
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">Dashboard</a>
                <a href="manage-admins.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">Admins</a>
                <a href="faculty-approvals.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">Faculty Approvals</a>
                <a href="system-logs.php" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-900 rounded-xl font-bold text-sm">System Logs</a>
                <a href="database-backup.php" class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-amber-500/20">Database Backup</a>
            </nav>
        </aside>

        <main class="flex-1 ml-64 p-10">
            <header class="mb-10">
                <h2 class="text-3xl font-black text-white tracking-tight">Database Maintenance</h2>
                <p class="text-slate-500 font-medium mt-1">Secure and export your college database system.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="bg-slate-800/30 backdrop-blur-md p-10 rounded-[2.5rem] border border-slate-700/50 shadow-2xl">
                    <div class="w-20 h-20 bg-amber-500/10 text-amber-500 rounded-3xl flex items-center justify-center text-3xl mb-8">
                        <i class="fas fa-download"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-4 uppercase tracking-tight">Quick Export</h3>
                    <p class="text-slate-400 leading-relaxed mb-10">Generate a full SQL dump of the <b>college_db</b>. This includes all users, faculty profiles, logs, and content metadata.</p>
                    <a href="?download=1" class="inline-flex items-center gap-4 px-8 py-5 bg-amber-500 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 hover:scale-105 transition-all text-sm uppercase tracking-widest">
                        <i class="fas fa-file-export"></i> Download SQL Backup
                    </a>
                </div>

                <div class="bg-slate-800/30 backdrop-blur-md p-10 rounded-[2.5rem] border border-slate-700/50 shadow-2xl opacity-50">
                    <div class="w-20 h-20 bg-blue-500/10 text-blue-500 rounded-3xl flex items-center justify-center text-3xl mb-8">
                        <i class="fas fa-clock-rotate-left"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-4 uppercase tracking-tight">Auto-Backup</h3>
                    <p class="text-slate-400 leading-relaxed mb-10">Schedule weekly or monthly cloud backups via AWS S3 or Google Drive.</p>
                    <button disabled class="px-8 py-5 bg-slate-700 text-slate-500 font-black rounded-2xl text-sm uppercase tracking-widest cursor-not-allowed">Coming Soon</button>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
