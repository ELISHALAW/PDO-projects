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

            if ($blockUntil && $blockUntil > $currentTime) {
                $diff = $currentTime->diff($blockUntil);
                $errors[] = "Account locked. Try again in " . $diff->format('%i minutes, %s seconds');
            } else {
                if (password_verify($password, $row['password'])) {
                    // Success
                    $update = $_db->prepare("UPDATE user SET login_attempts = 0, block_until = NULL WHERE user_id = :id");
                    $update->execute([':id' => $row['user_id']]);

                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['status'] = $row['status'];

                    if (isset($_POST['remember'])) {
                        setcookie('remember_email', $usernameEmail, time() + (7 * 24 * 60 * 60), "/");
                    } else {
                        setcookie('remember_email', '', time() - 3600, "/");
                    }

                    $location = ($row['status'] === 'admin') ? "./Admin/adminHomepage.php" : "index.php";
                    header("Location: $location");
                    exit();
                } else {
                    // Failure
                    $newAttempts = $row['login_attempts'] + 1;
                    $lockTime = null;

                    if ($newAttempts >= 3) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link rel="icon" type="image/png" href="images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[url('../images/Laptop.jpg')] bg-cover bg-center bg-no-repeat min-h-screen flex items-center justify-center p-4 antialiased relative">
    
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs pointer-events-none"></div>

    <div class="w-full max-w-md z-10">
        <form action="login.php" method="POST" class="bg-white/90 backdrop-blur-md p-8 rounded-2xl shadow-2xl border border-white/20 space-y-6">
            
            <div class="text-center space-y-1">
                <h2 class="text-3xl font-bold tracking-tight text-slate-800">Welcome Back</h2>
                <p class="text-sm text-slate-500">Please enter your details to sign in</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded text-sm text-red-700">
                    <?php displayError($errors); ?>
                </div>
            <?php endif; ?>

            <!-- Google Login Button -->
            <a href="google-login.php" 
               class="flex items-center justify-center w-full gap-3 bg-white hover:bg-gray-50 border border-gray-300 text-slate-700 font-semibold py-2.5 px-4 rounded-xl transition duration-200 shadow">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" width="20">
                Sign in with Google
            </a>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white/90 px-4 text-slate-500">or continue with email</span>
                </div>
            </div>

            <div class="space-y-4 [&_input]:w-full [&_input]:px-4 [&_input]:py-2.5 [&_input]:border [&_input]:border-slate-200 [&_input]:rounded-xl [&_input]:text-slate-800 [&_input]:transition [&_input]:duration-200 [&_input]:focus:outline-none [&_input]:focus:ring-2 [&_input]:focus:ring-blue-500/20 [&_input]:focus:border-blue-500 [&_input]:bg-white/80">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <?= inputField('email', 'usernameEmail', 'example@gmail.com', $usernameEmail) ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <?= inputField('password', 'password', 'Enter your password') ?>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer select-none [&_input]:rounded [&_input]:border-slate-300 [&_input]:text-blue-600 [&_input]:focus:ring-blue-500">
                    <?= checkbox('remember', isset($_POST['remember']) || isset($_COOKIE['remember_email'])) ?>
                    <span>Remember me</span>
                </label>
                <a href="confirmEmail.php" class="font-medium text-blue-600 hover:text-blue-500 hover:underline transition duration-150">Forgot password?</a>
            </div>

            <div>
                <?= html_submit('submit', 'submit', 'w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-500/20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer text-center block', 'Login') ?>
            </div>

            <p class="text-center text-sm text-slate-600 pt-2">
                Don't have an account? 
                <a href="registration.php" class="font-semibold text-blue-600 hover:text-blue-500 hover:underline transition duration-150">Register now</a>
            </p>
        </form>
    </div>

</body>
</html>