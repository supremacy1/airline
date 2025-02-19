<?php
session_start();
require_once "db.php";
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $_POST["company_name"];
    $iata_code = $_POST["iata_code"];
    $icao_code = $_POST["icao_code"];
    $contact_person = $_POST["contact_person"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $country = $_POST["country"];
    $language = $_POST["language"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM airline_companies WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists!", "redirect" => true]);
        exit();
    }

    // If passwords don't match, return an error (but don't redirect)
    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!", "redirect" => false]);
        exit();
    }

    // Hash password after validation
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new airline company
    $stmt = $pdo->prepare("INSERT INTO airline_companies (company_name, iata_code, icao_code, contact_person, email, phone, country, language, password) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$company_name, $iata_code, $icao_code, $contact_person, $email, $phone, $country, $language, $hashed_password])) {
        
        // Send welcome email
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'debysfoundation@gmail.com'; // Replace with your email
            $mail->Password = 'fmtg jqrk hvot qabt'; // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587; 

            // Email settings
            $mail->setFrom('debysfoundation@gmail.com', 'Your Company');
            $mail->addAddress($email, $contact_person);
            $mail->Subject = "Welcome to Our Airline Platform!";
            $mail->isHTML(true);
            $mail->Body = "
                <h2>Welcome, $contact_person!</h2>
                <p>Thank you for registering your airline company, <strong>$company_name</strong>. We're excited to have you on board!</p>
                <p>Here are your details:</p>
                <ul>
                    <li><strong>Company Name:</strong> $company_name</li>
                    <li><strong>IATA Code:</strong> $iata_code</li>
                    <li><strong>ICAO Code:</strong> $icao_code</li>
                    <li><strong>Contact Email:</strong> $email</li>
                </ul>
                <p>If you have any questions, feel free to contact our support team.</p>
                <p>Best Regards,<br>Your Company</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Log error (do not expose details to the user)
            error_log("Email sending failed: " . $mail->ErrorInfo);
        }

        echo json_encode(["status" => "success", "message" => "Registration Successful! A welcome email has been sent.", "redirect" => true]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed. Try again.", "redirect" => false]);
    }
    exit();
}
?>
