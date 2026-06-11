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
    // Updated to allow all 4 categories mapping to your array
    if (empty($category_id) || !in_array($category_id, [1, 2, 3, 4])) {
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
            echo "<p style='text-align:center; color:green; margin-top:20px;'>Product updated successfully!</p>";
        } else {
            echo "<p style='text-align:center; color:red; margin-top:20px;'>Error updating product.</p>";
        }
    } else {
        echo "<ul style='color: red; text-align:center; margin-top:20px;'>";
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

$categories = [
    1 => 'Asus',
    2 => 'Huawei',
    3 => 'Acer',
    4 => 'Dell',
];
?>
<a href="../products/"></a>

<div class="max-w-5xl mx-auto py-10 px-4">
    <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl shadow-xl border border-slate-700/60 overflow-hidden">
        
        <div class="bg-slate-800/60 px-8 py-6 border-b border-slate-700/60">
            <h2 class="text-xl font-bold text-white tracking-wide">Edit Product Specifications</h2>
            <p class="text-xs text-slate-400 mt-1">Modify pricing tiers, description parameters, or refresh image assets.</p>
        </div>

        <form action="productdetail.php?id=<?= e($id) ?>" method="POST" enctype="multipart/form-data" class="p-8">
            <input type="hidden" name="product_id" value="<?= e($fetch['product_id']) ?>">
            
            <div class="grid md:grid-cols-5 gap-10">
                
                <div class="space-y-4 md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Current Asset Image</label>
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl opacity-30 blur group-hover:opacity-50 transition duration-300"></div>
                        <img src="../products/<?= e($fetch['image']) ?>" alt="Product" 
                             class="relative w-full h-64 object-cover rounded-xl shadow-2xl border border-slate-600/50 bg-slate-900">
                    </div>
                    
                    <div class="pt-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Change Image File</label>
                        <?= inputField('file','image','','','block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-slate-600 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 hover:file:text-white transition-all cursor-pointer') ?>
                    </div>
                </div>

                <div class="space-y-5 md:col-span-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Product Name</label>
                        <?= inputField('text','Product_name','', e($fetch['Product_name']), 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Price (RM)</label>
                            <?= inputField('number','price','',e($fetch['price']), 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Quantity Stock</label>
                            <?= inputField('number','quantity','',e($fetch['quantity']), 'w-full bg-slate-900/60 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all') ?>
                        </div>
                    </div>

                   <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Category Designation</label>
                        <select name="category_id" id="category_id" 
                                class="w-full bg-slate-900/90 border border-slate-700/50 text-slate-200 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all cursor-pointer appearance-none">
                            <?php foreach ($categories as $id_cat => $name_cat): ?>
                                <option value="<?= $id_cat ?>" <?= $id_cat == $fetch['category_id'] ? 'selected' : '' ?> class="bg-slate-900 text-slate-200">
                                    <?= e($name_cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Details Overview</label>
                        <textarea name="detail" 
                                  rows="4" 
                                  class="w-full bg-slate-900/90 border border-slate-700/50 text-slate-200 placeholder-slate-500 rounded-xl p-3.5 text-sm font-medium outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 shadow-inner transition-all resize-none" 
                                  placeholder="Enter product details..."><?= e($fetch['detail']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-700/60 flex items-center justify-end gap-4">
                <a href="productlist.php" class="px-5 py-2.5 text-xs font-semibold text-slate-400 hover:text-white transition-colors">Back to List</a>
                <?= html_submit('submit','submit','px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-blue-600/10 cursor-pointer','Save Changes') ?>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>