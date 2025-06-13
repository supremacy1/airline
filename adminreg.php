<?php
// File: create_admin.php
include 'db/db.php'; // This file should define the $pdo variable (PDO connection)

$username = 'adminpass';         // Change to desired username
$plainPassword = 'pass123';      // Change to desired password

// Hash the password securely
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

try {
    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (:username, :password)");

    // Execute with bound values
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword
    ]);

    echo "Admin account created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
