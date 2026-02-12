<?php
require_once 'config.php';

$stats = [];
try {
    $conn = getDbConnection();
    $result = $conn->query("SELECT * FROM site_stats");
    while ($row = $result->fetch_assoc()) {
        $stats[$row['page_name']] = $row['visit_count'];
    }
    $conn->close();
} catch (Exception $e) {
    die("Error fetching stats: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Analytics | KISE Graduation</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #f8fafc; color: #1e293b; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .dashboard { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 500px; text-align: center; }
        h1 { margin-bottom: 30px; color: #1a4a8e; }
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .stat-card { background: #f1f5f9; padding: 20px; border-radius: 12px; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); background: #e2e8f0; }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: #1a4a8e; display: block; margin-bottom: 5px; }
        .stat-label { color: #64748b; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .submission-card { grid-column: 1 / -1; background: #dcfce7; color: #166534; }
        .submission-card .stat-number { color: #15803d; }
        .refresh-btn { margin-top: 30px; padding: 10px 20px; background: #1a4a8e; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: inherit; }
        .refresh-btn:hover { background: #1e40af; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>📊 Site Analytics</h1>
        <div class="stat-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo number_format($stats['index.html'] ?? 0); ?></span>
                <span class="stat-label">Home Visits</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo number_format($stats['apply.html'] ?? 0); ?></span>
                <span class="stat-label">Form Views</span>
            </div>
            <div class="stat-card submission-card">
                <span class="stat-number"><?php echo number_format($stats['submissions'] ?? 0); ?></span>
                <span class="stat-label">Successful Submissions</span>
            </div>
        </div>
        <button class="refresh-btn" onclick="location.reload()">Refresh Data</button>
        <p style="margin-top: 20px; font-size: 0.8rem; color: #94a3b8;">Data updates in real-time</p>
    </div>
</body>
</html>
