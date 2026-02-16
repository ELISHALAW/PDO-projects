<?php
require __DIR__ . '/_base.php';

$errors = [];
$usernameEmail = $_COOKIE['remember_email'] ?? "";
$password = "";

if (is_post()) {
    $usernameEmail = trim($_POST['usernameEmail']);
    $password = $_POST['password'];

    if (empty($usernameEmail) || empty($password)) {
        $errors[] = "Both fields are required.";
    } elseif (!filter_var($usernameEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Fetch user including the security columns
        $stmt = $_db->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute([':email' => $usernameEmail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $currentTime = new DateTime();
            $blockUntil = $row['block_until'] ? new DateTime($row['block_until']) : null;

            // Check if account is currently locked
            if ($blockUntil && $blockUntil > $currentTime) {
                $diff = $currentTime->diff($blockUntil);
                $errors[] = "Account locked. Try again in " . $diff->format('%i minutes, %s seconds');
            } else {
                if (password_verify($password, $row['password'])) {
                    // --- SUCCESS ---
                    // Reset attempts and clear block
                    $update = $_db->prepare("UPDATE user SET login_attempts = 0, block_until = NULL WHERE user_id = :id");
                    $update->execute([':id' => $row['user_id']]);

                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['status'] = $row['status'];

                    // Handle Remember Me
                    if (isset($_POST['remember'])) {
                        setcookie('remember_email', $usernameEmail, time() + (7 * 24 * 60 * 60), "/");
                    } else {
                        setcookie('remember_email', '', time() - 3600, "/");
                    }

                    $location = ($row['status'] === 'admin') ? "./Admin/adminHomepage.php" : "index.php";
                    header("Location: $location");
                    exit();
                } else {
                    // --- FAILURE ---
                    $newAttempts = $row['login_attempts'] + 1;
                    $lockTime = null;

                    if ($newAttempts >= 3) {
                        // Set block for 15 minutes
                        $lockTime = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $errors[] = "Too many failed attempts. Account locked for 15 minutes.";
                    } else {
                        $remaining = 3 - $newAttempts;
                        $errors[] = "Incorrect password. $remaining attempts remaining.";
                    }

                    $update = $_db->prepare("UPDATE user SET login_attempts = :attempts, block_until = :until WHERE user_id = :id");
                    $update->execute([
                        ':attempts' => $newAttempts,
                        ':until' => $lockTime,
                        ':id' => $row['user_id']
                    ]);
                }
            }
        } else {
            $errors[] = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <div class="form-container">
        <form action="login.php" method="POST">
            <h2>Login</h2>
            <?php displayError($errors); ?>

            <div class="form-checkbox">
                <label style="display:inline-flex; align-items:center;">
                    <?= checkbox('remember', isset($_POST['remember']) || isset($_COOKIE['remember_email'])) ?>
                    <span class="text">Remember me</span>
                </label>
            </div>

            <?= inputField('email', 'usernameEmail', 'example@gmail.com', $usernameEmail) ?><br>
            <?= inputField('password', 'password', 'Enter your password') ?><br>

            <?= html_submit('submit', 'submit', 'form-btn', 'Login') ?>

            <p>Don't have an account? <a href="registration.php">Register now</a></p>
            <a href="confirmEmail.php">Forgot password?</a>
        </form>
    </div>
</body>

</html>