<?php
include '../head.php';

// ----------------------------------------------------------------------------
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='../login.php';</script>";
    exit();
}

if (is_post()) {
    $id   = req('id');
    $unit = req('unit');
    update_cart($id, $unit);
    redirect();
}

// Handle search input
$search = req('search');

// Pagination setup
$itemsPerPage = 8; // how many products per page
$page = max(1, (int)req('page')); // current page, at least 1
$offset = ($page - 1) * $itemsPerPage;

// Total number of products
$countSql = 'SELECT COUNT(*) FROM product';
if ($search) {
    $countSql .= ' WHERE Product_name LIKE ' . $_db->quote('%' . $search . '%');
}
$totalProducts = $_db->query($countSql)->fetchColumn();
$totalPages = ceil($totalProducts / $itemsPerPage);

// Fetch products for this page
$sql = 'SELECT * FROM product';
if ($search) {
    $sql .= ' WHERE Product_name LIKE ' . $_db->quote('%' . $search . '%');
}
$sql .= " ORDER BY product_id DESC LIMIT $itemsPerPage OFFSET $offset";
$arr = $_db->query($sql);

// ----------------------------------------------------------------------------
$_title = 'Product | List';
?>

<style>
    .product-container {
        margin-top: 90px;
    }
    #products {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        text-align: center;
        justify-content: center;
        align-items: center;
    }
    .product {
        border: 5px solid #333;
        width: 200px;
        height: 250px;
        position: relative;
        background: #000;
        overflow: hidden;
        border-radius: 8px;
    }
    .product img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    .product img:hover {
        transform: scale(1.05);
    }
    .product-info {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        font-size: 14px;
        padding: 8px 5px;
        box-sizing: border-box;
        line-height: 1.4;
    }
    .product-name-price {
        margin-bottom: 5px;
        font-weight: bold;
    }
    .product-stock {
        font-size: 12px;
        color: #ccc;
    }
    .low-stock {
        color: #ff4d4d; /* Red color for low stock */
        font-weight: bold;
    }
    form[method="get"] {
        text-align: center;
        margin-bottom: 20px;
    }
    form[method="get"] input {
        padding: 5px;
        width: 200px;
    }
    form[method="get"] button {
        padding: 5px 10px;
    }
    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    .pagination a {
        margin: 0 5px;
        padding: 6px 12px;
        background: #007BFF;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }
    .pagination a.active {
        background: #0056b3;
        font-weight: bold;
    }
    .pagination a:hover {
        background: #0056b3;
    }
</style>

<!-- Search form -->
<div class="product-container">
<form method="get">
    <?= html_search('text', 'search', 'Search products...', e($search ?? '')) ?>
    <button type="submit">Search</button>
</form>

<!-- Product grid -->
<div id="products">
    <?php foreach ($arr as $p): ?>
        <?php
        $cart = get_cart();
        $id   = $p->product_id;
        ?>
        <div class="product">
            <form method="post">
                <?= html_hidden('product_id', $id) ?>
            </form>

            <a href="detail.php?id=<?= $p->product_id ?>">
                <img src="/products/<?= e($p->image) ?>" alt="<?= e($p->Product_name) ?>">
            </a>

            <div class="product-info">
                <div class="product-name-price">
                    <?= e($p->Product_name) ?> | RM <?= e($p->price) ?>
                </div>
                <div class="product-stock <?= $p->quantity <= 5 ? 'low-stock' : '' ?>">
                    Stock: <?= e($p->quantity) ?>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<!-- Pagination links -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=1<?= $search ? '&search=' . urlencode($search) : '' ?>">« First</a>
        <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">« Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Next »</a>
        <a href="?page=<?= $totalPages ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Last »</a>
    <?php endif; ?>
</div>

</div>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php include '../foot.php'; ?>