<?php
require '../config/db.php';
require '../admin/funtion/func.php';
require '../admin/EmailSender/EmailSender.php';
require '../admin/updateprocess/mailTemplate.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $demail = cleanInput($_POST['demail']);
    $dname = cleanInput($_POST['dname']);
    $dnumber = cleanInput($_POST['dnumber']);
    $dlocation = cleanInput($_POST['dlocation']);
    $dpassword = cleanInput($_POST['dpassword']);
    $com_password = cleanInput($_POST['com_password']);
    // $generatedCode = $_POST['generated_code'];
    // $userInputCode = cleanInput($_POST['captcha_input']);
    $keepMeLoggedIn = isset($_POST['keepMeLoggedIn']);

    $url = $_SERVER['HTTP_REFERER'];
    $location = "$url";
    $message = "";
    $type = "error";
    $noError = true;

    if (empty($demail)) {
        $message = "Enter your email address.";
        $noError = false;
    } elseif (!filter_var($demail, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
        $noError = false;
    } else {
        $demail = strtolower($demail);
    }

    if (empty($dname)) {
        $message = "Enter your name.";
        $noError = false;
    }

    if (empty($dlocation)) {
        $message = "Select location.";
        $noError = false;
    }

    if (empty($dnumber)) {
        $message = "Enter your phone number.";
        $noError = false;
    } elseif (!preg_match('/^\d{11}$/', $dnumber)) {
        $message = "Enter a valid 11-digit phone number.";
        $noError = false;
    }

    if (empty($dpassword)) {
        $message = "Enter a password.";
        $noError = false;
    } elseif (strlen($dpassword) < 8) {
        $message = "Password must be at least 8 characters.";
        $noError = false;
    } elseif ($dpassword !== $com_password) {
        $message = "Passwords do not match.";
        $noError = false;
    }


    if ($noError) {
        $query = "SELECT * FROM users WHERE demail = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $message = "Database error. Please try again.";
            $noError = false;
        } else {
            $stmt->bind_param("s", $demail);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $message = "Email already exists.";
                $noError = false;
            }
        }
    }

    if ($noError) {
        $hashed_password = password_hash($dpassword, PASSWORD_DEFAULT);
        $unique_id = hash('sha256', uniqid());
        $userid = date('YmdHis') . rand(100000, 999999);
        $verification_code = bin2hex(random_bytes(16)); // Generate a random verification code

        $query = "INSERT INTO users (unique_id, userid, demail, dname, dnumber, dlocation, dpassword, verification_code, is_verified) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param(
                "ssssssss",
                $unique_id,
                $userid,
                $demail,
                $dname,
                $dnumber,
                $dlocation,
                $hashed_password,
                $verification_code
            );

            if ($stmt->execute()) {
                $_SESSION['userid'] = $userid;
                $_SESSION['demail'] = $demail;
                $_SESSION['userlocation'] = $dlocation;

                if ($keepMeLoggedIn) {
                    setcookie('userid', $userid, time() + (86400 * 30), "/"); // 30 days
                    setcookie('isLoggedin', true, time() + (86400 * 30), "/"); // 30 days
                }
                
              
                    // online code
                $verification_link = "https://brightpowerstation.com/auth/verify?code=$verification_code";
                $subject = "Welcome to Bright Power Station - Verify Your Email";
                
                // Email Content
                $emailContent = "
                <html>
                <head>
                    <style>
                        .email-container {
                            border: 1px solid #ddd;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            padding: 20px;
                            font-family: Arial, sans-serif;
                            background-color: #f9f9f9;
                        }
                        .email-header {
                            font-size: 18px;
                            font-weight: bold;
                            margin-bottom: 20px;
                            background-color: rgb(19, 193, 65);
                            padding: 10px;
                            color: #f9f9f9;
                        }
                        .email-body {
                            font-size: 16px;
                            line-height: 1.5;
                            color: #333;
                        }
                        .email-footer {
                            margin-top: 20px;
                            font-size: 14px;
                            color: #555;
                            text-align: center;
                        }
                        .verify-link {
                            display: inline-block;
                            margin-top: 20px;
                            padding: 12px 24px;
                            background-color: #007bff;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 5px;
                            font-weight: bold;
                            transition: all 0.3s ease-in-out;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        }
                        .verify-link:hover {
                            background-color: #0056b3;
                            color: #fff;
                            transform: translateY(-3px);
                            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
                        }
                        .verify-link strong {
                            color: #fff;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='email-header'>Dear $dname,</div>
                        <div class='email-body'>
                            Welcome to Bright Power Station!<br><br>
                            Please verify your email address to complete your registration and proceed with your account.<br><br>
                            <a href='$verification_link' class='verify-link'><strong>Verify Your Email</strong></a><br><br>
                            If you did not request this registration, please ignore this email.<br><br>
                            Thank you for joining Bright Power Station!<br><br>
                            Best regards,<br>
                            <strong>Bright Power Station Team</strong>
                        </div>
                        <div class='email-footer'>
                            This is an automated message. Please do not reply to this email.
                        </div>
                    </div>
                </body>
                </html>";

                $template = '../admin/updateprocess/mailTemplate.php';

                try {
                    if (EmailSender::sendEmail($demail, $dname, $subject, $template, $emailContent)) {
                        redirectWithMessage("./authorization", "Registration successful. Please check your email to verify your account.", "success");
                    } else {
                        redirectWithMessage("$url", "Account created, but email sending failed.", "error");
                    }
                } catch (Exception $e) {
                    redirectWithMessage("$url", "Account created, but email sending failed. Error: " . $e->getMessage(), "error");
                }
            } else {
                $message = "Registration failed. Please try again.";
            }
        } else {
            $message = "Database error. Please try again.";
        }
    }

    redirectWithMessage($url, $message, $type);
    exit();
} else {
    $location = "../register";
    $message = "Invalid request.";
    $type = "error";
    redirectWithMessage($location, $message, $type);
    exit();
}
?>