<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

require 'vendor/autoload.php';

if (isset($_POST['send'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $receiver_email = $_POST['receiver_email'];
    $destination = htmlspecialchars($_POST['destination']);
    $dates = htmlspecialchars($_POST['dates']);
    $notes = nl2br(htmlspecialchars($_POST['notes']));

    // Your logo URL (hosted image or local path if inline)
    $logo_url = 'https://delightskcompanyltd.com/logo.png'; // Replace with actual logo URL

    // HTML content for PDF
    $html = "
    <div style='font-family: Arial, sans-serif;'>
        <img src='$logo_url' style='height: 60px; margin-bottom: 20px;'>
        <h2 style='color: #007bff;'>Trip Itinerary</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Destination:</strong> $destination</p>
        <p><strong>Travel Dates:</strong> $dates</p>
        <p><strong>Notes/Activities:</strong><br>$notes</p>
    </div>
    ";

    // Generate PDF
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfOutput = $dompdf->output();
    $pdfFile = 'itinerary_' . time() . '.pdf';
    file_put_contents($pdfFile, $pdfOutput);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = "delightskcompanyltd.com";
        $mail->SMTPAuth = true;
        $mail->Username = "info@delightskcompanyltd.com";
        $mail->Password = "andybest@1";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom("info@delightskcompanyltd.com", "DelightSK Travels");
        $mail->addAddress($receiver_email);

        // Attach PDF
        $mail->addAttachment($pdfFile);

        // Email content with logo
        $mail->isHTML(true);
        $mail->Subject = 'Your Trip Itinerary (PDF Attached)';
        $mail->Body = "
        <div style='font-family: Arial, sans-serif;'>
            <img src='$logo_url' style='height: 60px;'><br><br>
            <p>Hi,</p>
            <p>Please find the attached trip itinerary.</p>
            <p><strong>Traveler Name:</strong> $name<br>
               <strong>Destination:</strong> $destination<br>
               <strong>Dates:</strong> $dates</p>
            <p>Safe travels!<br><strong>DelightSK Company Ltd</strong></p>
        </div>
        ";

        $mail->send();
        unlink($pdfFile); // Delete temp file

        echo "<script>alert('Itinerary sent successfully!'); window.location='index.html';</script>";
    } catch (Exception $e) {
        echo "Error sending email: {$mail->ErrorInfo}";
    }
}
?>
