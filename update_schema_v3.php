<?php
require_once 'config.php';

echo "<h2>Updating Database Schema (v3)</h2>";

try {
    $conn = getDbConnection();
    echo "<p>✓ Database connection successful!</p>";
    
    // Add columns if they don't exist
    $columns = [
        "guardian_contact" => "VARCHAR(100) AFTER email",
        "attendance_reason" => "TEXT AFTER attendance_mode"
    ];
    
    foreach ($columns as $colName => $colDef) {
        $checkSql = "SHOW COLUMNS FROM graduation_applications LIKE '$colName'";
        $result = $conn->query($checkSql);
        
        if ($result->num_rows == 0) {
            $alterSql = "ALTER TABLE graduation_applications ADD COLUMN $colName $colDef";
            if ($conn->query($alterSql) === TRUE) {
                echo "<p>✓ Column '$colName' added successfully.</p>";
            } else {
                echo "<p>✗ Error adding column '$colName': " . $conn->error . "</p>";
            }
        } else {
            echo "<p>ℹ Column '$colName' already exists.</p>";
        }
    }
    
    echo "<h3 style='color: green;'>Schema Update v3 Complete!</h3>";
    echo "<p><a href='check_database.php'>Check Database Structure</a></p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
