<?php
require_once 'config.php';

echo "<h1>KISE Graduation Portal - Database Diagnostic</h1>";

try {
    $conn = getDbConnection();
    echo "<p style='color:green;'>✅ Connection Successful!</p>";

    // Check for mysqlnd
    if (method_exists('mysqli_stmt', 'get_result')) {
        echo "<p style='color:green;'>✅ Driver: mysqlnd (Normal)</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ Driver: Generic (Fallback Required - I've implemented this in fetch_student.php)</p>";
    }

    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'students_master'");
    if ($result->num_rows > 0) {
        echo "<p style='color:green;'>✅ Table 'students_master' exists.</p>";
        
        // Count entries
        $count = $conn->query("SELECT COUNT(*) as total FROM students_master")->fetch_assoc()['total'];
        echo "<p>📊 Total students in database: <strong>$count</strong></p>";
        
        if ($count == 0) {
            echo "<p style='color:orange;'>⚠️ Warning: No students found. Did you run the schema.sql?</p>";
        } else {
            echo "<h3>Current Data in Table:</h3><ul>";
            $samples = $conn->query("SELECT admission_number, first_name, last_name, course FROM students_master");
            while ($row = $samples->fetch_assoc()) {
                echo "<li><strong>[" . htmlspecialchars($row['admission_number']) . "]</strong> - " . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . " (" . htmlspecialchars($row['course']) . ")</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color:red;'>❌ Error: Table 'students_master' does NOT exist. Please create it using schema.sql.</p>";
    }

    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
