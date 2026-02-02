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
        background: #f9f9f9;
        color: #333;
        line-height: 1.6;
    }

    .product-detail-container {
        max-width: 900px;
        margin: 60px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease;
    }

    .product-detail-container:hover {
        transform: translateY(-3px);
    }

    #photo {
        display: block;
        margin: 0 auto 25px;
        border-radius: 12px;
        width: 300px;
        height: 300px;
        object-fit: cover;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
    }

    #photo:hover {
        transform: scale(1.05);
    }

    .table.detail {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 15px;
    }

    .table.detail th,
    .table.detail td {
        padding: 14px 18px;
        border-bottom: 1px solid #e0e0e0;
        vertical-align: top;
    }

    .table.detail th {
        background-color: #222;
        color: #fff;
        width: 160px;
        text-align: left;
        border-radius: 8px 0 0 8px;
    }

    .table.detail tr:hover td {
        background-color: #f7f7f7;
    }

    .quantity-form {
        margin-top: 10px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .quantity-form label {
        font-weight: bold;
        margin-right: 5px;
    }

    .quantity-form input[type="number"] {
        width: 70px;
        padding: 6px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    .quantity-form button {
        background-color: #007BFF;
        color: #fff;
        padding: 8px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .quantity-form button:hover {
        background-color: #0056b3;
    }

    .back-link {
        display: inline-block;
        margin-top: 25px;
        text-decoration: none;
        color: #007BFF;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* Stock badges */
    .stock-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 25px;
        font-size: 14px;
        color: #fff;
        font-weight: 600;
        margin-left: 10px;
        min-width: 90px;
        text-align: center;
    }

    .stock-available {
        background-color: #28a745;
    }

    .stock-low {
        background-color: #ffc107;
        color: #333;
    }

    .stock-out {
        background-color: #dc3545;
    }

    /* Responsive tweaks */
    @media (max-width: 600px) {
        .product-detail-container {
            padding: 20px;
        }

        #photo {
            width: 100%;
            height: auto;
        }

        .table.detail th,
        .table.detail td {
            padding: 10px 12px;
        }
    }
</style>

<!-- Product Detail Content -->
<div class="product-detail-container">
    <img src="../products/<?= e($p->image) ?>" id="photo" alt="<?= e($p->Product_name) ?>">

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
            <th>Quantity</th>
            <td>
                <?php
                $cart = get_cart();
                $unit = $cart[$p->product_id] ?? 0;
                ?>
                <form method="post" class="quantity-form">
                    <?= html_hidden('id', $p->product_id) ?>
                    <label for="unit">Qty:</label>
                    <?= inputNumber('number', 'unit', 1, $stock, e($unit)) ?>
                    <button type="submit">Add to Cart</button>
                    <?= $unit ? '✅' : '' ?>
                </form>
            </td>
        </tr>
    </table>

    <a href="list.php" class="back-link">← Back to Product List</a>
</div>

<script>
    // Submit form on select change if any (from inputNumber)
    $('select').on('change', e => e.target.form.submit());
</script>