<?php
include '../head.php';

// ----------------------------------------------------------------------------
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='../login.php';</script>";
    exit();
}

// Handle POST requests
if (is_post()) {
    // Wishlist actions
    if (isset($_POST['wishlist_action'])) {
        $action = req('wishlist_action');
        $product_id = req('product_id');

        if ($action === 'add') {
            $error = add_to_wishlist($_SESSION['id'], $product_id);
            if ($error) {
                echo "<script>alert('$error');</script>";
            }
        } elseif ($action === 'remove') {
            $error = remove_from_wishlist($_SESSION['id'], $product_id);
            if ($error) {
                echo "<script>alert('$error');</script>";
            }
        }
        redirect(); // Make sure redirect() preserves query string (search + page)
    }

    // Cart actions
    $id   = req('id');
    $unit = req('unit');
    if ($id && $unit) {
        update_cart($id, $unit);
        redirect();
    }
}

// Handle search input
$search = req('search');

// Pagination setup
$itemsPerPage = 8;
$page = max(1, (int)req('page'));
$offset = ($page - 1) * $itemsPerPage;

// Total products
$countSql = 'SELECT COUNT(*) FROM product';
if ($search) {
    $countSql .= ' WHERE Product_name LIKE ' . $_db->quote('%' . $search . '%');
}
$totalProducts = $_db->query($countSql)->fetchColumn();
$totalPages = ceil($totalProducts / $itemsPerPage);

// Fetch products
$sql = 'SELECT * FROM product';
if ($search) {
    $sql .= ' WHERE Product_name LIKE ' . $_db->quote('%' . $search . '%');
}
$sql .= " ORDER BY product_id DESC LIMIT $itemsPerPage OFFSET $offset";
$arr = $_db->query($sql);

// ----------------------------------------------------------------------------
$_title = 'Product | List';
?>
<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gradient-to-b from-slate-100 via-slate-50 to-blue-50/40 min-h-screen pt-24 pb-16 px-4 sm:px-6 lg:px-8 antialiased selection:bg-blue-500 selection:text-white">

    <!-- Search Bar -->
    <div class="max-w-xl mx-auto mb-12">
        <form method="get" class="relative flex items-center bg-white border border-slate-200 rounded-full pl-5 pr-2 py-1.5 shadow-md shadow-slate-200/40 focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 transition-all duration-300">
            <span class="text-slate-400 text-lg mr-3 select-none">🔍</span>
            <input
                type="text"
                name="search"
                placeholder="Search products..."
                value="<?= e($search ?? '') ?>"
                autocomplete="off"
                class="w-full flex-1 bg-transparent border-0 outline-none text-slate-800 placeholder-slate-400 py-1.5 text-base focus:ring-0"
            >
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full transition-all duration-150 active:scale-95 cursor-pointer shadow-md shadow-blue-500/10"
            >
                Search
            </button>
        </form>
    </div>

    <!-- Products Grid -->
    <div id="products" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
        <?php foreach ($arr as $p): ?>
            <?php
            $id = $p->product_id;
            $in_wishlist = is_in_wishlist($_SESSION['id'], $id);
            ?>

            <div class="group bg-white rounded-2xl border border-slate-200/60 overflow-hidden flex flex-col shadow-xs hover:-translate-y-2 hover:shadow-xl hover:shadow-slate-300/40 transition-all duration-300 relative">

                <!-- Wishlist Button -->
                <form method="post" class="wishlist-form absolute top-3 right-3 z-10" data-product-id="<?= $id ?>">
                    <input type="hidden" name="product_id" value="<?= $id ?>">
                    <input type="hidden" name="wishlist_action" value="<?= $in_wishlist ? 'remove' : 'add' ?>">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full <?= $in_wishlist ? 'bg-pink-500 text-white' : 'bg-white/95 text-slate-600 hover:text-pink-600' ?> border <?= $in_wishlist ? 'border-pink-600' : 'border-slate-200' ?> shadow-lg hover:shadow-xl transition-all duration-200"
                        title="<?= $in_wishlist ? 'Remove from wishlist' : 'Add to wishlist' ?>"
                    >
                        <?= $in_wishlist ? '❤️' : '🤍' ?>
                    </button>
                </form>

                <a href="detail.php?id=<?= $p->product_id ?>" class="block w-full aspect-square overflow-hidden bg-slate-100 relative">
                    <img
                        src="/products/<?= e($p->image) ?>"
                        alt="<?= e($p->Product_name) ?>"
                        class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                    >
                </a>

                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <a href="detail.php?id=<?= $p->product_id ?>" class="block font-bold text-slate-800 text-lg hover:text-blue-600 transition duration-150 line-clamp-2 mb-1">
                            <?= e($p->Product_name) ?>
                        </a>
                        <div class="text-blue-600 font-extrabold text-xl tracking-tight">
                            RM <?= e($p->price) ?>
                        </div>
                    </div>

                    <div class="pt-1">
                        <?php if ($p->quantity <= 5): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                ⚠️ Low Stock: <?= e($p->quantity) ?> left
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                                Stock: <?= e($p->quantity) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination flex justify-center items-center gap-1.5 mt-16 flex-wrap">
            <?php if ($page > 1): ?>
                <a href="?page=1<?= $search ? '&search=' . urlencode($search) : '' ?>" class="px-3.5 py-2 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 font-medium text-sm transition duration-150 shadow-xs">« First</a>
                <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="px-3.5 py-2 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 font-medium text-sm transition duration-150 shadow-xs">« Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                   class="px-4 py-2 font-semibold text-sm rounded-xl border transition duration-150 shadow-xs <?= $i == $page ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/10' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="px-3.5 py-2 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 font-medium text-sm transition duration-150 shadow-xs">Next »</a>
                <a href="?page=<?= $totalPages ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="px-3.5 py-2 bg-white text-slate-600 rounded-xl border border-slate-200 hover:bg-slate-50 font-medium text-sm transition duration-150 shadow-xs">Last »</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Wishlist AJAX Handler -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.wishlist-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const button = this.querySelector('button');
            const originalHTML = button.innerHTML;
            const productId = this.dataset.productId;

            // Visual feedback
            button.style.opacity = '0.7';
            button.disabled = true;

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: new FormData(this)
                });

                if (response.ok) {
                    // Refresh the whole page (simplest & most reliable)
                    window.location.reload();
                } else {
                    alert('Failed to update wishlist');
                    button.innerHTML = originalHTML;
                }
            } catch (error) {
                console.error(error);
                alert('Network error');
                button.innerHTML = originalHTML;
            }
        });
    });
});
</script>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php include '../foot.php'; ?>