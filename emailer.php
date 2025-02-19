<?php
require '../config/db.php';
require '../config/function/function.php';
require '../EmailSender/EmailSender.php';
include './mailTemplate.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_shippingFORM'])) {
    // Sanitize inputs
    $user_name = cleanInput($_POST['user_name']);
    $user_email = cleanInput($_POST['user_email']);
    $user_number = cleanInput($_POST['user_number']);
    $package_name = cleanInput($_POST['package_name']);
    $current_location = cleanInput($_POST['current_location']);
    $receiver_address = cleanInput($_POST['receiver_address']);
    $status = 'pending';

    $url = $_SERVER['HTTP_REFERER'];
    $noError = true;
    $location = "$url";
    $message = "";
    $type = "error";

    // Validation
    if (empty($user_name)) {
        $message = "Enter user name";
        $noError = false;
    }


    if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $message = empty($user_email) ? "Enter your email address." : "Enter a valid email address.";
        $noError = false;
    } else {
        $user_email = strtolower($user_email);
    }

    if (empty($user_number) || !preg_match('/^\d{11}$/', $user_number)) {
        $message = empty($user_number) ? "Enter user number." : "Enter a valid 11-digit phone number.";
        $noError = false;
    }

    if (empty($package_name)) {
        $message = "Enter package Name";
        $noError = false;
    }

    if (empty($current_location)) {
        $message = "Enter current location";
        $noError = false;
    }

    if (empty($receiver_address)) {
        $message = "Enter receiver address";
        $noError = false;
    }

    if ($noError) {
        $unique_Id = date('YmdHis') . rand(100000, 999998);
        $trackNo = date('YmdHis') . rand(100000, 999999);

        $query = "INSERT INTO packages (trackNo, unique_Id, user_name, user_email, user_number, package_name, current_location, receiver_address, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sssssssss", $trackNo, $unique_Id, $user_name, $user_email, $user_number, $package_name, $current_location, $receiver_address, $status);

            if ($stmt->execute()) {
                $fetchQuery = "SELECT * FROM packages WHERE unique_Id = ?";
                $fetchStmt = $conn->prepare($fetchQuery);

                if ($fetchStmt) {
                    $fetchStmt->bind_param("s", $unique_Id);
                    $fetchStmt->execute();
                    $fetchResult = $fetchStmt->get_result();

                    if ($fetchResult && $fetchResult->num_rows > 0) {
                        $userData = $fetchResult->fetch_assoc();
                        $user_name = $userData['user_name'];
                        $trackNo = $userData['trackNo'];
                        $status = $userData['status'];

                        $_SESSION['unique_Id'] = $unique_Id;
                        $_SESSION['user_name'] = $user_name;

                        // Send email to user
                        $subject = 'Package Tracking Information';
                        $emailContent = "
                                <div class='email-container'>
                                    <h3>Welcome to domain, $user_name!</h3>
                                    <p>Your package tracking number is:</p>
                                    <h2 style='color: blue; font-size: 24px; margin-bottom: 10px;'>$trackNo</h2>
                                    <p>Status: $status</p>
                                    <p><a href='https://primeportgloballogistics.org/tracking?email=$user_email' target='_blank'>Track your package</a></p>
                                    <br>
                                    <p>Best regards,<br>Primeport Global Logistics</p>
                                </div>
                                ";
                        // echo _DIR_;
                        // die();
                        $template = './mailTemplate.php';
                        try {
                            if (EmailSender::sendEmail($user_email, $user_name, $subject, $template, $emailContent)) {
                                redirectWithMessage("$url", "Shipping successful. Email sent to $user_email", "success");
                            } else {
                                redirectWithMessage("$url", "Shipping successful, but email sending failed.", "error");
                            }
                        } catch (Exception $e) {
                            redirectWithMessage("$url", "Shipping successful, but email sending failed. Error: " . $e->getMessage(), "error");
                        }


                        exit();
                    } else {
                        $message = "Failed to fetch user data.";
                    }
                } else {
                    $message = "Database error. Please try again.";
                }
            } else {
                $message = "Shipping failed. Please try again.";
            }
        } else {
            $message = "Database error. Please try again.";
        }
    }

    redirectWithMessage($url, $message, $type);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_update_shipping'])) {
    $unique_Id = cleanInput($_POST['delivery_Id']);
    $current_location = cleanInput($_POST['current_location']);
    $receiver_address = cleanInput($_POST['receiver_address']);

    $url = $_SERVER['HTTP_REFERER'];
    $noError = true;
    $location = "$url";
    $message = "";
    $type = "error";

    if (empty($unique_Id) || empty($current_location) || empty($receiver_address)) {
        $message = "All fields are required.";
        $type = "error";
        // redirectWithMessage("../admin/edit-shipping-location?unique_Id=$unique_Id", $message, $type);
        exit();
    }

    $query = "UPDATE packages SET current_location = ?, receiver_address = ? WHERE unique_Id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $current_location, $receiver_address, $unique_Id);

        if ($stmt->execute()) {
            // Fetch the updated user's data
            $fetchQuery = "SELECT * FROM packages WHERE unique_Id = ?";
            $fetchStmt = $conn->prepare($fetchQuery);

            if ($fetchStmt) {
                $fetchStmt->bind_param("s", $unique_Id);
                $fetchStmt->execute();
                $fetchResult = $fetchStmt->get_result();

                if ($fetchResult && $fetchResult->num_rows > 0) {
                    $userData = $fetchResult->fetch_assoc();
                    $user_name = $userData['user_name'];
                    $user_email = $userData['user_email'];
                    $trackNo = $userData['trackNo'];
                    $status = $userData['status'];

                    // Send email to user
                    $subject = 'primeportglobalprimeportgloballogistics | Shipping Location Updated';
                    $emailContent = "
                        <title>Shipping Location Updated</title>
                        <div class='email-container'>
                            <h3>Hello $user_name,</h3>
                            <p>Your shipping location has been updated.</p>
                            <p>Current Location: $current_location</p>
                            <p>Receiver Address: $receiver_address</p>
                            <p>Tracking Number: $trackNo</p>
                            <p>Status: <strong class='status-$status'>$status</strong></p>
                            <p><a href='https://primeportglobalprimeportgloballogistics.com/tracking?email=$user_email' target='_blank'>Track your package</a></p>
                            <br>
                            <p>Best regards,<br>primeportglobalprimeportgloballogistics Team</p>
                        </div>
                        <style>
                            .email-container {
                                padding: 20px;
                                background-color: #f9f9f9;
                                border-radius: 10px;
                                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                                max-width: 600px;
                                margin: auto;
                            }
                            .email-container h3 {
                                color: #333;
                            }
                            .email-container p {
                                color: #555;
                            }
                            .email-container a {
                                background-color: blue;
                                color: white;
                                padding: 10px;
                                text-decoration: none;
                                border-radius: 5px;
                            }
                            .status-pending {
                                color: orange;
                            }
                            .status-processing {
                                color: blue;
                            }
                            .status-completed {
                                color: green;
                            }
                        </style>
                    ";

                    $template = './mailTemplate.php';
                    try {
                        if (EmailSender::sendEmail($user_email, $user_name, $subject, $template, $emailContent)) {
                            redirectWithMessage("$url", "Shipping location updated successfully. email sent to $user_email", "success");
                        } else {
                            redirectWithMessage("$url", "Shipping successful, but email sending failed.", "error");
                        }
                    } catch (Exception $e) {
                        redirectWithMessage("$url", "Shipping location updated, but email sending failed. " . $e->getMessage(), "error");
                    }
                } else {
                    $message = "Failed to fetch user data.";
                    $type = "error";
                }
            } else {
                $message = "Database error. Please try again.";
                $type = "error";
            }
        } else {
            $message = "Failed to update shipping location.";
            $type = "error";
        }

        $stmt->close();
    } else {
        $message = "Database error. Please try again.";
        $type = "error";
    }

    $conn->close();
    redirectWithMessage("$url", $message, $type);
}






