<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

require 'vendor/autoload.php';

if (isset($_POST['send'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $destination = htmlspecialchars($_POST['destination']);
    $dates = htmlspecialchars($_POST['dates']);
    $notes = nl2br(htmlspecialchars($_POST['notes']));

    // Create HTML itinerary content
        $company_email = "andrewohejiedogbu@gmail.com";

    $html = "
    <h2 style='color: #007bff;'>Trip Itinerary</h2>
    <p><strong>Name:</strong> $name</p>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Destination:</strong> $destination</p>
    <p><strong>Travel Dates:</strong> $dates</p>
    <p><strong>Notes/Activities:</strong><br>$notes</p>
    ";

    // Generate PDF using DomPDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output PDF to string
    $pdfOutput = $dompdf->output();
    $pdfFile = 'itinerary_' . time() . '.pdf';
    file_put_contents($pdfFile, $pdfOutput);

    $mail = new PHPMailer(true);

    try {
        // SMTP settings
       $mail->isSMTP();
        $mail->Host = "delightskcompanyltd.com";  
        $mail->SMTPAuth = true;
        $mail->Username = "info@delightskcompanyltd.com";  
        $mail->Password = "andybest@1";  
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Email headers
    $mail->setFrom("info@delightskcompanyltd.com", "Flight Booking System");
        $mail->addAddress($company_email, "Flight Company");

        // Attach PDF
        $mail->addAttachment($pdfFile);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Trip Itinerary (PDF Attached)';
        $mail->Body = "Hi $name,<br><br>Please find your itinerary attached as a PDF.<br><br>Safe travels!";

        $mail->send();

        // Remove temporary PDF file
        unlink($pdfFile);

        echo "<script>alert('Itinerary sent with PDF successfully!'); window.location='index.php';</script>";
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
