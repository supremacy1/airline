<?php
session_start();
require_once "db.php";
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json"); // Ensure JSON response
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    // Check password match
    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match!", "redirect" => false]);
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO airline_companies (company_name, iata_code, icao_code, contact_person, email, phone, country, language, password) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt->execute([$company_name, $iata_code, $icao_code, $contact_person, $email, $phone, $country, $language, $hashed_password])) {
        
        // Send welcome email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'debysfoundation.org.ng'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'support@debysfoundation.org.ng'; 
            $mail->Password = 'andybestdigita@1'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Fix encryption method
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('no-reply@debysfoundation.org.ng', 'Airline Company');
            $mail->addAddress($email, $contact_person);

            // Email Content
            $mail->isHTML(false);
            $mail->Subject = "Welcome to Our Airline Company!";
            $mail->Body = "Dear $contact_person,\n\nThank you for registering with us. We are excited to have you on board!!\n\nBest regards,\nThe Airline Team";

            $mail->send();

        } catch (Exception $e) {
            // Log error but continue registration
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }

        echo json_encode(["status" => "success", "message" => "Registration Successful!", "redirect" => true]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed. Try again.", "redirect" => false]);
    }
    exit();
}
?>
