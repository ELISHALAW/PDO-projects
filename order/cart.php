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
    /* Modernized cart CSS */
    .cart-container {
        width: 95%;
        max-width: 1100px;
        margin: 200px 200px;
        position: fixed;
        font-family: "Segoe UI", Roboto, Arial, sans-serif;
        padding: 18px;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        box-shadow: 0 6px 18px rgba(13, 38, 63, 0.06);
        border-radius: 10px;
        overflow: hidden;
    }

    .table th,
    .table td {
        padding: 12px 14px;
        vertical-align: middle;
    }

    .table th {
        background: linear-gradient(90deg, #0ea5e9, #3b82f6);
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        text-align: left;
    }

    .table td {
        background: #fff;
        border-bottom: 1px solid #f0f2f5;
        color: #0f172a;
        font-size: 14px;
    }

    .table tr:hover td {
        background: #fbfdff;
    }

    .table img.popup {
        width: 52px;
        height: 52px;
        object-fit: cover;
        border-radius: 6px;
    }

    input[type="number"] {
        width: 76px;
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #e6eef8;
    }

    .remove-btn {
        background-color: #ef4444;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
    }

    .remove-btn:hover {
        background-color: #dc2626;
    }

    .cart-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
    }

    .checkout-btn {
        padding: 12px 22px;
        background: linear-gradient(90deg, #059669, #10b981);
        color: white;
        border-radius: 10px;
        border: none;
        cursor: pointer;
    }

    .back-link {
        color: #6b21a8;
        text-decoration: none;
        font-size: 14px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 700px) {
        .cart-container {
            padding: 8px;
        }

        .table th,
        .table td {
            padding: 10px;
            font-size: 13px;
        }

        input[type="number"] {
            width: 60px;
        }

        .table img.popup {
            width: 40px;
            height: 40px;
        }

        .cart-actions {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="cart-container">
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

    <div class="cart-actions">
        <a class="back-link" href="../product/list.php">← Back to Product page</a>
        <form method="post" action="checkout.php">
            <input type="hidden" name="checkout" value="1">
            <button type="submit" class="checkout-btn">Checkout</button>
        </form>
    </div>

</div>

<?php

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


<!-- Later can make the css better -->