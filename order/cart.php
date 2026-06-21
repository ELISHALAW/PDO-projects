<?php
include '../head.php';

$_units = [];

if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='login.php';</script>";
    exit();
}

// Handle POST Requests
if (is_post()) {
    if (isset($_POST['btn']) && req('btn') === 'clear') {
        set_cart();
        redirect('?');
    }

    if (isset($_POST['remove_id'])) {
        $remove_id = req('remove_id');
        remove_from_cart($remove_id);
        redirect('?');
    }

    if (isset($_POST['id']) && isset($_POST['unit'])) {
        $id = req('id');
        $unit = (int)req('unit');
        update_cart($id, $unit);
        redirect('?');
    }
}

$_title = 'Order | Shopping Cart';
$cart = get_cart();
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-gradient-to-b from-slate-100 via-slate-50 to-blue-50/30 min-h-screen pt-28 pb-16 px-4 sm:px-6 lg:px-8 antialiased text-slate-800">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 border-b border-slate-200/60 pb-5">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Your Shopping Cart</h1>
                <p class="text-sm text-slate-500 mt-1">Review your selections and proceed to secure checkout.</p>
            </div>
            
            <?php if (!empty($cart)): ?>
                <form method="post" action="" onsubmit="return confirm('Are you sure you want to clear your entire cart?');">
                    <button type="submit" name="btn" value="clear" class="inline-flex items-center text-xs font-bold text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/40 px-3 py-1.5 rounded-lg transition duration-150 cursor-pointer">
                        🗑️ Clear Cart
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($cart)): ?>
            <div class="bg-white border border-slate-200/70 rounded-2xl p-12 text-center shadow-xl shadow-slate-100/50 max-w-md mx-auto my-12">
                <span class="text-5xl block mb-4 animate-bounce">🛒</span>
                <h3 class="text-xl font-bold text-slate-800 mb-1">Your cart is empty</h3>
                <p class="text-sm text-slate-400 mb-6">Looks like you haven't added anything to your cart yet.</p>
                <a href="../product/list.php" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition duration-150 shadow-md shadow-blue-500/10">
                    Explore Products
                </a>
            </div>
        <?php else: ?>
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-100/40 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider select-none">
                                <th class="py-4 px-6 text-center w-16">ID</th>
                                <th class="py-4 px-6">Product</th>
                                <th class="py-4 px-6 text-right">Price</th>
                                <th class="py-4 px-6 text-center w-24">Qty</th>
                                <th class="py-4 px-6 text-center w-28">Action</th>
                                <th class="py-4 px-6 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                            $count = 0;
                            $total = 0;

                            $stm = $_db->prepare('SELECT * FROM product WHERE product_id = ?');

                            foreach ($cart as $id => $unit):
                                $stm->execute([$id]);
                                $p = $stm->fetch(PDO::FETCH_OBJ); // Mapping as Object for arrow syntax consistency

                                if (!$p) continue;

                                $subtotal = $p->price * $unit;
                                $count += $unit;
                                $total += $subtotal;
                            ?>
                                <tr class="hover:bg-slate-50/60 transition-colors duration-100 group">
                                    <td class="py-4 px-6 text-center font-mono text-xs font-bold text-slate-400">
                                        <?= htmlspecialchars($p->product_id) ?>
                                    </td>
                                    
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 overflow-hidden relative shadow-xs shrink-0">
                                                <img src="/products/<?= htmlspecialchars($p->image) ?>" alt="<?= htmlspecialchars($p->Product_name) ?>" class="absolute inset-0 w-full h-full object-cover">
                                            </div>
                                            <div class="font-semibold text-slate-900 text-sm max-w-[240px] truncate" title="<?= htmlspecialchars($p->Product_name) ?>">
                                                <?= htmlspecialchars($p->Product_name) ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-right font-medium text-slate-600 text-sm">
                                        RM <?= number_format($p->price, 2) ?>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center">
                                        <form method="post" action="" class="inline-block">
                                            <input type="hidden" name="id" value="<?= $p->product_id ?>">
                                            <input 
                                                type="number" 
                                                name="unit" 
                                                min="1" 
                                                value="<?= $unit ?>" 
                                                onchange="this.form.submit()"
                                                class="w-16 px-2 py-1.5 text-center font-semibold text-slate-800 border border-slate-200 rounded-lg bg-white shadow-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition text-sm"
                                            >
                                        </form>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-center">
                                        <form method="post" action="" onsubmit="return confirm('Are you sure you want to remove this product?');" class="inline-block">
                                            <input type="hidden" name="remove_id" value="<?= $p->product_id ?>">
                                            <button type="submit" class="text-xs font-bold text-rose-600 hover:text-white border border-rose-100 hover:border-rose-600 bg-rose-50/40 hover:bg-rose-600 px-2.5 py-1.5 rounded-lg transition duration-150 cursor-pointer">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                    
                                    <td class="py-4 px-6 text-right font-bold text-slate-900 text-sm">
                                        RM <?= number_format($subtotal, 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        
                        <tfoot>
                            <tr class="bg-slate-50/80 font-bold border-t border-slate-200">
                                <td colspan="3" class="py-4 px-6 text-slate-500 text-sm text-right uppercase tracking-wider select-none">Summary Totals:</td>
                                <td class="py-4 px-6 text-center text-slate-900 text-sm bg-slate-100/50">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 bg-slate-200 text-slate-800 text-xs font-bold rounded-md">
                                        <?= $count ?> Units
                                    </span>
                                </td>
                                <td colspan="2" class="py-4 px-6 text-right text-base text-blue-600 font-black tracking-tight">
                                    RM <?= number_format($total, 2) ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
                <a class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors duration-150 group" href="../product/list.php">
                    <span class="mr-2 transform group-hover:-translate-x-1 transition-transform duration-150">←</span> 
                    Continue Shopping
                </a>
                
                <form method="post" action="checkout.php" class="w-full sm:w-auto">
                    <input type="hidden" name="checkout" value="1">
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl transition duration-150 active:scale-[0.99] cursor-pointer shadow-md shadow-emerald-500/10 text-sm tracking-wide">
                        <span>Checkout Securely</span>
                        <span>➔</span>
                    </button>
                </form>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php
include '../foot.php';


?>