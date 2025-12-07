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
    .popup {
        width: 100px;
        height: 100px;
        cursor: pointer;
    }

    /* Optional: enlarge image when clicked */
    #popup-image {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
        z-index: 1000;
        box-shadow: 0 0 10px #000;
        border: 5px solid #fff;
        background: #fff;
    }

    #popup-background {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 999;
    }

    /* Table Styling */
    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 12px;
        text-align: center;
    }

    th {
        background-color: #333;
        color: white;
    }

    td {
        background-color: #f4f4f4;
    }

    tr:nth-child(even) td {
        background-color: #e2e2e2;
    }

    tr:hover td {
        background-color: #ddd;
    }

    .right {
        text-align: right;
    }

    /* Link styling */
    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    
</style>

<!-- Popup Image Modal -->
<div id="popup-background" onclick="hidePopup()"></div>
<img id="popup-image" onclick="hidePopup()">

<form class="form">
    <label>Order ID</label>
    <b><?= htmlspecialchars($o->order_id) ?></b>
    <br>

    <label>Date</label>
    <div><?= htmlspecialchars($o->date) ?></div>
    <br>

    <label>Count</label>
    <div><?= (int)$o->count ?></div>
    <br>

    <label>Total</label>
    <div>RM <?= number_format((float)$o->total, 2) ?></div>
    <br>
</form>

<p><?= count($arr) ?> item(s)</p>

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

    <tr>
        <th colspan="3"></th>
        <th class="right"><?= (int)$o->count ?></th>
        <th class="right"><?= number_format((float)$o->total, 2) ?></th>
        <th></th>
    </tr>
</table>

<p>
  <a href="history.php">Back to history</a>
</p>

<p>
  <a href="payment.php">Go to Payment</a>
</p>

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
