<?php
require_once 'config.php';

echo "<h2>Setting up Analytics Schema</h2>";

try {
    $conn = getDbConnection();
    
    // Create site_stats table
    // Columns: page_name (PK), visit_count
    $sql = "CREATE TABLE IF NOT EXISTS site_stats (
        page_name VARCHAR(50) PRIMARY KEY,
        visit_count INT DEFAULT 0
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>✓ Table 'site_stats' created or already exists.</p>";
    } else {
        throw new Exception("Error creating table: " . $conn->error);
    }
    
    // Initialize counters if not present
    $pages = ['index.html', 'apply.html', 'submissions'];
    
    $stmt = $conn->prepare("INSERT IGNORE INTO site_stats (page_name, visit_count) VALUES (?, 0)");
    
    foreach ($pages as $page) {
        $stmt->bind_param("s", $page);
        $stmt->execute();
    }
    
    echo "<p>✓ Counters initialized for: " . implode(", ", $pages) . "</p>";
    echo "<h3 style='color: green;'>Analytics Setup Complete!</h3>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
