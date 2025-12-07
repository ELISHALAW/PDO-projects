<?php
include '../_base.php';

$_title = 'Product | Detail';

// Handle form submission to update cart
if (is_post()) {
    $id   = req('id');
    $unit = req('unit');
    update_cart($id, $unit);
    redirect(); // refresh page
}

// Get product ID from request
$id = req('id');

if (!$id) {
    echo "<p style='color:red'>❌ Product ID is missing from the request.</p>";
    include '../foot.php';
    exit;
}

// Fetch product from database
$stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
$stm->execute([$id]);
$p = $stm->fetch(PDO::FETCH_OBJ);

if (!$p) {
    echo "<p style='color:red'>❌ Product not found in the database.</p>";
    include '../foot.php';
    exit;
}
?>

<!-- CSS Styling -->
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f5f5f5;
        color: #333;
    }

    .product-detail-container {
        max-width: 800px;
        margin: 50px auto;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    #photo {
        display: block;
        margin: 0 auto 20px;
        border-radius: 10px;
        width: 250px;
        height: 250px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }

    #photo:hover {
        transform: scale(1.05);
    }

    .table.detail {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table.detail th, .table.detail td {
        padding: 12px 15px;
        border-bottom: 1px solid #ccc;
        text-align: left;
    }

    .table.detail th {
        background-color: black;
        color: white;
        width: 150px;
    }

    .table.detail tr:hover {
        background-color: #f1f1f1;
    }

    .quantity-form {
        margin-top: 10px;
    }

    .quantity-form label {
        font-weight: bold;
    }

    .quantity-form input[type="number"] {
        width: 70px;
        padding: 6px;
        margin-right: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .quantity-form button {
        background-color: #28a745;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .quantity-form button:hover {
        background-color: #218838;
    }

    .back-link {
        display: inline-block;
        margin-top: 20px;
        text-decoration: none;
        color: #007BFF;
        font-weight: bold;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .stock-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 14px;
        color: white;
        background-color: #6c757d;
        margin-left: 10px;
    }

    .stock-available {
        background-color: #28a745;
    }

    .stock-low {
        background-color: #ffc107;
        color: black;
    }

    .stock-out {
        background-color: #dc3545;
    }
</style>

<!-- Product Detail Content -->
<div class="product-detail-container">
    <img src="../products/<?= e($p->image) ?>" id="photo" alt="Product Image">

    <table class="table detail">
        <tr>
            <th>ID</th>
            <td><?= e($p->product_id) ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?= e($p->Product_name) ?></td>
        </tr>
        <tr>
            <th>Price</th>
            <td>RM <?= number_format($p->price, 2) ?></td>
        </tr>
        <tr>
            <th>Stock</th>
            <td>
                <?php
                $stock = (int)$p->quantity;
                if ($stock > 10) {
                    echo "<span class='stock-badge stock-available'>In stock ($stock)</span>";
                } elseif ($stock > 0) {
                    echo "<span class='stock-badge stock-low'>Low stock ($stock left)</span>";
                } else {
                    echo "<span class='stock-badge stock-out'>Out of stock</span>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <th>Details</th>
            <td><?= nl2br(e($p->detail)) ?></td>
        </tr>
        <tr>
            <th>Unit</th>
            <td>
                <?php
                $cart = get_cart();
                $unit = $cart[$p->product_id] ?? 0;
                ?>
                <form method="post" class="quantity-form">
                    <?= html_hidden('id', $p->product_id) ?>
                    <label>Quantity:</label>
                    <?= inputNumber('number','unit',1,$stock,e($unit)) ?>
                    <button type="submit">Add to Cart</button>
                    <?= $unit ? '✅' : '' ?>
                </form>
            </td>
        </tr>
    </table>

    <a href="list.php" class="back-link">← Back to Product List</a>
</div>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php include '../foot.php'; ?>