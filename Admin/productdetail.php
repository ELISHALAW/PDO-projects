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

<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800">Edit Product</h2>
        </div>

        <form action="productdetail.php?id=<?= e($id) ?>" method="POST" enctype="multipart/form-data" class="p-8">
            <input type="hidden" name="product_id" value="<?= e($fetch['product_id']) ?>">
            
            <div class="grid md:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase">Current Image</label>
                    <img src="../products/<?= e($fetch['image']) ?>" alt="Product" 
                         class="w-full h-64 object-cover rounded-xl shadow-md border-4 border-white">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Change Image</label>
                        <?= inputField('file','image','','','w-full p-2 text-sm border border-gray-200 rounded-lg') ?>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Product Name</label>
                        <?= inputField('text','Product_name','', e($fetch['Product_name']), 'mt-1 w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Price</label>
                            <?= inputField('number','price','',e($fetch['price']), 'mt-1 w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Quantity</label>
                            <?= inputField('number','quantity','',e($fetch['quantity']), 'mt-1 w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Category</label>
                        <?= html_select('category_id','category_id',$categories,$fetch['category_id'], 'mt-1 w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none') ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Detail</label>
                        <?= html_textarea('detail','','3','30','mt-1 w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none', e($fetch['detail'])) ?>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end gap-6">
                <a href="productlist.php" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">Back to List</a>
                <?= html_submit('submit','submit','px-8 py-3 bg-gray-900 text-white font-bold rounded-lg hover:bg-indigo-600 transition-all','Save Changes') ?>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>