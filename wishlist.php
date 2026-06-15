<?php
include 'head.php';

// (1) Authorization
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='login.php';</script>";
    exit();
}

// (2) Handle POST Requests
if (is_post()) {
    // Clear entire wishlist
    if (isset($_POST['btn']) && req('btn') === 'clear') {
        $_db->prepare("DELETE FROM wishlist WHERE user_id = ?")->execute([$_SESSION['id']]);
        redirect('?');
    }

    // Remove single item
    if (isset($_POST['remove_id'])) {
        $remove_id = req('remove_id');
        $error = remove_from_wishlist($_SESSION['id'], $remove_id);
        if ($error) {
            echo "<script>alert('$error');</script>";
        }
        redirect('?');
    }

    // Add to cart
    if (isset($_POST['add_to_cart'])) {
        $product_id = req('add_to_cart');
        
        $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
        $stm->execute([$product_id]);
        $product = $stm->fetch(PDO::FETCH_OBJ);
        
        if ($product) {
            $cart = get_cart();
            $cart[$product_id] = ($cart[$product_id] ?? 0) + 1;
            set_cart($cart);
            
            // Optional: remove from wishlist after adding to cart
            // remove_from_wishlist($_SESSION['id'], $product_id);
            
            redirect('/order/cart.php');
        }
    }
}

// (3) Get wishlist items
$wishlist_items = get_wishlist($_SESSION['id']);
$total_items = count($wishlist_items);

$_title = 'Wishlist';
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gradient-to-b from-slate-100 via-slate-50 to-blue-50/30 min-h-screen pt-28 pb-16 px-4 sm:px-6 lg:px-8 antialiased text-slate-800">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-slate-200/60 pb-5">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-600">Personal Collection</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Your Wishlist</h1>
                <p class="mt-3 max-w-2xl text-sm text-slate-500">Save products for later and manage your favorite items.</p>
            </div>
            
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                    ❤️ <?= $total_items ?> item<?= $total_items !== 1 ? 's' : '' ?>
                </div>
                
                <?php if (!empty($wishlist_items)): ?>
                    <form method="post" action="" onsubmit="return confirm('Are you sure you want to clear your entire wishlist?');">
                        <button type="submit" name="btn" value="clear" 
                                class="inline-flex items-center justify-center rounded-3xl bg-gradient-to-r from-rose-500 to-pink-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-200/30 transition duration-200 hover:-translate-y-0.5 hover:shadow-rose-300/40">
                            🗑️ Clear Wishlist
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Empty State -->
        <?php if (empty($wishlist_items)): ?>
            <div class="bg-white border border-slate-200/70 rounded-2xl p-16 text-center shadow-xl shadow-slate-100/50 max-w-2xl mx-auto my-12">
                <span class="text-6xl block mb-4">💔</span>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">Your wishlist is empty</h3>
                <p class="text-slate-500 mb-8">Start adding your favorite products to your wishlist!</p>
                <a href="/product/list.php" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-8 py-3 rounded-xl transition duration-150 shadow-md shadow-indigo-500/20">
                    Browse Products
                </a>
            </div>
        <?php else: ?>
            <!-- Wishlist Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="wishlist-grid">
                <?php foreach ($wishlist_items as $item): ?>
                    <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden hover:-translate-y-1" id="item-<?= $item->product_id ?>">
                        
                        <!-- Product Image -->
                        <div class="relative h-56 bg-gradient-to-br from-slate-50 to-slate-100 overflow-hidden">
                            <img 
                                src="/products/<?= e($item->image) ?>" 
                                alt="<?= e($item->Product_name) ?>" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            >
                            
                            <!-- Remove Button -->
                            <form method="post" class="wishlist-remove-form absolute top-3 right-3" data-id="<?= $item->product_id ?>">
                                <input type="hidden" name="remove_id" value="<?= $item->product_id ?>">
                                <button type="submit" 
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/95 hover:bg-red-500 text-red-500 hover:text-white border border-red-200 shadow-lg transition-all duration-200">
                                    ❤️
                                </button>
                            </form>
                            
                            <!-- Category Badge -->
                            <div class="absolute top-3 left-3 bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                <?= htmlspecialchars(strtoupper(substr($item->Product_name, 0, 3))) ?>
                            </div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4 flex flex-col h-full">
                            <h3 class="font-bold text-slate-900 text-sm mb-1 line-clamp-2 group-hover:text-indigo-600 transition">
                                <?= e($item->Product_name) ?>
                            </h3>
                            
                            <div class="mb-3 flex items-baseline gap-2">
                                <span class="text-lg font-bold text-slate-900">RM <?= number_format($item->price, 2) ?></span>
                                <span class="text-xs text-slate-500"><?= $item->quantity > 0 ? 'In Stock' : 'Out of Stock' ?></span>
                            </div>
                            
                            <p class="text-xs text-slate-400 mb-4">Added <?= date('M d, Y', strtotime($item->added_at)) ?></p>
                            
                            <!-- Actions -->
                            <div class="mt-auto flex gap-2">
                                <form method="post" action="" class="flex-1">
                                    <input type="hidden" name="add_to_cart" value="<?= $item->product_id ?>">
                                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm py-2 px-3 rounded-lg transition duration-150">
                                        🛒 Add to Cart
                                    </button>
                                </form>
                                <a href="/product/detail.php?id=<?= $item->product_id ?>" 
                                   class="flex-1 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-sm py-2 px-3 rounded-lg transition duration-150 text-center">
                                    👁️ View
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="mt-12 bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Ready to shop?</h3>
                        <p class="text-sm text-slate-500">Add items from your wishlist to your cart and proceed to checkout.</p>
                    </div>
                    <a href="/order/cart.php" class="inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold text-sm px-6 py-3 rounded-xl transition duration-200 shadow-md shadow-orange-500/20">
                        🛒 Go to Cart
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- AJAX for Remove Button -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.wishlist-remove-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const button = this.querySelector('button');
            const originalHTML = button.innerHTML;
            const productId = this.dataset.id;
            const card = document.getElementById('item-' + productId);

            // Visual feedback
            button.style.opacity = '0.6';
            button.disabled = true;

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: new FormData(this)
                });

                if (response.ok) {
                    // Remove card with animation
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        // Update count
                        const countEl = document.querySelector('.rounded-3xl.border');
                        if (countEl) {
                            let count = parseInt(countEl.textContent.match(/\d+/)[0]);
                            count--;
                            countEl.innerHTML = `❤️ ${count} item${count !== 1 ? 's' : ''}`;
                            
                            if (count === 0) {
                                location.reload(); // Show empty state
                            }
                        }
                    }, 400);
                } else {
                    alert('Failed to remove item');
                    button.innerHTML = originalHTML;
                }
            } catch (error) {
                console.error(error);
                alert('Network error. Please try again.');
                button.innerHTML = originalHTML;
            }
        });
    });
});
</script>

<?php include 'foot.php'; ?>