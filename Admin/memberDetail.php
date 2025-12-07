<?php include './headandfoot/head.php'; ?>

<?php
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: member-listing.php");
    exit();
}

// Upload image logic with duplicate filename check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    if (!empty($_FILES['update_image']['name'])) {
        $imageName = basename($_FILES['update_image']['name']);
        $imageTmp = $_FILES['update_image']['tmp_name'];
        $uploadDir = '../uploaded_img/';
        $ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        // Validate extension
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Only JPG, JPEG, PNG formats are allowed.');</script>";
        } else {
            // Check if the same image name exists for another user
            $stmtCheck = $_db->prepare("SELECT COUNT(*) FROM user WHERE image = :image AND user_id != :id");
            $stmtCheck->bindParam(':image', $imageName);
            $stmtCheck->bindParam(':id', $id);
            $stmtCheck->execute();
            $exists = $stmtCheck->fetchColumn();

            if ($exists > 0) {
                echo "<script>alert('An image with the same name already exists. Please rename and try again.');</script>";
            } else {
                // Move the uploaded file
                if (move_uploaded_file($imageTmp, $uploadDir . $imageName)) {
                    // Update DB
                    $stmt = $_db->prepare("UPDATE user SET image = :image WHERE user_id = :id");
                    $stmt->bindParam(':image', $imageName, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();

                    echo "<script>alert('Image updated successfully!');</script>";
                } else {
                    echo "<script>alert('Failed to move uploaded file.');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('No image uploaded.');</script>";
    }
}

// Fetch user info
$stmt = $_db->prepare("SELECT * FROM user WHERE user_id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<style>
    body {
        background-color: #f7f8fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .profile {
        display: flex;
        justify-content: center;
        gap: 40px;
        padding: 50px;
    }

    .inputBox {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        padding: 30px;
        max-width: 450px;
        flex: 1;
    }

    .inputBox img {
        border-radius: 12px;
        object-fit: cover;
        width: 100%;
        max-width: 250px;
        height: auto;
        margin: 0 auto 20px;
        display: block;
    }

    .box {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin-bottom: 15px;
        font-size: 16px;
        background-color: #f9f9f9;
        pointer-events: none;
    }

    .inputBox label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }

    .inputBox textarea {
        resize: none;
        height: 100px;
        background-color: #f9f9f9;
        pointer-events: none;
    }

    .inputBox a,
    .inputBox button {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background-color: crimson;
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
    }

    .inputBox a:hover,
    .inputBox button:hover {
        background-color: darkred;
    }

    input[type="file"] {
        background-color: white;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 10px;
        width: 100%;
    }
</style>

<form action="" method="post" enctype="multipart/form-data">
    <div class="profile">
        <div class="inputBox">
            <img src="<?= !empty($results['image']) ? '../uploaded_img/' . e($results['image']) : '../images/default-avatar.png' ?>" alt="User Image">

            <?= inputField('text', 'update_username', '', $results['username'] ?? '', 'box') ?>
            <?= inputField('text', 'update_name', '', $results['name'] ?? '', 'box') ?>
            <?= inputField('email', 'update_email', '', $results['email'] ?? '', 'box') ?>
            <?= inputField('text', 'update_phone', '', $results['phone_number'] ?? '', 'box') ?>
            <?= inputField('text', 'update_address', '', $results['address'] ?? '', 'box') ?>

            <label for="update_image">Change Image:</label>
            <?= inputField('file','update_image','') ?>

            <button type="submit" name="upload_image">Upload Image</button>
            <a href="member-listing.php">Back</a>
        </div>
    </div>
</form>

<?php require './headandfoot/foot.php'; ?>
