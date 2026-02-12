<?php
require_once 'config.php';

echo "<h2>KISE College Database Setup</h2>";

try {
    $conn = getDbConnection();
    echo "<p>✓ Database connection successful!</p>";
    
    // Create students_master table
    $sql1 = "CREATE TABLE IF NOT EXISTS students_master (
        admission_number VARCHAR(50) PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        middle_name VARCHAR(100),
        last_name VARCHAR(100) NOT NULL,
        course VARCHAR(255) NOT NULL,
        certificate_level ENUM('Degree', 'Diploma', 'Certificate') NOT NULL,
        email VARCHAR(100)
    )";
    
    if ($conn->query($sql1) === TRUE) {
        echo "<p>✓ Table 'students_master' created successfully (or already exists)</p>";
    } else {
        echo "<p>✗ Error creating students_master table: " . $conn->error . "</p>";
    }
    
    // Create graduation_applications table
    $sql2 = "CREATE TABLE IF NOT EXISTS graduation_applications (
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
    
    if ($conn->query($sql2) === TRUE) {
        echo "<p>✓ Table 'graduation_applications' created successfully (or already exists)</p>";
    } else {
        echo "<p>✗ Error creating graduation_applications table: " . $conn->error . "</p>";
    }
    
    // Insert sample data
    $sql3 = "INSERT IGNORE INTO students_master (admission_number, first_name, middle_name, last_name, course, certificate_level, email) VALUES 
    ('2025-06', 'John', 'Kamau', 'Njoroge', 'Diploma in Special Needs Education', 'Diploma', 'john.kamau@kisecollege.ac.ke'),
    ('2025-07', 'Mary', '', 'Wambui', 'Certificate in Early Childhood Education', 'Certificate', 'mary.wambui@kisecollege.ac.ke')";
    
    if ($conn->query($sql3) === TRUE) {
        echo "<p>✓ Sample data inserted successfully</p>";
    } else {
        echo "<p>✗ Error inserting sample data: " . $conn->error . "</p>";
    }
    
    echo "<h3 style='color: green;'>Setup Complete!</h3>";
    echo "<p><strong>IMPORTANT:</strong> For security, please delete this setup.php file after running it once.</p>";
    echo "<p><a href='index.html'>Go to Graduation Application Form</a></p>";
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
