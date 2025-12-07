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

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    color: black;
}
.header-container {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background: #1e1e1e;
    border-radius: 5px;
}
.button-link {
    color: white;
    text-decoration: none;
    font-size: 18px;
    padding: 10px;
    background: #333;
    border-radius: 5px;
}
.container {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.1);
    max-width: 1300px;
    margin: auto;
}
form div {
    margin-bottom: 10px;
}
.form_action--button input {
    width: 48%;
    cursor: pointer;
    background: #007BFF;
    border: none;
    color: white;
    font-size: 16px;
    padding: 15px;
    border-radius: 10px;
    transition: 0.3s;
}
.form_action--button input:hover {
    background: #0056b3;
}
.button-link {
    width: 48%;
    cursor: pointer;
    background: #007BFF;
    border: none;
    color: white;
    font-size: 16px;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    transition: 0.3s;
}
.button-link:hover {
    background: #0056b3;
}
.input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #555;
    color: black;
}
.form_action--button {
    display: flex;
    justify-content: space-between;
}
.error {
    color: red;
    margin-bottom: 10px;
}
</style>

<div class="container">
    <form action="createproduct.php" method="POST" enctype="multipart/form-data">
        <?= displayError($errors) ?>
        <div>
            <label for="product">Product Name</label>
            <?= inputField('text', 'product', 'Enter product name', $product, 'input') ?>
        </div>
        <div>
            <label for="qty">Quantity</label>
            <?= inputField('number', 'qty', 'Enter the quantity', $quantity, 'input') ?>
        </div>
        <div>
            <label for="perPrice">Price</label>
            <?= inputField('number', 'perPrice', 'Enter the price', $perPrice, 'input') ?>
        </div>
        <div>
            <label for="detail">Detail</label>
            <?= inputField('text', 'detail', 'Enter the detail', $detail, 'input') ?>
        </div>
        <div>
            <label for="category">Category</label>
            <?= inputField('number', 'category', 'Category must between 1 to 4', $category, 'input') ?>
        </div>
        <div>
            <label for="image-upload">Upload Image</label>
            <?= inputField('file', 'image-upload', '', '', 'input') ?>
        </div>
        <div class="form_action--button">
            <?= inputField('submit', 'submit', 'input', 'Submit') ?>
            <?= inputField('reset', 'reset', 'input', 'Reset') ?>
        </div>
    </form>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>