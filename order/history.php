<?php
include '../head.php';

// ----------------------------------------------------------------------------

// (1) Authorization (member)
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='../login.php';</script>";
    exit();
}

// (2) Return orders belonging to the user (descending) with pagination
$perPage = 10; // change this to adjust items per page
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $perPage;

// total count
$stm = $_db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = :uid');
$stm->execute([':uid' => $_SESSION['id']]);
$total = (int)$stm->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

// fetch page
$stm = $_db->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY order_id DESC LIMIT :limit OFFSET :offset');
$stm->bindValue(':uid', $_SESSION['id'], PDO::PARAM_INT);
$stm->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stm->bindValue(':offset', $offset, PDO::PARAM_INT);
$stm->execute();
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Order | History';

?>
<style>
    /* History table container */
    .table {
        margin: 40px auto;
        width: 95%;
        max-width: 1100px;
        border-collapse: separate;
        border-spacing: 0;
        font-family: "Segoe UI", Roboto, Arial, sans-serif;
        font-size: 14px;
        box-shadow: 0 8px 20px rgba(2, 6, 23, 0.04);
        border-radius: 10px;
        overflow: hidden;
    }

    .table th,
    .table td {
        padding: 12px 16px;
        text-align: left;
        vertical-align: middle;
    }

    .table th {
        background: linear-gradient(90deg, #0ea5e9, #3b82f6);
        color: #fff;
        font-weight: 600;
        font-size: 13px;
    }

    .table td {
        background: #fff;
        border-bottom: 1px solid #eef3f8;
        color: #0f172a;
    }

    .table tr:nth-child(even) td {
        background: #fbfdff;
    }

    .table tr:hover td {
        background: #f7fbff;
    }

    .right {
        text-align: right;
    }

    .popup img {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 6px;
        border: 1px solid #e6eef8;
    }

    /* Buttons */
    button[data-post],
    button[data-get] {
        background: linear-gradient(90deg, #6d28d9, #8b5cf6);
        color: #fff;
        border: none;
        padding: 8px 14px;
        margin: 4px 6px 4px 0;
        cursor: pointer;
        border-radius: 8px;
        font-size: 14px;
        box-shadow: 0 6px 16px rgba(107, 33, 168, 0.12);
    }

    button[data-post]:hover,
    button[data-get]:hover {
        transform: translateY(-1px);
    }

    /* Link styled as button for reliable navigation (no JS required) */
    a.btn-detail {
        display: inline-block;
        background: linear-gradient(90deg, #6d28d9, #8b5cf6);
        color: #fff;
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        margin-right: 6px;
    }

    a.btn-detail:hover {
        transform: translateY(-1px);
    }

    p.records {
        width: 95%;
        max-width: 1100px;
        margin: 8px auto 0;
        color: #334155;
    }

    a.back-home {
        display: block;
        width: 95%;
        max-width: 1100px;
        margin: 12px auto;
        color: #6b21a8;
        text-decoration: none;
    }

    @media (max-width:700px) {

        .table th,
        .table td {
            padding: 10px;
            font-size: 13px;
        }

        .popup img {
            width: 44px;
            height: 44px;
        }
    }
</style>

<p>
    <button data-post="reset.php" data-confirm>Reset</button>
</p>

<?php /* Summary */ ?>
<p class="records"><?= $total ?> record(s) — page <?= $page ?> of <?= $totalPages ?></p>

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
                <a class="btn-detail" href="detail.php?id=<?= $o->order_id ?>&p=<?= $page ?>">Detail</a>
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

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div style="width:95%;max-width:1100px;margin:12px auto 0;display:flex;gap:8px;flex-wrap:wrap;">
        <?php if ($page > 1): ?>
            <a class="btn-detail" href="history.php?p=<?= $page-1 ?>">← Prev</a>
        <?php endif ?>

        <?php
        // show a window of pages
        $start = max(1, $page - 3);
        $end = min($totalPages, $page + 3);
        for ($i = $start; $i <= $end; $i++):
        ?>
            <?php if ($i == $page): ?>
                <span style="display:inline-block;padding:8px 12px;background:#eef6ff;border-radius:6px;font-weight:700;"><?= $i ?></span>
            <?php else: ?>
                <a class="btn-detail" href="history.php?p=<?= $i ?>"><?= $i ?></a>
            <?php endif ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a class="btn-detail" href="history.php?p=<?= $page+1 ?>">Next →</a>
        <?php endif ?>
    </div>
<?php endif; ?>

<a class="back-home" href="../index.php">← Back to Homepage</a>

<?php
include '../foot.php';
