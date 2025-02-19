<?php
require '../../config/db.php';
require '../../config/function/function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize inputs
    $demail = cleanInput($_POST['demail']);
    $dname = cleanInput($_POST['dname']);
    $dnumber = cleanInput($_POST['dnumber']);
    $dpassword = cleanInput($_POST['dpassword']);
    $com_password = cleanInput($_POST['com_password']);
    $generatedCode = cleanInput($_POST['generated_code']);
    $userInputCode = cleanInput($_POST['captcha_input']);
    $keepMeLoggedIn = isset($_POST['keepMeLoggedIn']);
    $OneTimeCode = mt_rand(1000, 9999);

    $noError = true;
    $location = "../../jobseeker/sign-up";
    $message = "";
    $type = "error";

    // Validation
    if (empty($demail) || !filter_var($demail, FILTER_VALIDATE_EMAIL)) {
        $message = empty($demail) ? "Enter your email address." : "Enter a valid email address.";
        $noError = false;
    } else {
        $demail = strtolower($demail);
    }

    if (empty($dname)) {
        $message = "Enter your name.";
        $noError = false;
    }

    if (empty($dnumber) || !preg_match('/^\d{11}$/', $dnumber)) {
        $message = empty($dnumber) ? "Enter your phone number." : "Enter a valid 11-digit phone number.";
        $noError = false;
    }

    if (empty($dpassword) || strlen($dpassword) < 8 || !preg_match('/[A-Za-z]/', $dpassword) || !preg_match('/\d/', $dpassword) || $dpassword !== $com_password) {
        $message = empty($dpassword) ? "Enter a password." : (strlen($dpassword) < 8 ? "Password must be at least 8 characters." : (!preg_match('/[A-Za-z]/', $dpassword) ? "Password must include at least one letter." : (!preg_match('/\d/', $dpassword) ? "Password must include at least one number." :
                        "Passwords do not match.")));
        $noError = false;
    }

    if (empty($userInputCode) || strcasecmp($generatedCode, $userInputCode) !== 0) {
        $message = "Invalid CAPTCHA. Please try again.";
        $noError = false;
    }

    if ($noError) {
        // Check if email or phone number already exists
        $query = "SELECT * FROM jobseek WHERE demail = ? OR dnumber = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ss", $demail, $dnumber);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['demail'] === $demail) {
                        $message = "Email already exists.";
                        $noError = false;
                        break;
                    }
                    if ($row['dnumber'] === $dnumber) {
                        $message = "Phone Number already exists.";
                        $noError = false;
                        break;
                    }
                }
            }
        } else {
            $message = "Database error. Please try again.";
            $noError = false;
        }
    }

    if ($noError) {
        $hashed_password = password_hash($dpassword, PASSWORD_DEFAULT);
        $userid = date('YmdHis') . rand(100000, 999999);

        // Insert user data into the database
        $query = "INSERT INTO jobseek (userid, demail, dname, dnumber, dpassword, OneTimeCode) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssssss", $userid, $demail, $dname, $dnumber, $hashed_password, $OneTimeCode);

            if ($stmt->execute()) {
                // Fetch the inserted user's data
                $fetchQuery = "SELECT * FROM jobseek WHERE userid = ?";
                $fetchStmt = $conn->prepare($fetchQuery);

                if ($fetchStmt) {
                    $fetchStmt->bind_param("s", $userid); 
                    $fetchStmt->execute();
                    $fetchResult = $fetchStmt->get_result();

                    if ($fetchResult && $fetchResult->num_rows > 0) {
                        $userData = $fetchResult->fetch_assoc();
                        $demail = $userData['demail'];
                        // $OneTimeCode = $userData['OneTimeCode'];
                        
                        // echo "User Email: " . $demail . "<br>";
                        // echo "One-Time Code: " . $OneTimeCode . "<br>";
                        // exit();
                          $_SESSION['userid'] = $userid;
                        $_SESSION['demail'] = $demail;

                        // Send email to user
                        $subject = 'Capinta | Signup Confirmation & OTP';
                        $htmlContent = "
                            <html>
                            <head>
                                <title>Account Verification</title>
                            </head>
                            <body>
                                <h3>Welcome to Capinta, $dname!</h3>
                                <p>Your account has been successfully created. Use the OTP below to verify your email:</p>
                                //  <h2 style='color: blue;'>$OneTimeCode</h2>
                                // <p>Or click the link below to verify your account:</p>
                                // <p><a href='https://www.capinta.com.ng/app/verify?email=$demail' target='_blank' style='background-color: blue; color: white; padding: 10px; text-decoration: none;'>Verify Account</a></p>
                                <p>If you didn't request this, please ignore this email.</p>
                                <br>
                                <p>Best regards,<br>Capinta Team</p>
                            </body>
                            </html>";

                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
                        $headers .= "From: Capinta ERP <no-reply@capinta.com.ng>" . "\r\n";
                        $headers .= "Reply-To: support@capinta.com.ng" . "\r\n";

                        if (mail($demail, $subject, $htmlContent, $headers)) {
                            redirectWithMessage("../../jobseeker/otp", "Registration successful. Please check your email.", "success");
                        } else {
                            $error = error_get_last();
                            redirectWithMessage("../../jobseeker/otp", "Registration successful, but email sending failed. Error: " . $error['message'], "error");
                        }

                        exit();
                    } else {
                        $message = "Failed to fetch user data.";
                    }
                } else {
                    $message = "Database error. Please try again.";
                }
            } else {
                $message = "Registration failed. Please try again.";
            }
        } else {
            $message = "Database error. Please try again.";
        }
    }

    redirectWithMessage($location, $message, $type);
}