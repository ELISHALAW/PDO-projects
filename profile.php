<?php
require '_base.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

// Get the user_id from the URL
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch user data based on user_id
$stmt = $_db->prepare("SELECT * FROM user WHERE user_id = :user_id");
$stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
$stmt->execute();
$fetch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fetch) {
    echo "<script>alert('User not found!'); window.location.href='index.php';</script>";
    exit();
}

if (is_post()) {
    $update_name = trim($_POST['update_name'] ?? '');
    $update_email = trim($_POST['update_email'] ?? '');
    $phone_number = $_POST['phone_number'];
    $address = trim($_POST['address'] ?? '');

    if (preg_match('/[a-zA-Z]/', $phone_number)) {
        $errors[] = 'Phone number must not contain any letters.';
    }

    // Validate address
    if (strlen($address) < 5) {
        $errors[] = 'Address must be at least 5 characters long.';
    }

    // Check if the new email already exists for a different user
    $checkEmailStmt = $_db->prepare("SELECT COUNT(*) FROM user WHERE email = :email AND user_id != :user_id");
    $checkEmailStmt->bindParam(':email', $update_email, PDO::PARAM_STR);
    $checkEmailStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $checkEmailStmt->execute();
    $emailExists = $checkEmailStmt->fetchColumn();

    if ($emailExists) {
        $errors[] = "Email already exists. Please use a different one.";
    }

    if (empty($errors)) {
        // Update user details
        $stmt = $_db->prepare("UPDATE user SET name = :name, email = :email, phone_number = :phone_number, address = :address WHERE user_id = :user_id");
        $stmt->bindParam(":name", $update_name, PDO::PARAM_STR);
        $stmt->bindParam(":email", $update_email, PDO::PARAM_STR);
        $stmt->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
        $stmt->bindParam(":address", $address, PDO::PARAM_STR);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $errors[] = "Profile updated successfully!";
    }

    // Handle password update
    $update_pass = $_POST['update_pass'] ?? '';
    $new_pass = $_POST['new_pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';

    if (!empty($update_pass) && !empty($new_pass) && !empty($confirm_pass)) {
        $stmt = $_db->prepare("SELECT password FROM user WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($update_pass, $user_data['password'])) {
            $errors[] = 'Current password is incorrect!';
        } elseif (strlen($new_pass) < 8 || !preg_match('/[A-Z]/', $new_pass) || !preg_match('/[a-z]/', $new_pass) || !preg_match('/[0-9]/', $new_pass)) {
            $errors[] = 'New password must be at least 8 characters, include upper & lower case letters and a number.';
        } elseif ($new_pass !== $confirm_pass) {
            $errors[] = 'New passwords do not match!';
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $_db->prepare("UPDATE user SET password = :password WHERE user_id = :user_id");
            $stmt->bindParam(":password", $new_hash, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            session_regenerate_id(true);
            $_SESSION['password_hash'] = $new_hash;
            $errors[] = 'Password updated successfully!';
        }
    }

    // Handle image update
    if (!empty($_FILES['update_image']['name'])) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/' . basename($update_image);

        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($update_image, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $errors[] = 'Invalid image format! Only JPG, JPEG, and PNG allowed.';
        } elseif ($update_image_size > 2000000) {
            $errors[] = 'Image is too large!';
        } else {
            $stmt = $_db->prepare("UPDATE user SET image = :image WHERE user_id = :user_id");
            $stmt->bindParam(":image", $update_image, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
            $errors[] = 'Image updated successfully!';
        }
    }

    // Refresh data after update
    $stmt = $_db->prepare("SELECT * FROM user WHERE user_id = :user_id");
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex items-center justify-center p-4 md:p-8">

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-indigo-600 h-32 w-full relative"></div>

    <form action="" method="POST" enctype="multipart/form-data" class="px-6 pb-8 sm:px-10 relative">
        <input type="hidden" name="user_id" value="<?= $user_id; ?>">

        <div class="flex flex-col sm:flex-row items-center gap-4 -mt-16 mb-8 relative z-10">
            <div class="relative group">
                <img class="w-32 h-32 rounded-full object-cover border-4 border-white bg-white shadow-md" 
                     src="<?= (!empty($fetch['image']) ? 'uploaded_img/' . e($fetch['image']) : 'images/default-avatar.png') ?>" 
                     alt="Profile avatar">
            </div>
            <div class="text-center sm:text-left mt-12 sm:mt-16">
                <h1 class="text-2xl font-bold text-gray-900"><?= e($fetch['name'] ?? 'Update Profile') ?></h1>
                <p class="text-sm text-gray-500"><?= e($fetch['email'] ?? '') ?></p>
            </div>
        </div>

        <style>
            /* Quick injection override targeting helper-generated layout if necessary */
            .input-group span { @apply block text-sm font-medium text-gray-700 mb-1; }
            .box { 
                width: 100% !important;
                padding: 0.5rem 0.75rem !important;
                border-radius: 0.375rem !important;
                border: 1px solid #e5e7eb !important;
                background-color: #fff !important;
                outline: none !important;
                transition: border-color 0.15s ease-in-out;
            }
            .box:focus { border-color: #4f46e5 !important; ring: 2px #c7d2fe !important; }
            .btn {
                display: inline-flex !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 0.625rem 1.25rem !important;
                font-weight: 500 !important;
                font-size: 0.875rem !important;
                color: #fff !important;
                background-color: #4f46e5 !important;
                border-radius: 0.375rem !important;
                cursor: pointer !important;
                transition: background-color 0.15s ease-in-out !important;
            }
            .btn:hover { background-color: #4338ca !important; }
        </style>

        <div class="mb-6">
            <?= displayError($errors) ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="space-y-4 input-group">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Basic Info</h3>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Username:</span>
                    <?= inputField('text', 'update_name', '', $fetch['name'] ?? '', 'box') ?>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Your Email:</span>
                    <?= inputField('email', 'update_email', '', $fetch['email'] ?? '', 'box') ?>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Update Your Pic:</span>
                    <?= inputField('file', 'update_image', '', '', 'box block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100') ?>
                </div>
            </div>

            <div class="space-y-4 input-group">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Security</h3>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Old Password:</span>
                    <?= html_password('password', 'update_pass', 'Enter previous password', '', 'box') ?>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">New Password:</span>
                    <?= html_password('password', 'new_pass', 'Enter new password', '', 'box') ?>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Confirm Password:</span>
                    <?= html_password('password', 'confirm_pass', 'Confirm new password', '', 'box') ?>
                </div>
            </div>

            <div class="space-y-4 input-group">
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Contact Details</h3>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Phone Number:</span>
                    <?= inputField('text', 'phone_number', 'e.g. 123-456-7890', $fetch['phone_number'], 'box')?>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-1">Address:</span>
                    <?= inputField('text', 'address', '', $fetch['address'] ?? '', 'box') ?>
                </div>
            </div>
        </div>

        <hr class="border-gray-100 my-6">

        <div class="flex flex-col sm:flex-row-reverse items-center justify-start gap-3">
            <?= html_submit('submit', 'update_profile', 'btn w-full sm:w-auto', 'Update Profile') ?>
            <a href="index.php" class="w-full sm:w-auto text-center px-5 py-2.5 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition duration-150">
                Go Back
            </a>
        </div>
    </form>
</div>

</body>
</html>