if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_emailCONTACT']))
    $errors = [];

// Sanitize user inputs
$dname = $conn->real_escape_string(trim($_POST['dname']));
$demail = $conn->real_escape_string(trim($_POST['demail']));
$dsubject = $conn->real_escape_string(trim($_POST['dsubject']));
$dmessage = $conn->real_escape_string(trim($_POST['dmessage']));
// $captcha_input = trim($_POST['captcha_input']);
// $generated_code = trim($_POST['generated_code']);

$url = $_SERVER['HTTP_REFERER'];
$noError = true;
$location = "$url";
$message = "";
$type = "error";

// Validate inputs
if (empty($dname)) {
    $errors[] = "Name is required.";
}
if (empty($demail) || !filter_var($demail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email is required.";
}
if (empty($dsubject)) {
    $errors[] = "Subject is required.";
}
if (empty($dmessage)) {
    $errors[] = "Message is required.";
}

// // Validate captcha
// if ($captcha_input !== $generated_code) {
//     $errors[] = "Captcha verification failed.";
// }

// If there are no errors, proceed to insert into the database and send email
if (empty($errors)) {
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO contacts (dname, demail, dsubject, dmessage) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $dname, $demail, $dsubject, $dmessage);

    if ($stmt->execute()) {
        $toEmail = "sundaygift34@gmail.com";
        $email_message = "An email has been received with the following details:\n\n";
        $mailHeaders = "Name: $dname\r\n" .
            "Sender: $demail\r\n" .
            "Subject: $dsubject\r\n" .
            "Message: $dmessage\r\n";

        if (mail($toEmail, $email_message, $mailHeaders)) {
            $message = "Submitted successfully. Our agents will attend to you soon.";
            $message = "success";
        } else {
            $message = "Form submitted, but email sending failed.";
            $message = "error";
        }
    } else {
        $message = "Failed to save your data. Please try again later.";
        $message = "error";
    }

    $stmt->close();
} else {
    // Collect and display validation errors
    $message = implode("<br>", $errors);
    $message = "error";
} {
    $conn->close();
    redirectWithMessage($url, $message, $type);
    exit;
}