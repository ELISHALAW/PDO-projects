<?php
include '../head.php';

// ----------------------------------------------------------------------------

// (1) Authorization (member)
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='../login.php';</script>";
    exit();
}

// (2) Return orders belonging to the user (descending)
$stm = $_db->prepare('
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY order_id DESC
');
$stm->execute([$_SESSION['id']]);
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Order | History';

?>
<style>
    table {
        margin-top: 50px;
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    th, td {
        padding: 12px 15px;
        border: 1px solid #ccc;
        text-align: left;
    }

    th {
        background-color:rgb(0, 0, 0);
        color: white;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2; /* Light grey for even rows */
    }

    tr:nth-child(odd) {
        background-color: #ffffff; /* White for odd rows */
    }

    .right {
        text-align: right;
    }

    .popup img {
        width: 50px;
        height: 50px;
        outline: 1px solid #666;
    }

    button {
        background-color:rgb(0, 0, 0);
        color: white;
        border: none;
        padding: 8px 16px;
        margin: 4px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
    }

    button:hover {
        background-color:rgb(0, 0, 0);
    }
</style>

<p>
    <button data-post="reset.php" data-confirm>Reset</button>
</p>

<p><?= count($arr) ?> record(s)</p>

<table class="table">
    <tr>
        <th>Id</th>
        <th>Datetime</th>
        <th>Count</th>
        <th>Total (RM)</th>
        <th></th>
    </tr>

    <?php foreach ($arr as $o): ?>
    <tr>
        <td><?= $o->order_id ?></td>
        <td><?= $o->date ?></td>
        <td class="right"><?= $o->count ?></td>
        <td class="right"><?= number_format((float)$o->total, 2) ?></td>
        <td>
            <button data-get="detail.php?id=<?= $o->order_id ?>">Detail</button>
            <!-- (A) EXTRA: Product photos -->
            <div class="popup">
                <?php
                    $stm = $_db->prepare('
                        SELECT p.image 
                        FROM order_item AS i, product AS p
                        WHERE i.product_id = p.product_id
                        AND i.order_id = ?
                    ');
                    $stm->execute([$o->order_id]);
                    $photos = $stm->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($photos as $photo) {
                        echo "<img src='/products/$photo'>";
                    }
                ?>
            </div>
        </td>
    </tr>
    <?php endforeach ?>
</table> 

        </div>
            <a href="../index.php">Back to Homepage</a>
         </div>

<?php
include '../foot.php';