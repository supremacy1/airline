<?php
session_start();
require_once "db.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$departure = $_POST['departure'];
$destination = $_POST['destination'];
$departure_date = $_POST['departure_date'];
$return_date = $_POST['return_date'];
$payment_method = $_POST['payment_method'];

// Company email address (Change this to your actual email)
$company_email = "debysfoundation@gmail.com";

// Create email message
$message = "
    <h2>New Flight Booking Request</h2>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Phone:</strong> $phone</p>
    <p><strong>Departure:</strong> $departure</p>
    <p><strong>Destination:</strong> $destination</p>
    <p><strong>Departure Date:</strong> $departure_date</p>
    <p><strong>Return Date:</strong> $return_date</p>
    <p><strong>Payment Method:</strong> $payment_method</p>
";

// Send email using PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = "debysfoundation.org.ng";  // Example: smtp.gmail.com
    $mail->SMTPAuth = true;
    $mail->Username = "support@debysfoundation.org.ng";  // Your email
    $mail->Password = "andybestdigita@1";  // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Fix encryption method
    $mail->Port = 465;

    $mail->setFrom("no-reply@debysfoundation.org.ng", "Flight Booking System");
    $mail->addAddress($company_email, "Flight Company");
    $mail->Subject = "New Flight Booking";
    $mail->isHTML(true);
    $mail->Body = $message;

    $mail->send();
    
    echo "<script>alert('Booking request sent successfully!'); window.location.href = './index.php';</script>";
} catch (Exception $e) {
    echo "<script>alert('Error sending email: " . $mail->ErrorInfo . "'); window.history.back();</script>";
}
?>
