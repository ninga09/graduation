<?php
require_once 'config.php';

header('Content-Type: application/json');

if (isset($_GET['admission_number'])) {
    $admissionNumber = $_GET['admission_number'];
    
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT first_name, middle_name, last_name, course, certificate_level, email FROM students_master WHERE admission_number = ?");
    $stmt->bind_param("s", $admissionNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No admission number provided']);
}
?>
