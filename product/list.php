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
        margin-top: 100px;
        padding: 0 20px;
    }

    /* Search */
    form[method="get"] {
        text-align: center;
        margin-bottom: 30px;
    }

    form[method="get"] input {
        padding: 8px 12px;
        width: 240px;
        border-radius: 6px;
        border: 1px solid #ccc;
        outline: none;
    }

    form[method="get"] button {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        background: #007BFF;
        color: #fff;
        cursor: pointer;
    }

    form[method="get"] button:hover {
        background: #0056b3;
    }

    /* Product grid */
    #products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        max-width: 900px;
        margin: auto;
    }

    /* Product card */
    .product {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .product:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }

    /* Product image */
    .product img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    /* Product info */
    .product-info {
        padding: 12px;
        background: #fff;
        color: #333;
        text-align: left;
    }

    .product-name-price {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    /* Stock display */
    .product-stock {
        font-size: 13px;
        color: #666;
    }

    .low-stock {
        color: #d93025;
        font-weight: 600;
    }

    /* Pagination */
    .pagination {
        margin: 35px 0;
        text-align: center;
    }

    .pagination a {
        display: inline-block;
        margin: 0 4px;
        padding: 7px 14px;
        background: #f1f1f1;
        color: #333;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
    }

    .pagination a.active {
        background: #007BFF;
        color: #fff;
        font-weight: 600;
    }

    .pagination a:hover {
        background: #007BFF;
        color: #fff;
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