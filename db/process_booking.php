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

// Check if email exists in the database
$stmt = $pdo->prepare("SELECT * FROM airline_companies WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // If email does not exist, show modal
    echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('emailNotExistModal').style.display = 'block';
            });
        </script>
    ";
} else {
    // Proceed with sending email
    $company_email = "debysfoundation@gmail.com";
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

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = "debysfoundation.org.ng";  
        $mail->SMTPAuth = true;
        $mail->Username = "support@debysfoundation.org.ng";  
        $mail->Password = "andybestdigita@1";  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom("no-reply@debysfoundation.org.ng", "Flight Booking System");
        $mail->addAddress($company_email, "Flight Company");
        $mail->Subject = "New Flight Booking";
        $mail->isHTML(true);
        $mail->Body = $message;

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
    }
}
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
