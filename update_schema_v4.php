<?php
require_once 'config.php';

echo "<h2>Updating Database Schema (V4)</h2>";

try {
    $conn = getDbConnection();
    
    // Check if column exists first
    $checkSql = "SHOW COLUMNS FROM graduation_applications LIKE 'phone_number'";
    $result = $conn->query($checkSql);
    
    if ($result->num_rows == 0) {
        $sql = "ALTER TABLE graduation_applications ADD COLUMN phone_number VARCHAR(20) AFTER email";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>✓ Column 'phone_number' added successfully.</p>";
        } else {
            throw new Exception("Error adding column: " . $conn->error);
        }
    } else {
        echo "<p style='color: orange;'>ℹ Column 'phone_number' already exists.</p>";
    }
    
    echo "<h3 style='color: green;'>Database Update Complete!</h3>";
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
