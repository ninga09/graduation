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
    
    // Basic server-side validation
    if (empty($firstName) || empty($lastName) || empty($admissionNumber) || empty($email) || empty($graduationYear) || empty($course) || empty($certificateLevel)) {
        die("Please fill in all required fields.");
    }

    // Handle File Upload - Only if status is 'paid'
    $targetFile = "";
    if ($paymentStatus === 'paid') {
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

    // Database Insertion
    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO graduation_applications (first_name, middle_name, last_name, admission_number, email, graduation_year, course, certificate_level, payment_status, receipt_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $firstName, $middleName, $lastName, $admissionNumber, $email, $graduationYear, $course, $certificateLevel, $paymentStatus, $targetFile);

        if ($stmt->execute()) {
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
                        <div class="logo" style="margin: 0 auto 30px;"><span class="college-initials">KISE</span></div>
                        <h1 style="color: var(--success);">Submission Successful!</h1>
                        <p style="margin: 20px 0; font-size: 1.2rem;">Thank you, <strong>' . htmlspecialchars($firstName . ' ' . $lastName) . '</strong>. Your graduation application has been received.</p>
                        <a href="index.html" class="submit-btn" style="text-decoration: none; display: inline-block; width: auto; padding: 15px 40px;">Return to Form</a>
                    </div>
                </main>
            </body>
            </html>';
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    header("Location: index.html");
    exit();
}
?>
