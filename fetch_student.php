<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $searchType = $_GET['s_type'] ?? 'adm'; // 'adm' or 'name'
    $searchTerm = trim($_GET['s_term'] ?? '');
    
    // Backward compatibility if s_term not provided but limit exists
    if (empty($searchTerm) && isset($_GET['admission_number'])) {
        $searchTerm = trim($_GET['admission_number']);
        $searchType = 'adm';
    }

    if (!empty($searchTerm)) {
        $conn = getDbConnection();
        if (!$conn) throw new Exception("Connection failed");

        $sql = "";
        $paramType = "";
        $paramValue = "";

        if ($searchType === 'name') {
            // Search by name (first, middle, or last)
            // Splitting search terms to allow "John Kamau" to find "John Kamau Njoroge"
            // For simplicity, let's do a broad search on full name concatenation or individual fields
            // Let's try: search term matches any part of (first + middle + last)
            $sql = "SELECT admission_number, first_name, middle_name, last_name, course, certificate_level, email FROM students_master WHERE CONCAT(first_name, ' ', IFNULL(middle_name,''), ' ', last_name) LIKE ? LIMIT 20";
            $paramType = "s";
            $paramValue = "%" . $searchTerm . "%";
        } else {
            // Default: Admission Number
            $sql = "SELECT admission_number, first_name, middle_name, last_name, course, certificate_level, email FROM students_master WHERE admission_number LIKE ? LIMIT 1";
            $paramType = "s";
            $paramValue = $searchTerm; // Exact-ish match
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) throw new Exception("Query prepare failed: " . $conn->error);

        $stmt->bind_param($paramType, $paramValue);
        
        if (!$stmt->execute()) throw new Exception("Execution failed: " . $stmt->error);

        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (count($data) > 0) {
            // Check application status for each found student
            foreach ($data as &$student) {
                $checkStmt = $conn->prepare("SELECT id FROM graduation_applications WHERE admission_number = ?");
                $checkStmt->bind_param("s", $student['admission_number']);
                $checkStmt->execute();
                $student['already_applied'] = $checkStmt->get_result()->num_rows > 0;
                $checkStmt->close();
            }
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No student found matching "' . $searchTerm . '"']);
        }
        
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'No search term provided']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
