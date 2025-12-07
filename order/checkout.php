<?php
include '../_base.php';

if (!isset($_SESSION['id'])) {
    die("Authorization failed: Please log in. <a href='login.php'>Go to login</a>");
}

// Only accept POST with 'checkout' action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $cart = get_cart();
    if (!$cart) {
        die("Cart is empty. <a href='cart.php'>Go back to cart</a>");
    }

    $invalid_products = [];
    $checkStm = $_db->prepare('SELECT COUNT(*) FROM product WHERE product_id = ? AND quantity >= ?');

    foreach ($cart as $product_id => $unit) {
        $unit = (int)$unit;
        $checkStm->execute([$product_id, $unit]);
        $exists = $checkStm->fetchColumn();
        if (!$exists) {
            $invalid_products[] = $product_id;
        }
    }

    if (!empty($invalid_products)) {
        $error = 'Invalid or insufficient stock for products: ' . implode(', ', $invalid_products);
        temp('error', $error);
        die("$error <br> <a href='cart.php'>Go back to cart</a>");
    }

    try {
        $_db->beginTransaction();

        $orderStm = $_db->prepare('INSERT INTO `orders` (date, user_id) VALUES (NOW(), ?)');
        $orderStm->execute([$_SESSION['id']]);
        $orderId = $_db->lastInsertId();

        $itemStm = $_db->prepare('
            INSERT INTO `order_item`
                (order_id, product_id, price, unit, subtotal)
             VALUES
                (?, ?, (SELECT price FROM product WHERE product_id = ?), ?, 
                 (SELECT price FROM product WHERE product_id = ?) * ?)
        ');

        $stockStm = $_db->prepare('
            UPDATE product
                SET quantity = quantity - :unit
              WHERE product_id = :product_id
        ');

        foreach ($cart as $product_id => $unit) {
            $unit = (int)$unit;
            $itemStm->execute([
                $orderId, $product_id, $product_id, $unit, $product_id, $unit
            ]);
            $stockStm->execute([
                ':unit' => $unit,
                ':product_id' => $product_id
            ]);
        }

        $totStm = $_db->prepare('
            UPDATE `orders`
                SET count = (SELECT SUM(unit) FROM order_item WHERE order_id = ?),
                    total = (SELECT SUM(subtotal) FROM order_item WHERE order_id = ?)
              WHERE order_id = ?
        ');
        $totStm->execute([$orderId, $orderId, $orderId]);

        $_db->commit();
        set_cart(); // Clear cart

        temp('info', 'Order placed successfully!');
        header("Location: detail.php?id=$orderId");
        exit;

    } catch (Exception $e) {
        $_db->rollBack();
        $error = 'Failed to process order: ' . $e->getMessage();
        temp('error', $error);
        die("$error <br> <a href='cart.php'>Go back to cart</a>");
    }
}

die("Invalid access. <a href='cart.php'>Go back to cart</a>");
?>