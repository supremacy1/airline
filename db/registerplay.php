<?php
session_start();
require 'db.php'; // Ensure you have a DB connection file.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function cleanInput($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $companyName = cleanInput($_POST['company_name']);
    $iataCode = cleanInput($_POST['iata_code']);
    $icaoCode = cleanInput($_POST['icao_code']);
    $contactPerson = cleanInput($_POST['contact_person']);
    $email = cleanInput($_POST['email']);
    $phone = cleanInput($_POST['phone']);
    $country = cleanInput($_POST['country']);
    $password = cleanInput($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $message = "";
    $success = false;

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif (empty($companyName) || empty($password) || empty($phone)) {
        $message = "Please fill in all required fields.";
    } else {
        // Check if email already exists
        $query = "SELECT * FROM airlines WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already exists.";
        } else {
            // Insert data into database
            $insertQuery = "INSERT INTO airlines (company_name, iata_code, icao_code, contact_person, email, phone, country, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssssssss", $companyName, $iataCode, $icaoCode, $contactPerson, $email, $phone, $country, $hashedPassword);

            if ($stmt->execute()) {
                $subject = 'Registration Successful';
                $message = "Dear $contactPerson, your registration with $companyName has been successfully completed.";
                $headers = "From: no-reply@andreohejiedogbu@gmai.com";

                mail($email, $subject, $message, $headers);

                echo "<script>alert('Registration successful!'); window.location.href='success.html';</script>";
                exit;
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
    echo "<script>alert('$message'); window.history.back();</script>";
}
?>
