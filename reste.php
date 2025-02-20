<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require "vendor/autoload.php";

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];

$mail = new PHPMailer(true);

try {
    // Enable SMTP debugging (optional for troubleshooting)
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    $mail->isSMTP();
    $mail->SMTPAuth = true;
    
    $mail->Host = "debysfoundation.org.ng";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->Username = "debysfoundation@gmail.com";
    $mail->Password = "andybestdigita@1"; // WARNING: Do not expose credentials in public code!

    $mail->setFrom($email, $name);
    $mail->addAddress("debysfoundation@gmail.com", "Andrew Ohejie");

    $mail->Subject = $subject;
    $mail->Body = $message;

    $mail->send();
    echo "Mail sent successfully";
} catch (Exception $e) {
    echo "Mail could not be sent. Error: {$mail->ErrorInfo}";
}
?>
