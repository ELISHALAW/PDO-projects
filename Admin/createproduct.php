<?php require __DIR__ . '/headandFoot/head.php'; ?>

<?php
$errors = [];
$product = '';
$quantity = '';
$perPrice = '';
$detail = '';
$imageUpload = '';
$category = '';
$success = '';

if (is_post()) {
    $product = trim($_POST['product'] ?? '');
    $perPrice = trim($_POST['perPrice'] ?? '');
    $quantity = trim($_POST['qty'] ?? '');
    $detail = trim($_POST['detail'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $imageUpload = $_FILES['image-upload']['name'] ?? '';

    // Validation
    if (empty($product)) {
        $errors[] = "Product name is required.";
    }

    if (!is_numeric($quantity) || (int)$quantity <= 0) {
        $errors[] = "Quantity must be a positive number.";
    }

    if (!is_numeric($perPrice) || (float)$perPrice <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    if (empty($detail)) {
        $errors[] = "Product detail is required.";
    }

    if (!is_numeric($category) || (int)$category <= 0 || (int)$category >= 5) {
        $errors[] = "Category must be a valid number and only 1 to 4.";
    }

    if (empty($imageUpload)) {
        $errors[] = "Product image is required.";
    } else {
        $target_dir = "../products/";
        $image_name = basename($_FILES["image-upload"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image-upload"]["tmp_name"]);
        if ($check === false) {
            $errors[] = "Uploaded file is not a valid image.";
        } 

        $allowed_types = ["jpg", "jpeg", "png"];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, and PNG files are allowed.";
        }

        if ($_FILES["image-upload"]["size"] > 2 * 1024 * 1024) {
            $errors[] = "Image size must be less than 2MB.";
        }

        // Check for duplicate image
        $checkStmt = $_db->prepare("SELECT COUNT(*) FROM product WHERE image = :image");
        $checkStmt->bindValue(':image', $image_name, PDO::PARAM_STR);
        $checkStmt->execute();
        if ($checkStmt->fetchColumn() > 0) {
            $errors[] = "This image is already used by another product.";
        }
    }

    // If no errors, insert into DB
    if (empty($errors)) {
        if (move_uploaded_file($_FILES["image-upload"]["tmp_name"], $target_file)) {
            try {
                $stmt = $_db->prepare("INSERT INTO product (Product_name, price, image, quantity, detail, category_id) 
                                       VALUES (:Product_name, :price, :image, :quantity, :detail, :category_id)");

                $price = (float)$perPrice;
                $qty = (int)$quantity;
                $category_id = (int)$category;

                $stmt->bindParam(":Product_name", $product, PDO::PARAM_STR);
                $stmt->bindParam(":price", $price, PDO::PARAM_STR);
                $stmt->bindParam(":image", $image_name, PDO::PARAM_STR);
                $stmt->bindParam(":quantity", $qty, PDO::PARAM_INT);
                $stmt->bindParam(":detail", $detail, PDO::PARAM_STR);
                $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);

                $stmt->execute();
                $success = "Product added successfully!";
                $product = $quantity = $perPrice = $detail = $category = ''; // Reset form
            } catch (PDOException $e) {
                if ($e->getCode() == '23000') {
                    $errors[] = "This image is already used by another product.";
                } else {
                    $errors[] = "Insert failed: " . $e->getMessage();
                }
            }
        } else {
            $errors[] = "Failed to upload image.";
        }
    }
}
?>

<?php if (!empty($success)): ?>
    <div style="padding: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 10px;">
        <?= $success ?>
    </div>
<?php endif; ?>

<div class="max-w-4xl mx-auto my-6 p-8 bg-slate-800/40 backdrop-blur-md rounded-2xl shadow-xl border border-slate-700/60">
    <div class="mb-6 border-b border-slate-700/60 pb-4">
        <h2 class="text-2xl font-bold text-white tracking-wide">Create New Product</h2>
        <p class="text-slate-400 text-sm mt-1">Fill in the fields below to add a new inventory item to the platform.</p>
    </div>
    
    <form action="createproduct.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php if (!empty($errors)): ?>
            <div class="bg-red-500/10 text-red-400 p-4 rounded-xl border border-red-500/20 text-sm font-medium">
                <?= displayError($errors) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Product Name</label>
                <?= inputField('text', 'product', 'Enter product name', $product, 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Quantity</label>
                <?= inputField('number', 'qty', 'Enter quantity', $quantity, 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Price (RM)</label>
                <?= inputField('number', 'perPrice', 'Enter price', $perPrice, 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Detail Descriptions</label>
                <?= inputField('text', 'detail', 'Enter product details', $detail, 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Category ID <span class="text-slate-500 font-normal">(1 to 4)</span></label>
                <?= inputField('number', 'category', 'Must be 1-4', $category, 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Product Cover Image</label>
                <?= inputField('file', 'image-upload', '', '', 'block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-slate-600 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 hover:file:text-white transition-all cursor-pointer') ?>
            </div>
        </div>

        <div class="flex gap-4 pt-4 border-t border-slate-700/60">
            <?= inputField('submit', 'submit', '', 'Submit New Product', 'flex-1 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-blue-600/10 cursor-pointer') ?>
            <?= inputField('reset', 'reset', '', 'Reset Form', 'flex-1 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm rounded-xl transition-all border border-slate-700/60 cursor-pointer') ?>
        </div>
    </form>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>