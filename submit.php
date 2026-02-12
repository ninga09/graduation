<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $admissionNumber = $_POST['admissionNumber'] ?? '';
    $email = $_POST['email'] ?? '';
    $graduationYear = $_POST['graduationYear'] ?? '';
    $course = $_POST['course'] ?? '';
    $certificateLevel = $_POST['certificateLevel'] ?? '';
    $paymentStatus = $_POST['paymentStatus'] ?? 'not_paid';
    $numAttendees = $_POST['numAttendees'] ?? 0;
    $attendanceMode = $_POST['attendanceMode'] ?? 'Physical';
    $paymentReference = $_POST['paymentReference'] ?? '';
    $paymentDate = $_POST['paymentDate'] ?? NULL; // Allow NULL if not paid
    if ($paymentDate === '') $paymentDate = NULL; // Handle empty string from date input
    $guardianContact = $_POST['guardianContact'] ?? '';
    $studentPhone = $_POST['studentPhone'] ?? '';
    $attendanceReason = $_POST['attendanceReason'] ?? ''; // Reason for Online/Absentia

    
    // Basic server-side validation
    // Note: payment fields validated if status is paid below
    if (empty($firstName) || empty($lastName) || empty($admissionNumber) || empty($email) || empty($studentPhone) || empty($graduationYear) || empty($course) || empty($certificateLevel) || empty($attendanceMode)) {
        die("Please fill in all required fields.");
    }
    
    // Validate Reason if not Physical
    if ($attendanceMode !== 'Physical' && empty($attendanceReason)) {
        die("Please provide a reason for " . $attendanceMode . " attendance.");
    }

    // Handle File Upload - Only if status is 'paid'
    $targetFile = "";
    if ($paymentStatus === 'paid') {
        if (empty($paymentReference) || empty($paymentDate)) {
             die("Please provide payment reference and date.");
        }
        if (!isset($_FILES["paymentReceipt"]) || $_FILES["paymentReceipt"]["error"] !== UPLOAD_ERR_OK) {
            die("Please upload your payment receipt.");
        }

        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileInfo = pathinfo($_FILES["paymentReceipt"]["name"]);
        $fileName = $admissionNumber . "_" . time() . "." . $fileInfo['extension'];
        $targetFile = $targetDir . basename($fileName);
        $fileType = strtolower($fileInfo['extension']);

        if ($_FILES["paymentReceipt"]["size"] > 5000000) {
            die("Sorry, your file is too large.");
        }

        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            die("Sorry, only JPG, JPEG, PNG & PDF files are allowed.");
        }

        if (!move_uploaded_file($_FILES["paymentReceipt"]["tmp_name"], $targetFile)) {
            die("Sorry, there was an error uploading your file.");
        }
    }

    // Handle Guest List Upload
    $guestListFile = "";
    if ($attendanceMode === 'Physical' && $numAttendees > 0 && isset($_FILES['guestList']) && $_FILES['guestList']['error'] === UPLOAD_ERR_OK) {
         $targetDir = "uploads/";
         // Ensure dir exists (already checked above but safe to double check or just reuse)
         if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

         $gFileInfo = pathinfo($_FILES["guestList"]["name"]);
         $gFileName = "GuestList_" . $admissionNumber . "_" . time() . "." . $gFileInfo['extension'];
         $guestListFile = $targetDir . basename($gFileName);
         
         // Basic type check
         $allowedDocTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
         if (!in_array(strtolower($gFileInfo['extension']), $allowedDocTypes)) {
             die("Invalid file type for guest list.");
         }

         if (!move_uploaded_file($_FILES["guestList"]["tmp_name"], $guestListFile)) {
             die("Error uploading guest list.");
         }
    }

    // Database Insertion
    try {
        $conn = getDbConnection();
        
        // --- PREVENT MULTIPLE APPLICATIONS ---
        $checkStmt = $conn->prepare("SELECT id FROM graduation_applications WHERE admission_number = ?");
        $checkStmt->bind_param("s", $admissionNumber);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            die("<div style='color: #856404; background-color: #fff3cd; border: 1px solid #ffeeba; padding: 20px; border-radius: 8px; text-align: center; font-family: sans-serif;'>
                    <h3>Already Applied!</h3>
                    <p>Our records show that an application for Admission Number <strong>" . htmlspecialchars($admissionNumber) . "</strong> has already been submitted.</p>
                    <p>If you believe this is an error, please contact the administration.</p>
                    <a href='index.html' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #1a4a8e; color: white; text-decoration: none; border-radius: 5px;'>Return Home</a>
                 </div>");
        }
        $checkStmt->close();
        // --------------------------------------

        // Prepare Statement
        $sql = "INSERT INTO graduation_applications (first_name, middle_name, last_name, admission_number, email, phone_number, graduation_year, course, certificate_level, payment_status, receipt_path, num_attendees, attendance_mode, payment_reference, payment_date, guest_list_path, guardian_contact, attendance_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
             // This usually happens if the database schema doesn't match the query (e.g., missing columns)
             throw new Exception("Database Prepare Error: " . $conn->error . " (Likely missing columns. Run update_schema_v4.php)");
        }

        $stmt->bind_param("ssssssssssisssssss", $firstName, $middleName, $lastName, $admissionNumber, $email, $studentPhone, $graduationYear, $course, $certificateLevel, $paymentStatus, $targetFile, $numAttendees, $attendanceMode, $paymentReference, $paymentDate, $guestListFile, $guardianContact, $attendanceReason);

        if ($stmt->execute()) {
            
            // --- ANALYTICS: Track Submission ---
            try {
                $analyticsSql = "INSERT INTO site_stats (page_name, visit_count) VALUES ('submissions', 1) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1";
                $conn->query($analyticsSql);
            } catch (Exception $e) {
                // Ignore analytics errors, don't stop submission
            }
            // -----------------------------------
            
            echo '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Success | KISE College</title>
                <link rel="stylesheet" href="style.css">
            </head>
            <body>
                <div class="background-decor"></div>
                <main class="container">
                    <div class="premium-form" style="text-align: center;">
                        <div class="logo"><img src="kiselogo.gif" alt="KISE College Logo"></div>
                        <h1 style="color: var(--success);">Submission Successful!</h1>
                        <p style="margin: 20px 0; font-size: 1.2rem;">Thank you, <strong>' . htmlspecialchars($firstName . ' ' . $lastName) . '</strong>. Your graduation application has been received.</p>
                        <p style="margin-bottom: 30px; color: var(--text-muted);">ℹ️ Please note: Final confirmation of the graduation list will be done on the 20th March 2026.</p>
                        <a href="index.html" class="submit-btn" style="text-decoration: none; display: inline-block; width: auto; padding: 15px 40px;">Return to Home</a>
                    </div>
                </main>
            </body>
            </html>';
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();
    } catch (Throwable $e) {
        // Catch both Exceptions and Fatal Errors (PHP 7+)
        http_response_code(500);
        echo "<div style='color: red; padding: 20px; border: 1px solid red; background: #ffeeee;'>";
        echo "<h3>Submission Failed</h3>";
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Please ensure your database is updated with the new columns.</p>";
        echo "</div>";
    }
} else {
    header("Location: index.html");
    exit();
}
?>
