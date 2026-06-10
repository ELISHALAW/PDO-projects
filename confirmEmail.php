<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
require __DIR__ . '/_base.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$email = "";

if (is_post()) {
    $email = $_POST['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (empty($errors)) {
        // Check if email exists in user table
        $stmt = $_db->prepare('SELECT user_id, name, email FROM user WHERE email = :email');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $id = sha1(uniqid() . rand());
            $expires = date("Y-m-d H:i:s", strtotime("+5 minutes"));
            $user_id = $user['user_id'];

            // Is to ensure that only one active token exists for each user
            $deleteStmt = $_db->prepare('DELETE FROM token WHERE user_id = :user_id');
            $deleteStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Insert new token
            $insertStmt = $_db->prepare('INSERT INTO token (id, expires, user_id) VALUES (:id, :expires, :user_id)');
            $insertStmt->bindParam(':id', $id, PDO::PARAM_STR);
            $insertStmt->bindParam(':expires', $expires, PDO::PARAM_STR);
            $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insertStmt->execute();

            $url = "http://localhost:8080/resetPassword.php?token=" . urlencode($id);


            $m = get_mail();
            $m->addAddress(e($user['email']), e($user['name']));
            $m->isHTML(true);
            $m->Subject = 'Password reset Request';
            $m->Body = "
            <p>Dear " . e($user['name'] ?? 'User') . ",</p>
            <h2 style='color:red;'>Reset Your Password</h2>
            <p>Click <a href='$url'>here</a> to reset your password. This link is valid for 5 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
            <p>Best Regards,</p>
            <p><strong>Admin</strong></p>";

            if (!$m->send()) {
                error_log("Mailer Error: " . $m->ErrorInfo);
                $errors[] = 'Failed to send email. Please try again later';
            } else {
                echo "<script>alert('Password reset email sent! Please check your gmail')</script>";
                // $_SESSION['success_message'] = 'Password reset email sent! check your gmail';

            }
        } else {
            $errors[] = 'No Account found with this email';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Email</title>
    <link rel="icon" type="image/png" href="images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="./css/confirmEmail.css">
</head>
<body class="bg-cover bg-center bg-no-repeat flex items-center justify-center min-h-screen" style="background-image: url('../images/Laptop.jpg');">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="mb-6 flex justify-center">
            <img src="../images/Laptop.jpg" alt="Laptop" class="w-24 h-24 object-cover rounded-full">
        </div>

        <form action="confirmEmail.php" method="POST" class="space-y-6">
            <div class="text-red-600 text-sm">
                <?= displayError($errors) ?>
            </div>

            <h2 class="text-2xl font-bold text-gray-800">Please enter your email</h2>
            
            <div class="w-full">
                <div class="w-full">
                    <?= inputField('email', 'email', 'Enter your email address', $email) ?>
                </div>
            </div>
            <?= html_submit('submit', 'submit', 'w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200', 'submit') ?>

            <p class="text-sm text-gray-600">
                Back to <a href="login.php" class="text-blue-500 hover:underline">Login</a>
            </p>
        </form>
    </div>
</body>