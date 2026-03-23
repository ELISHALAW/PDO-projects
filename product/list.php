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
    body {
        /* A very light, cool-toned gray prevents the "cheap" look of pure white */
        background-color: #f8fafc;

        /* Subtle gradient to add depth from top to bottom */
        background-image: linear-gradient(180deg, #f1f5f9 0%, #f8fafc 100%);
        background-attachment: fixed;

        /* Modern, clean typography */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: #1e293b;
        /* Dark slate instead of pure black for better readability */

        margin: 0;
        padding: 0;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Optional: Add a subtle pattern if the page feels too empty */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        /* Very faint dots pattern */
        background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
        background-size: 24px 24px;
        opacity: 0.4;
        z-index: -1;
        pointer-events: none;
    }

    :root {
        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --bg-light: #f8fafc;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --danger: #ef4444;
        --success: #22c55e;
    }

    .product-container {
        margin-top: 60px;
        padding: 40px 20px;
        background-color: var(--bg-light);
        min-height: 100vh;
        font-family: 'Inter', system-ui, sans-serif;
    }

    /* Modern Search Bar */
    /* Container for the search bar */
    /* Container to center and give space */
    .search-section {
        max-width: 650px;
        margin: 40px auto 50px auto;
        /* 40px top margin, 50px bottom */
        padding: 0 20px;
    }

    /* The "Pill" search bar */
    .search-input-wrapper {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 50px;
        padding: 5px 8px 5px 20px;
        /* Space inside the bar */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
    }

    .search-input-wrapper:focus-within {
        border-color: #2563eb;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.15);
    }

    /* Icon styling */
    .search-icon {
        margin-right: 12px;
        font-size: 16px;
        color: #94a3b8;
    }

    /* Input reset */
    .search-input-wrapper input {
        flex: 1;
        border: none !important;
        outline: none !important;
        padding: 10px 0;
        font-size: 16px;
        background: transparent;
    }

    /* Button integrated into the bar */
    .search-section button {
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.1s active;
    }

    .search-section button:hover {
        background: #1d4ed8;
    }

    .search-section button:active {
        transform: scale(0.96);
    }

    /* Improved Product Grid */
    #products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: auto;
    }

    /* Sleek Product Card */
    .product {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .product:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .image-container {
        position: relative;
        overflow: hidden;
        aspect-ratio: 1 / 1;
    }

    .product img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product:hover img {
        transform: scale(1.05);
    }

    /* Product Details */
    .product-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
        text-decoration: none;
        display: block;
    }

    .price-tag {
        font-size: 1.25rem;
        color: var(--primary);
        font-weight: 800;
        margin-bottom: 12px;
    }

    /* Stock Badges */
    .stock-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #f1f5f9;
        color: var(--text-muted);
    }

    .low-stock {
        background: #fee2e2;
        color: var(--danger);
    }

    /* Pagination */
    .pagination {
        margin-top: 60px;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .pagination a {
        padding: 10px 18px;
        background: #fff;
        color: var(--text-main);
        border-radius: 10px;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        font-weight: 500;
    }

    .pagination a.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .pagination a:hover:not(.active) {
        background: #f8fafc;
        border-color: #cbd5e1;
    }
</style>

<!-- Search form -->
<div class="product-container">
    <form method="get" class="search-section">
        <div class="search-input-wrapper">
            <span class="search-icon">🔍</span>
            <input type="text" name="search" placeholder="Search products..." value="<?= e($search ?? '') ?>" autocomplete="off">
            <button type="submit">Search</button>
        </div>
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