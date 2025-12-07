<?php
require __DIR__ . '/_base.php';


date_default_timezone_set('Asia/Kuala_Lumpur');

$token = $_GET['token'] ?? null;
if (!$token) {
    echo "<script>alert('Missing token!'); window.location.href = 'confirmEmail.php';</script>";
    exit;
}

try {
   
    $stmt = $_db->prepare("DELETE FROM token WHERE expires <= NOW()");
    $stmt->execute();

    
    $stmt = $_db->prepare("SELECT user_id FROM token WHERE id = :token AND expires > NOW()");
    $stmt->bindParam(":token", $token, PDO::PARAM_STR);
    $stmt->execute();
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tokenData) {
        error_log("Token not found or expired: " . $token);
        echo "<script>alert('Invalid or expired token!'); window.location.href = 'confirmEmail.php';</script>";
        exit;
    }

    $user_id = $tokenData['user_id']; 
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$errors = [];


if (is_post()) {
    $newPassword = trim($_POST['newPassword'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    if (!$newPassword || !$confirmPassword) {
        $errors[] = "Password fields cannot be empty.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($newPassword) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {
        $hashPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            
            $_db->beginTransaction();

            
            $stmt = $_db->prepare("UPDATE user SET password = :password WHERE user_id = :user_id");
            $stmt->bindParam(":password", $hashPassword, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $_db->prepare("DELETE FROM token WHERE BINARY id = :token");
            $stmt->bindParam(":token", $token, PDO::PARAM_STR);
            $stmt->execute();

           
            $_db->commit();

            
            echo "<script>alert('Password reset successful! You can now log in.'); window.location.href = 'login.php';</script>";
            exit;
        } catch (PDOException $e) {
            $_db->rollBack();
            die("Database error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="./css/resetPassword.css">
</head>
<body>
    <div class="form-container">
        <form action="resetPassword.php?token=<?= e($token) ?>" method="POST">
            <h2>Reset Password</h2>

            <?= displayError($errors)?>

            <?= inputField('password','newPassword','Enter your new password','','') ?>
            <?= inputField('password','confirmPassword','Confirm your new password', '','') ?>
            <?= html_submit('submit','submit','form-btn','Reset Password') ?>
        </form>
    </div>
</body>
</html>