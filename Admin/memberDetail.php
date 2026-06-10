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

<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800">Member Profile</h2>
            <p class="text-sm text-gray-500">View detailed information for <?= e($results['name'] ?? 'Member') ?></p>
        </div>

        <form action="" method="post" enctype="multipart/form-data" class="p-8">
            <div class="flex flex-col md:flex-row gap-10">
                <div class="flex-shrink-0 flex flex-col items-center gap-6">
                    <div class="relative group">
                        <img src="<?= !empty($results['image']) ? '../uploaded_img/' . e($results['image']) : '../images/default-avatar.png' ?>" 
                             alt="User Image" 
                             class="w-48 h-48 rounded-2xl object-cover shadow-lg border-4 border-white">
                    </div>
                    
                    <div class="w-full">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Change Image</label>
                        <input type="file" name="update_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-indigo-600 transition-all cursor-pointer">
                    </div>
                </div>

                <div class="flex-grow space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <?php 
                        $fields = [
                            'Username' => $results['username'] ?? '',
                            'Full Name' => $results['name'] ?? '',
                            'Email' => $results['email'] ?? '',
                            'Phone' => $results['phone_number'] ?? '',
                            'Address' => $results['address'] ?? ''
                        ];
                        foreach($fields as $label => $value): ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1"><?= $label ?></label>
                                <div class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-gray-700 font-medium">
                                    <?= e($value) ?: 'N/A' ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                <a href="member-listing.php" 
                   class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
                   Back to List
                </a>
                <button type="submit" name="upload_image" 
                        class="px-6 py-2.5 bg-gray-900 hover:bg-indigo-600 text-white text-sm font-bold rounded-lg transition-all shadow-md">
                    Upload New Image
                </button>
            </div>
        </form>
    </div>
</div>

<?php require './headandfoot/foot.php'; ?>