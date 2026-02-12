<?php
require_once 'config.php';

echo "<h2>Database Update: Adding Attendance Mode</h2>";

try {
    $conn = getDbConnection();
    echo "<p>✓ Connected to database</p>";
    
    // Add attendance_mode column
    $sql = "ALTER TABLE graduation_applications ADD COLUMN attendance_mode ENUM('Physical', 'Online') DEFAULT 'Physical' AFTER num_attendees";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Successfully added 'attendance_mode' column!</p>";
    } else {
        // Check if column already exists
        if (strpos($conn->error, "Duplicate column name") !== false) {
             echo "<p style='color: orange;'>⚠ Column 'attendance_mode' already exists.</p>";
        } else {
             echo "<p style='color: red;'>✗ Error adding column: " . $conn->error . "</p>";
        }
    }
    
    echo "<h3>Current Columns:</h3>";
    $result = $conn->query("DESCRIBE graduation_applications");
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    }
    
    echo "<p><strong>Update Complete!</strong> You can delete this file now.</p>";
    echo "<a href='apply.html'>Go to Form</a>";
    
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
