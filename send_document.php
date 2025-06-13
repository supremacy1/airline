<?php
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'delightskcompanyltd.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@delightskcompanyltd.com';
        $mail->Password = 'andybest@1';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('info@delightskcompanyltd.com', 'Admin');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($message); // convert line breaks to <br>

        // Attachment
        if (!empty($_FILES['pdf_file']['tmp_name'])) {
            $mail->addAttachment($_FILES['pdf_file']['tmp_name'], $_FILES['pdf_file']['name']);
        }

        $mail->send();
        echo "<script>alert('Document sent successfully!');window.location='admin_dashboard.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error sending: {$mail->ErrorInfo}');window.location='admin_dashboard.php';</script>";
    }
}
?>
