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

<div class="max-w-3xl mx-auto my-4 p-6 bg-white rounded-2xl shadow-sm border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Create New Product</h2>
    
    <form action="createproduct.php" method="POST" enctype="multipart/form-data" class="space-y-5">
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-lg border border-red-200 text-sm">
                <?= displayError($errors) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700">Product Name</label>
                <?= inputField('text', 'product', 'Enter product name', $product, 'mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Quantity</label>
                <?= inputField('number', 'qty', 'Enter quantity', $quantity, 'mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Price (RM)</label>
                <?= inputField('number', 'perPrice', 'Enter price', $perPrice, 'mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700">Detail</label>
                <?= inputField('text', 'detail', 'Enter product details', $detail, 'mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Category ID</label>
                <?= inputField('number', 'category', 'Must be 1-4', $category, 'mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Product Image</label>
                <?= inputField('file', 'image-upload', '', '', 'mt-1 w-full p-2 border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-indigo-600 cursor-pointer') ?>
            </div>
        </div>

        <div class="flex gap-4 pt-4">
            <?= inputField('submit', 'submit', '', 'Submit Product', 'flex-1 py-3 bg-gray-900 text-white font-bold rounded-lg hover:bg-indigo-600 transition-all cursor-pointer') ?>
            <?= inputField('reset', 'reset', '', 'Reset Form', 'flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-all cursor-pointer') ?>
        </div>
    </form>
</div>