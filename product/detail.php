<?php
include '../_base.php';

$_title = 'Product | Detail';

// Handle form submission to update cart
if (is_post()) {
    $id   = req('id');
    $unit = req('unit');
    update_cart($id, $unit);
    redirect(); // refresh page
}

// Handle wishlist actions
if (isset($_POST['wishlist_action'])) {
    $action = req('wishlist_action');
    $product_id = req('product_id');
    
    if (!isset($_SESSION['id'])) {
        echo "<script>alert('Please log in first'); window.location.href='../login.php';</script>";
        exit();
    }
    
    if ($action === 'add') {
        $error = add_to_wishlist($_SESSION['id'], $product_id);
        if ($error) {
            echo "<script>alert('$error');</script>";
        } else {
            echo "<script>alert('Added to wishlist!');</script>";
        }
    } elseif ($action === 'remove') {
        $error = remove_from_wishlist($_SESSION['id'], $product_id);
        if ($error) {
            echo "<script>alert('$error');</script>";
        } else {
            echo "<script>alert('Removed from wishlist!');</script>";
        }
    }
    redirect(); // refresh page
}

// Get product ID from request
$id = req('id');

if (!$id) {
    echo "<p style='color:red'>❌ Product ID is missing from the request.</p>";
    include '../foot.php';
    exit;
}

// Fetch product from database
$stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');
$stm->execute([$id]);
$p = $stm->fetch(PDO::FETCH_OBJ);

if (!$p) {
    echo "<p style='color:red'>❌ Product not found in the database.</p>";
    include '../foot.php';
    exit;
}
?>

<!-- CSS Styling -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-4xl mx-auto my-12 px-4 sm:px-6 antialiased text-slate-800">
    
    <div class="mb-6">
        <a href="list.php" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors duration-200 group">
            <span class="mr-2 transform group-hover:-translate-x-1 transition-transform duration-200">←</span> 
            Back to Product List
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-100/50 overflow-hidden grid grid-cols-1 md:grid-cols-2 gap-8 p-6 sm:p-8">
        
        <div class="w-full aspect-square rounded-xl overflow-hidden bg-slate-50 border border-slate-100 relative group">
            <img 
                src="../products/<?= e($p->image) ?>" 
                alt="<?= e($p->Product_name) ?>" 
                class="absolute inset-0 w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
            >
        </div>

        <div class="flex flex-col justify-between space-y-6">
            
            <div class="space-y-4">
                <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-500 border border-slate-200/60 select-none">
                    ID: <?= e($p->product_id) ?>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    <?= e($p->Product_name) ?>
                </h1>

                <div class="flex flex-wrap items-center gap-3 pt-1">
                    <div class="text-2xl font-black text-blue-600 tracking-tight">
                        RM <?= number_format($p->price, 2) ?>
                    </div>
                    
                    <div>
                        <?php
                        $stock = (int)$p->quantity;
                        if ($stock > 10) {
                            echo "<span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shadow-xs'>● In stock ($stock)</span>";
                        } elseif ($stock > 0) {
                            echo "<span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60 shadow-xs'>⚠️ Low stock ($stock left)</span>";
                        } else {
                            echo "<span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60 shadow-xs'>✕ Out of stock</span>";
                        }
                        ?>
                    </div>
                </div>

                <hr class="border-slate-100 my-4">

                <div class="space-y-1.5">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider select-none">Details</h4>
                    <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                        <?= nl2br(e($p->detail)) ?>
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 mt-auto">
                <?php
                $cart = get_cart();
                $unit = $cart[$p->product_id] ?? 0;
                $in_wishlist = isset($_SESSION['id']) && is_in_wishlist($_SESSION['id'], $p->product_id);
                ?>
                <form method="post" class="flex flex-wrap items-center gap-3">
                    <?= html_hidden('id', $p->product_id) ?>
                    
                    <div class="flex items-center space-x-2">
                        <label for="unit" class="text-xs font-bold text-slate-500 uppercase tracking-wider select-none">Qty:</label>
                        <div class="[&>input]:w-16 [&>input]:px-2.5 [&>input]:py-1.5 [&>input]:text-center [&>input]:font-semibold [&>input]:text-slate-800 [&>input]:border [&>input]:border-slate-200 [&>input]:rounded-lg [&>input]:bg-white [&>input]:shadow-xs [&>input]:focus:ring-2 [&>input]:focus:ring-blue-500/20 [&>input]:focus:border-blue-500 [&>input]:outline-none">
                            <?= inputNumber('number', 'unit', 1, $stock, e($unit)) ?>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="flex-1 min-w-[140px] bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg transition duration-150 active:scale-[0.98] cursor-pointer shadow-md shadow-blue-500/10 flex items-center justify-center space-x-1.5 text-sm"
                        <?= $stock <= 0 ? 'disabled class="opacity-50 cursor-not-allowed bg-slate-300 shadow-none"' : '' ?>
                    >
                        <span>🛒</span>
                        <span>Add to Cart</span>
                    </button>
                    
                    <?php if ($unit): ?>
                        <span class="inline-flex items-center justify-center bg-emerald-100 text-emerald-800 font-bold h-8 w-8 rounded-lg text-sm select-none" title="Item currently active in your shopping cart">
                            ✅
                        </span>
                    <?php endif; ?>
                </form>
                
                <!-- Wishlist Button -->
                <?php if (isset($_SESSION['id'])): ?>
                <form method="post" class="mt-3">
                    <input type="hidden" name="product_id" value="<?= $p->product_id ?>">
                    <button 
                        type="submit" 
                        name="wishlist_action" 
                        value="<?= $in_wishlist ? 'remove' : 'add' ?>"
                        class="w-full <?= $in_wishlist ? 'bg-pink-100 hover:bg-pink-200 text-pink-700 border border-pink-300' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' ?> font-semibold px-4 py-2 rounded-lg transition duration-150 active:scale-[0.98] cursor-pointer flex items-center justify-center space-x-2 text-sm"
                    >
                        <span><?= $in_wishlist ? '❤️' : '🤍' ?></span>
                        <span><?= $in_wishlist ? 'In Wishlist' : 'Add to Wishlist' ?></span>
                    </button>
                </form>
                <?php else: ?>
                <div class="mt-3">
                    <a 
                        href="../login.php" 
                        class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-lg transition duration-150 active:scale-[0.98] cursor-pointer flex items-center justify-center space-x-2 text-sm"
                    >
                        <span>🤍</span>
                        <span>Login to Add Wishlist</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<script>
    // Submit form on select change if any (from inputNumber structures)
    $('select').on('change', e => e.target.form.submit());
</script>