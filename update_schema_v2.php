<?php
require_once 'config.php';

echo "<h2>Updating Database Schema (v2)</h2>";

try {
    $conn = getDbConnection();
    echo "<p>✓ Database connection successful!</p>";
    
    // Add columns if they don't exist
    $columns = [
        "payment_reference" => "VARCHAR(100) AFTER payment_status",
        "payment_date" => "DATE AFTER payment_reference",
        "guest_list_path" => "VARCHAR(255) AFTER num_attendees"
    ];
    
    foreach ($columns as $colName => $colDef) {
        // Check if column exists
        $checkSql = "SHOW COLUMNS FROM graduation_applications LIKE '$colName'";
        $result = $conn->query($checkSql);
        
        if ($result->num_rows == 0) {
            // Column doesn't exist, add it
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

    // Modify attendance_mode enum if needed (though it was likely VARCHAR in previous steps, let's ensure it can handle 'In Absentia')
    // If it was ENUM, we might need to alter it. If it was VARCHAR, we are fine.
    // Let's check type first.
    $checkTypeSql = "SHOW COLUMNS FROM graduation_applications LIKE 'attendance_mode'";
    $result = $conn->query($checkTypeSql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<p>ℹ Current 'attendance_mode' type: " . $row['Type'] . "</p>";
        
        // If it's an enum and doesn't have 'In Absentia', we'd need to change it.
        // Assuming it's VARCHAR based on previous context, but if not, we'll convert to VARCHAR to be safe and flexible.
        if (strpos(strtoupper($row['Type']), 'ENUM') !== false) {
             $alterSql = "ALTER TABLE graduation_applications MODIFY COLUMN attendance_mode VARCHAR(50) NOT NULL DEFAULT 'Physical'";
             if ($conn->query($alterSql) === TRUE) {
                echo "<p>✓ Converted 'attendance_mode' to VARCHAR for flexibility.</p>";
             } else {
                 echo "<p>✗ Error modifying 'attendance_mode': " . $conn->error . "</p>";
             }
        }
    } else {
        // If it doesn't exist, add it (should have been added in v1 but just in case)
        $alterSql = "ALTER TABLE graduation_applications ADD COLUMN attendance_mode VARCHAR(50) NOT NULL DEFAULT 'Physical'";
        if ($conn->query($alterSql) === TRUE) {
            echo "<p>✓ Column 'attendance_mode' added successfully.</p>";
        }
    }
    
    echo "<h3 style='color: green;'>Schema Update Complete!</h3>";
    echo "<p><a href='check_database.php'>Check Database Structure</a></p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
