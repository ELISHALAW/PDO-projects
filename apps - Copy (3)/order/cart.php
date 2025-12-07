<?php
include '../head.php';

$_units = [];

if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='login.php';</script>";
    exit();
}

// Handle POST Requests
if (is_post()) {
    if (isset($_POST['btn']) && req('btn') === 'clear') {
        set_cart();
        redirect('?');
    }

    if (isset($_POST['remove_id'])) {
        $remove_id = req('remove_id');
        remove_from_cart($remove_id);
        redirect('?');
    }

    if (isset($_POST['id']) && isset($_POST['unit'])) {
        $id = req('id');
        $unit = req('unit');
        update_cart($id, $unit);
        redirect('?');
    }
}

$_title = 'Order | Shopping Cart';
?>

<style>
    /* (your CSS remains the same) */
    .popup {
        width: 100px;
        height: 100px;
    }

    .table {
        margin-top: 100px;
        border-collapse: collapse;
        width: 100%;
    }

    .table th,
    .table td {
        border: 1px solid #333;
        padding: 8px;
    }

    .table th {
        background-color: #666;
        color: white;
    }

    .table tr:hover td {
        background-color: #ccc;
    }

    .table td:has(.popup) {
        position: relative;
    }

    .table .popup {
        position: absolute;
        top: 50%;
        left: 100%;
        transform: translate(5px, -50%);
        z-index: 1;
        border: 1px solid #333;
        display: none;
    }

    .table tr:hover .popup {
        display: block;
    }

    .remove-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    }

    .remove-btn:hover {
        background-color: darkred;
    }

    .checkout-btn {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: green;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .checkout-btn:hover {
        background-color: darkgreen;
    }

</style>

<table class="table">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Price (RM)</th>
        <th>Unit</th>
        <th>Action</th>
        <th>Subtotal (RM)</th>
    </tr>

    <?php
    $count = 0;
    $total = 0;

    $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
    $cart = get_cart();

    foreach ($cart as $id => $unit):
        $stm->execute([$id]);
        $p = $stm->fetch();

        if (!$p) continue;

        $subtotal = $p->price * $unit;
        $count += $unit;
        $total += $subtotal;
    ?>
        <tr>
            <td><?= htmlspecialchars($p->product_id) ?></td>
            <td><?= htmlspecialchars($p->Product_name) ?></td>
            <td><?= number_format($p->price, 2) ?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?= $p->product_id ?>">
                    <input type="number" name="unit" min="1" value="<?= $unit ?>" onchange="this.form.submit()">
                </form>
            </td>
            <td>
                <form method="post" action="" onsubmit="return confirm('Are you sure you want to remove this product?');">
                    <input type="hidden" name="remove_id" value="<?= $p->product_id ?>">
                    <button type="submit" class="remove-btn">Remove</button>
                </form>
            </td>
            <td>
                <?= number_format($subtotal, 2) ?>
                <img src="/products/<?= htmlspecialchars($p->image) ?>" class="popup">
            </td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <th colspan="3"></th>
        <th><?= $count ?></th>
        <th colspan="2"><?= number_format($total, 2) ?></th>
    </tr>
</table>

<!-- Checkout Button -->
<form method="post" action="checkout.php">
    <input type="hidden" name="checkout" value="1">
    <button type="submit" class="checkout-btn">Checkout</button>
</form>

<a href="../product/list.php">Back to Product page</a>

<?php
include '../foot.php';

// Utility Functions
function remove_from_cart($id)
{
    $cart = get_cart();
    if (isset($cart[$id])) {
        unset($cart[$id]);
        set_cart($cart);
    }
}
?>