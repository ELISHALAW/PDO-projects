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
   <link rel="stylesheet" href="./css/profile.css">
</head>
<body>

<div class="update-profile">
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Hidden input to store the user_id -->
        <input type="hidden" name="user_id" value="<?= $user_id; ?>">

        <!-- Display current user image or default -->
        <?php echo '<img src="' . (!empty($fetch['image']) ? 'uploaded_img/' . e($fetch['image']) : 'images/default-avatar.png') . '">'; ?>

        <!-- Display error messages -->
        <?= displayError($errors) ?>

        <div class="flex">
            <div class="inputBox">
                <span>Username:</span>
                <?= inputField('text', 'update_name', '', $fetch['name'] ?? '', 'box') ?>
                <span>Your Email:</span>
                <?= inputField('email', 'update_email', '', $fetch['email'] ?? '', 'box') ?>
                <span>Update Your Pic:</span>
                <?= inputField('file', 'update_image', '', '', 'box') ?>
            </div>
            <div class="inputBox">
                <span>Old Password:</span>
                <?= html_password('password', 'update_pass', 'Enter previous password', '', 'box') ?>
                <span>New Password:</span>
                <?= html_password('password', 'new_pass', 'Enter new password', '', 'box') ?>
                <span>Confirm Password:</span>
                <?= html_password('password', 'confirm_pass', 'Confirm new password', '', 'box') ?>
            </div>
            <div class="inputBox">
                <span>Phone Number:</span>
                <?= inputField('text', 'phone_number', 'e.g. 123-456-7890', $fetch['phone_number'], 'box')?>
                <span>Address:</span>
                <?= inputField('text', 'address', '', $fetch['address'] ?? '', 'box') ?>
            </div>
        </div>

        <!-- Submit button -->
        <?= html_submit('submit', 'update_profile', 'btn', 'Update Profile') ?>
        <a href="index.php" class="delete-btn">Go Back</a>
    </form>
</div>

</body>
</html>
