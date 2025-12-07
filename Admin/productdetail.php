<?php 
require __DIR__ . '/headandFoot/head.php'; 

// Initialize error messages array
$errors = [];

// Handle form submission (update)
if (is_post()) {
    $id = $_POST['product_id'] ?? 0;
    $Product_name = $_POST['Product_name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    $category_id = $_POST['category_id'] ?? 0;
    $detail = $_POST['detail'] ?? '';

    // Validate required fields
    if (empty($Product_name)) {
        $errors[] = "Product name is required.";
    }
    if (empty($price) || $price <= 0) {
        $errors[] = "Price must be a positive number.";
    }
    if (empty($quantity) || $quantity <= 0) {
        $errors[] = "Quantity must be a positive number.";
    }
    if (empty($category_id) || !in_array($category_id, [1, 2, 3])) {
        $errors[] = "Please select a valid category.";
    }

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $imageName = basename($_FILES['image']['name']);
        $imageTmp = $_FILES['image']['tmp_name'];
        $uploadDir = '../products/';
        
        // Validate image upload
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
        
        if ($_FILES['image']['size'] > 5000000) {
            $errors[] = "Image size must be less than 5MB.";
        }

        if (empty($errors)) {
            // Check if image name already exists for another product
            $stmtCheckImage = $_db->prepare("SELECT COUNT(*) FROM product WHERE image = :image AND product_id != :id");
            $stmtCheckImage->bindParam(':image', $imageName);
            $stmtCheckImage->bindParam(':id', $id);
            $stmtCheckImage->execute();
            $imageExists = $stmtCheckImage->fetchColumn();

            if ($imageExists > 0) {
                $errors[] = "An image with the same name already exists.";
            } else {
                move_uploaded_file($imageTmp, $uploadDir . $imageName);
            }
        }
    } else {
        // Keep old image
        $stmtOld = $_db->prepare("SELECT image FROM product WHERE product_id = :id");
        $stmtOld->bindParam(":id", $id, PDO::PARAM_INT);
        $stmtOld->execute();
        $imageName = $stmtOld->fetchColumn();
    }

    // If there are no errors, proceed with the update
    if (empty($errors)) {
        $stmt = $_db->prepare("UPDATE product SET 
            Product_name = :Product_name,
            price = :price,
            quantity = :quantity,
            category_id = :category_id,
            detail = :detail,
            image = :image
            WHERE product_id = :product_id");

        $stmt->bindParam(':Product_name', $Product_name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':detail', $detail);
        $stmt->bindParam(':image', $imageName);
        $stmt->bindParam(':product_id', $id);

        if ($stmt->execute()) {
            echo "<p style='text-align:center; color:green;'>Product updated successfully!</p>";
        } else {
            echo "<p style='text-align:center; color:red;'>Error updating product.</p>";
        }
    } else {
        echo "<ul style='color: red; text-align:center;'>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
}

// Fetch product info for form
$id = $_GET['id'] ?? 1;
$stmt = $_db->prepare("SELECT * FROM product WHERE product_id = :product_id");
$stmt->bindParam(":product_id", $id, PDO::PARAM_INT);
$stmt->execute();
$fetch = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<a href="../products/"></a>
<style>
.product-detail-table {
    width: 80%;
    margin: 30px auto;
    border-collapse: collapse;
    border: 2px solid #ddd;
    font-family: Arial, sans-serif;
}
.product-detail-table td {
    padding: 12px;
    border: 1px solid #ccc;
    vertical-align: top;
}
.image-cell {
    width: 200px;
    text-align: center;
    background-color: #f9f9f9;
    justify-content: center;
    align-items: center;
}
.image-cell img {
    width: 100%;
    max-width: 180px;
    height: auto;
    border-radius: 10px;
    transition: transform 0.3s ease;
}
.image-cell img:hover {
    transform: scale(1.05);
}
.text-cell {
    background-color: #fff;
    font-size: 16px;
    line-height: 1.6;
}
textarea {
    resize: none;
}
.submitting {
    padding: 10px 20px; 
    background-color: #007BFF; 
    color: white;
    border: none; 
    border-radius: 5px;
    cursor: pointer;
}
.files input[type=file] {
    text-align: center;
    align-items: center;
    justify-content: center;
}
</style>

<?php
$categories = [
    1 => 'Asus',
    2 => 'Huawei',
    3 => 'Acer',
    4 => 'Dell',
];
?>

<div class="center-form-wrapper">
<form action="productdetail.php?id=<?= e($id) ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= e($fetch['product_id']) ?>">
    <table class="product-detail-table">
        <tr>
            <td class="image-cell" rowspan="6">
                <img src="../products/<?= e($fetch['image']) ?>" alt="Product Image">
                <br><br>
                <div class="files" style="text-align:center;">
                     <?= inputField('file','image','','','file') ?>
                </div>
            </td>
            <td class="text-cell">
                Name: <?= inputField('text','Product_name','', e($fetch['Product_name']),'') ?>
            </td>
        </tr>
        <tr>
            <td class="text-cell">
                Price: <?= inputField('number','price','',e($fetch['price']),'') ?>
            </td>
        </tr>
        <tr>
            <td class="text-cell">
                Quantity: <?= inputField('number','quantity','',e($fetch['quantity']),'') ?>
            </td>
        </tr>
        <tr>
            <td class="text-cell">
                Category: 
                <?= html_select('category_id','category_id',$categories,$fetch['category_id']) ?>
            </td>
        </tr>
        <tr>
            <td class="text-cell">
                Detail: <br>
                <?= html_textarea('detail','','3','30','',e($fetch['detail'])) ?>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 15px;">
        <?= html_submit('submit','submit','submitting','Save Changes') ?>
        <a href="productlist.php">Back to Product page</a>
    </div>
</form>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>
