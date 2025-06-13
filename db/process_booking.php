<?php
session_start();
require_once "db.php"; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// require_once '../PHPMailer/PHPMailer.php';
// require_once '../PHPMailer/Exception.php';
// require_once '../PHPMailer/SMTP.php';
require '../vendor/autoload.php';

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$destination = $_POST['destination'];
$departure_date = $_POST['departure_date'];

$payment_method = $_POST['payment_method'];



if (!$user) {
     header('Content-Type: application/json');
    // If email does not exist, show modal
    echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('emailNotExistModal').style.display = 'block';
            });
        </script>
    ";
} else {
     $stmt = $pdo->prepare("INSERT INTO bookings (name, email, phone, destination, departure_date, payment_method) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $destination, $departure_date, $payment_method]);

    // Proceed with sending email
    $company_email = "andrewohejiedogbu@gmail.com";
    $message = "
        <h2>New Flight Booking Request</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Destination:</strong> $destination</p>
        <p><strong>Departure Date:</strong> $departure_date</p>
        <p><strong>Payment Method:</strong> $payment_method</p>
    ";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = "delightskcompanyltd.com";
        $mail->SMTPAuth = true;
        $mail->Username = "info@delightskcompanyltd.com";
        $mail->Password = "andybest@1";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom("info@delightskcompanyltd.com", "Flight Booking System");
        $mail->addAddress($company_email, "Flight Company");
        $mail->Subject = "New Flight Booking";
        $mail->isHTML(true);
        $mail->Body = $message;
        $mail->send();
 // Add headers to improve deliverability 
        $mail->addCustomHeader('X-Mailer', 'PHPMailer');
        $mail->addCustomHeader('X-Priority', '3'); // Normal priority
        $mail->addCustomHeader('X-MSMail-Priority', 'Normal');
        // Send success email to the user
        $mail->clearAddresses();
        $mail->addAddress($email, $name);
        $mail->Subject = "Your Flight Booking Was Successful!";
        // Add your logo path (must be accessible via URL or use embed)
        $logoUrl = "./images/logo.png"; // Change to your actual logo URL
        $userMessage = "
            <div style='text-align:center;'>
                <img src='$logoUrl' alt='Company Logo' style='max-width:150px; margin-bottom:20px;'><br>
                <h2 style='color:#007bff;'>Booking Successful!</h2>
                <p>Dear $name,</p>
                <p>Your flight booking request has been received and is being processed.</p>
                <p>Thank you for choosing our airline!</p>
                <hr>
                <p style='font-size:12px;color:#888;'>If you have any questions, reply to this email.</p>
            </div>
        ";
        $mail->Body = $userMessage;
        $mail->send();

        // Show booking success modal
        echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('bookingSuccessModal').style.display = 'block';
                });
            </script>
        ";
    } catch (Exception $e) {
        echo "<script>alert('Error sending email: " . $mail->ErrorInfo . "'); window.history.back();</script>";
        error_log('Mailer Error: ' . $mail->ErrorInfo);
    }}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: white;
            padding: 20px;
            margin: 10% auto;
            width: fit-content;
            border-radius: 5px;
            text-align: center;
        }
        .modal button {
            margin-top: 10px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .modal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Email Not Exist Modal -->
    <div id="emailNotExistModal" class="modal">
        <div class="modal-content">
            <h3>Email Not Found</h3>
            <p>The email you entered does not exist. Please register first.</p>
            <button onclick="redirectToRegistration()">OK</button>
        </div>
    </div>

    <!-- Booking Success Modal -->
    <div id="bookingSuccessModal" class="modal">
        <div class="modal-content">
            <h3>Booking Successful</h3>
            <p>Your flight booking request has been submitted successfully!</p>
            <button onclick="redirectToHome()">OK</button>
        </div>
    </div>

    <script>
        function redirectToRegistration() {
            window.location.href = '../airlineregistrationform.php';
        }
        
        function redirectToHome() {
            window.location.href = '../index.php';
        }
    </script>

</body>
</html>
