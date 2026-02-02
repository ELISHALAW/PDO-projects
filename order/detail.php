<?php
include '../head.php';

// ----------------------------------------------------------------------------

// (1) Authorization (member)
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='../login.php';</script>";
    exit();
}

// (2) Return order (based on id) belonging to the user
$id = req('id');
$stm = $_db->prepare('
    SELECT * FROM `orders`
    WHERE order_id = ? AND user_id = ?
');
$stm->execute([$id, $_SESSION['id']]); // <-- Fixed here
$o = $stm->fetch();
if (!$o) {
    temp('error', 'Order not found.');
    redirect('history.php');
}

// (3) Return items (and products) belong to the order
$stm = $_db->prepare('
    SELECT i.*, p.Product_name, p.image
    FROM order_item AS i, product AS p
    WHERE i.product_id = p.product_id
    AND i.order_id = ?
');
$stm->execute([$id]);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Order | Detail';

?>

<style>
    /* Detail page modern table + modal */
    .popup {
        width: 92px;
        height: 64px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
    }

    #popup-image {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 92%;
        max-height: 92%;
        z-index: 1000;
        box-shadow: 0 8px 30px rgba(2, 6, 23, 0.45);
        border-radius: 8px;
        background: #fff;
        padding: 6px;
    }

    #popup-background {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 999;
    }

    .detail-wrap {
        width: 95%;
        max-width: 1000px;
        margin: 100px auto;
        font-family: "Segoe UI", Roboto, Arial, sans-serif;
    }

    table {
        width: 100%;
        margin-top: 12px;
        border-collapse: separate;
        box-shadow: 0 6px 18px rgba(13, 38, 63, 0.06);
        border-radius: 10px;
        overflow: hidden;
    }

    table th,
    table td {
        padding: 12px 14px;
    }

    table th {
        background: linear-gradient(90deg, #0ea5e9, #3b82f6);
        color: #fff;
        text-align: left;
        font-weight: 600;
    }

    table td {
        background: #fff;
        border-bottom: 1px solid #eef3f8;
    }

    tr:nth-child(even) td {
        background: #fbfdff;
    }

    tr:hover td {
        background: #f7fbff;
    }

    .right {
        text-align: right;
    }

    a {
        color: #6b21a8;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Order meta and summary */
    .order-meta {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .order-info {
        color: #0f172a;
    }

    .order-info div {
        margin-bottom: 6px;
    }

    .muted {
        color: #64748b;
        font-size: 13px;
    }

    .order-summary {
        background: linear-gradient(90deg, #06b6d4, #3b82f6);
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        min-width: 180px;
        text-align: center;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.08);
    }

    .order-summary .sum-label {
        font-size: 13px;
        opacity: 0.95;
    }

    .order-summary .sum-value {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .summary-row td {
        background: linear-gradient(90deg, #e6f4ff, #f1f8ff);
        font-weight: 700;
    }

    .actions-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 12px;
        align-items: center;
    }

    .checkout-link {
        background: linear-gradient(90deg, #059669, #10b981);
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
    }


    @media (max-width:700px) {
        .popup {
            width: 52px;
            height: 40px;
        }

        table th,
        table td {
            padding: 10px;
            font-size: 13px;
        }
    }
</style>

<!-- Popup Image Modal -->
<div id="popup-background" onclick="hidePopup()"></div>
<img id="popup-image" onclick="hidePopup()">

<div class="detail-wrap">
    <div class="order-meta">
        <div class="order-info">
            <div><strong>Order ID:</strong> <?= htmlspecialchars($o->order_id) ?></div>
            <div><strong>Date:</strong> <?= htmlspecialchars($o->date) ?></div>
            <div class="muted"><?= count($arr) ?> item(s)</div>
        </div>

        <div class="order-summary">
            <div class="sum-label">Items</div>
            <div class="sum-value"><?= (int)$o->count ?></div>
            <div class="sum-label">Total</div>
            <div class="sum-value">RM <?= number_format((float)$o->total, 2) ?></div>
        </div>
    </div>

    <table class="table">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price (RM)</th>
            <th>Unit</th>
            <th>Subtotal (RM)</th>
            <th>Photo</th>
        </tr>

        <?php foreach ($arr as $i): ?>
            <tr>
                <td><?= (int)$i->product_id ?></td>
                <td><?= htmlspecialchars($i->Product_name) ?></td>
                <td class="right"><?= number_format((float)$i->price, 2) ?></td>
                <td class="right"><?= (int)$i->unit ?></td>
                <td class="right"><?= number_format((float)$i->subtotal, 2) ?></td>
                <td>
                    <?php if (!empty($i->image)): ?>
                        <img src="/products/<?= htmlspecialchars($i->image) ?>" class="popup" onclick="showPopup(this.src)">
                    <?php else: ?>
                        No Image
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr class="summary-row">
            <td colspan="3"></td>
            <td class="right"><?= (int)$o->count ?></td>
            <td class="right"><?= number_format((float)$o->total, 2) ?></td>
            <td></td>
        </tr>
    </table>

    <div class="actions-row">
        <a href="history.php">← Back to history</a>
        <a class="checkout-link" href="payment.php">Proceed to Payment →</a>
    </div>

</div>

<script>
    function showPopup(src) {
        document.getElementById('popup-image').src = src;
        document.getElementById('popup-background').style.display = 'block';
        document.getElementById('popup-image').style.display = 'block';
    }

    function hidePopup() {
        document.getElementById('popup-background').style.display = 'none';
        document.getElementById('popup-image').style.display = 'none';
    }
</script>

<?php
