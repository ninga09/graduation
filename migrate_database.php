<?php
require_once 'config.php';

echo "<h2>Database Migration - Recreate graduation_applications Table</h2>";

try {
    $conn = getDbConnection();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Step 1: Drop the old table
    echo "<h3>Step 1: Dropping old table...</h3>";
    $conn->query("DROP TABLE IF EXISTS graduation_applications");
    echo "<p style='color: orange;'>✓ Old table dropped (if it existed)</p>";
    
    // Step 2: Create the new table with correct structure
    echo "<h3>Step 2: Creating new table with correct structure...</h3>";
    $sql = "CREATE TABLE graduation_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        middle_name VARCHAR(100),
        last_name VARCHAR(100) NOT NULL,
        admission_number VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        graduation_year INT NOT NULL,
        course VARCHAR(255) NOT NULL,
        certificate_level ENUM('Degree', 'Diploma', 'Certificate') NOT NULL,
        payment_status ENUM('paid', 'not_paid') DEFAULT 'not_paid',
        receipt_path VARCHAR(255),
        num_attendees INT DEFAULT 1,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ New table 'graduation_applications' created successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating table: " . $conn->error . "</p>";
    }
    
    // Step 3: Verify the new structure
    echo "<h3>Step 3: Verifying new table structure...</h3>";
    $result = $conn->query("DESCRIBE graduation_applications");
    if ($result) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<br><h3 style='color: green;'>✅ Migration Complete!</h3>";
    echo "<p><strong>IMPORTANT:</strong> Please delete this migrate_database.php file for security.</p>";
    echo "<p><a href='apply.html'>Go to Application Form</a></p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
