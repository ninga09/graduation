<?php
// Database configuration for KISE College Graduation Form
// Note: User will need to update these with actual credentials from cPanel

define('DB_HOST', 'mvwlkagz');
define('DB_USER', 'mvwlkagz_mvwlkagz');
define('DB_PASS', 'kisecollege(NEW)');
define('DB_NAME', 'mvwlkagz_kise_graduation_db');

function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>
