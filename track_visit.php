<?php
require_once 'config.php';

// disable cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');

try {
    if (!isset($_GET['page'])) {
        throw new Exception("Page parameter missing");
    }

    $page = $_GET['page'];
    $allowedPages = ['index.html', 'apply.html'];

    if (!in_array($page, $allowedPages)) {
         throw new Exception("Invalid page");
    }

    $conn = getDbConnection();
    
    // Auto-create or increment
    $stmt = $conn->prepare("INSERT INTO site_stats (page_name, visit_count) VALUES (?, 1) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1");
    $stmt->bind_param("s", $page);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
