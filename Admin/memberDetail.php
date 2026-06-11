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
<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl shadow-xl border border-slate-700/60 overflow-hidden">
        <div class="bg-slate-800/60 px-8 py-6 border-b border-slate-700/60">
            <h2 class="text-xl font-bold text-white tracking-wide">Member Profile</h2>
            <p class="text-sm text-slate-400 mt-1">Review full identity records for <span class="text-blue-400 font-medium"><?= e($results['name'] ?? 'Member') ?></span></p>
        </div>

        <form action="" method="post" enctype="multipart/form-data" class="p-8">
            <div class="flex flex-col md:flex-row gap-10">
                
                <div class="flex-shrink-0 flex flex-col items-center gap-5 w-full md:w-52">
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-2xl opacity-40 blur group-hover:opacity-70 transition duration-300"></div>
                        <img src="<?= !empty($results['image']) ? '../uploaded_img/' . e($results['image']) : '../images/default-avatar.png' ?>" 
                             alt="User Image" 
                             class="relative w-48 h-48 rounded-2xl object-cover shadow-2xl border border-slate-600/50 bg-slate-900">
                    </div>
                    
                    <div class="w-full mt-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 text-center md:text-left">Change Profile Image</label>
                        <input type="file" name="update_image" class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border file:border-slate-600 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 hover:file:text-white transition-all cursor-pointer">
                    </div>
                </div>

                <div class="flex-grow space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <?php 
                        $fields = [
                            'Username' => ['val' => $results['username'] ?? '', 'fullWidth' => false],
                            'Full Name' => ['val' => $results['name'] ?? '', 'fullWidth' => false],
                            'Email Address' => ['val' => $results['email'] ?? '', 'fullWidth' => true],
                            'Phone Number' => ['val' => $results['phone_number'] ?? '', 'fullWidth' => false],
                            'Postal Address' => ['val' => $results['address'] ?? '', 'fullWidth' => true]
                        ];
                        foreach($fields as $label => $data): ?>
                            <div class="<?= $data['fullWidth'] ? 'sm:col-span-2' : '' ?>">
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5"><?= $label ?></label>
                                <div class="w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 rounded-xl p-3.5 text-sm font-medium shadow-inner">
                                    <?= e($data['val']) ?: '<span class="text-slate-600 italic font-normal">Not Provided</span>' ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-4 pt-6 border-t border-slate-700/60">
                <a href="member-listing.php" 
                   class="px-5 py-2.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
                    Back to List
                </a>
                <button type="submit" name="upload_image" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-600/10">
                    Save New Image
                </button>
            </div>
        </form>
    </div>
</div>

<?php require './headandfoot/foot.php'; ?>