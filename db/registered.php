<?php
session_start();

// Include database connection
require_once 'db.php';

// Include PHPMailer
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $iata_code = $_POST['iata_code'];
    $icao_code = $_POST['icao_code'];
    $contact_person = $_POST['contact_person'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $country = $_POST['country'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = 'Email already exists!';
        $_SESSION['redirect'] = 'registration.html';
        header("Location: registration.html");
        exit;
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO users (company_name, iata_code, icao_code, contact_person, email, phone, country, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$company_name, $iata_code, $icao_code, $contact_person, $email, $phone, $country, $password])) {
        // Send welcome email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'debysfoundation.org.ng'; // Your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'support@debysfoundation.org.ng'; // SMTP username
            $mail->Password = 'andybestdigita@1'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STMPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('no-reply@debysfoundation.org.ng', 'Airline Company');
            $mail->addAddress($email, $contact_person);

            // Content
            $mail->isHTML(false);
            $mail->Subject = "Welcome to Our Airline Company!";
            $mail->Body = "Dear $contact_person,\n\nThank you for registering with us. We are excited to have you on board!\n\nBest regards,\nThe Airline Team";

            $mail->send();
            $_SESSION['message'] = 'Registration successful! Welcome email sent.';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Registration successful! However, the welcome email could not be sent.';
        }
        $_SESSION['redirect'] = 'success.html';
        header("Location: registration.html");
    } else {
        $_SESSION['message'] = 'Registration failed!';
        $_SESSION['redirect'] = 'registration.html';
        header("Location: registration.html");
    }
}
?>

<?php
session_start();
require_once "db.php";
require '../vendor/autoload.php';

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
 // Send welcome email using PHPMailer
 $mail = new PHPMailer(true);
 try {
     // Server settings
     $mail->isSMTP();
     $mail->Host = 'debysfoundation.org.ng'; // Your SMTP server
     $mail->SMTPAuth = true;
     $mail->Username = 'support@debysfoundation.org.ng'; // SMTP username
     $mail->Password = 'andybestdigita@1'; // SMTP password
     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STMPS;
     $mail->Port = 465;

     // Recipients
     $mail->setFrom('no-reply@debysfoundation.org.ng', 'Airline Company');
     $mail->addAddress($email, $contact_person);

     // Content
     $mail->isHTML(false);
     $mail->Subject = "Welcome to Our Airline Company!";
     $mail->Body = "Dear $contact_person,\n\nThank you for registering with us. We are excited to have you on board!\n\nBest regards,\nThe Airline Team";

         $mail->send();
     } catch (Exception $e) {
         echo json_encode(["status" => "error", "message" => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}", "redirect" => false]);
     }

        echo json_encode(["status" => "success", "message" => "Registration Successful!", "redirect" => true]);
    } else {
        echo json_encode(["status" => "error", "message" => "Registration failed. Try again.", "redirect" => false]);
    }
    exit();
}
?>
