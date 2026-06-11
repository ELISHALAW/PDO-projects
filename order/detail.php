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
$stm->execute([$id, $_SESSION['id']]); 
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

<script src="https://cdn.tailwindcss.com"></script>

<div id="popup-background" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden cursor-pointer transition-opacity duration-300" onclick="hidePopup()"></div>
<img id="popup-image" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-w-[90%] max-h-[90%] z-50 hidden shadow-2xl rounded-xl border border-slate-200 bg-white p-1.5 cursor-pointer transition-all duration-300" onclick="hidePopup()" alt="Enlarged view">

<div class="bg-gradient-to-b from-slate-100 via-slate-50 to-blue-50/30 min-h-screen pt-28 pb-16 px-4 sm:px-6 lg:px-8 antialiased text-slate-800">
    <div class="max-w-5xl mx-auto">
        
        <div class="flex flex-col md:flex-row items-start justify-between gap-6 mb-8 bg-white border border-slate-200 rounded-2xl p-6 shadow-xl shadow-slate-100/50">
            <div class="space-y-2 w-full md:w-auto">
                <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-500 border border-slate-200/60 select-none">
                    Receipt Statement Record
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Order ID: <span class="text-blue-600 font-mono text-lg sm:text-xl font-bold ml-1"><?= htmlspecialchars($o->order_id) ?></span>
                </h1>
                <div class="text-sm font-medium text-slate-600 flex flex-wrap items-center gap-x-4 gap-y-1 pt-1">
                    <span class="flex items-center gap-1.5 text-slate-400">📅 <span class="text-slate-600 font-semibold"><?= htmlspecialchars($o->date) ?></span></span>
                    <span class="hidden sm:inline text-slate-200">|</span>
                    <span class="flex items-center gap-1.5 text-slate-400">📦 <span class="text-slate-600 font-semibold"><?= count($arr) ?> unique product item(s)</span></span>
                </div>
            </div>

            <div class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl p-4 min-w-[220px] shadow-lg shadow-blue-500/10 flex items-center justify-between md:justify-around gap-4 select-none">
                <div class="text-center">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-100/80">Total Items</div>
                    <div class="text-xl font-black tracking-tight mt-0.5"><?= (int)$o->count ?></div>
                </div>
                <div class="h-8 w-px bg-white/20"></div>
                <div class="text-right md:text-center">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-100/80">Grand Total</div>
                    <div class="text-xl font-black tracking-tight mt-0.5 text-amber-300">RM <?= number_format((float)$o->total, 2) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-100/40 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider select-none">
                            <th class="py-4 px-6 text-center w-24">Product ID</th>
                            <th class="py-4 px-6">Product Description Name</th>
                            <th class="py-4 px-6 text-right">Price</th>
                            <th class="py-4 px-6 text-center w-20">Unit</th>
                            <th class="py-4 px-6 text-right">Subtotal</th>
                            <th class="py-4 px-6 text-center w-28">Photo Ref</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($arr as $i): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors duration-100 group">
                                <td class="py-4 px-6 text-center font-mono text-xs font-bold text-slate-400">
                                    <?= (int)$i->product_id ?>
                                </td>
                                
                                <td class="py-4 px-6 font-semibold text-slate-900 text-sm max-w-[260px] truncate" title="<?= htmlspecialchars($i->Product_name) ?>">
                                    <?= htmlspecialchars($i->Product_name) ?>
                                </td>
                                
                                <td class="py-4 px-6 text-right font-medium text-slate-600 text-sm font-mono">
                                    RM <?= number_format((float)$i->price, 2) ?>
                                </td>
                                
                                <td class="py-4 px-6 text-center font-bold text-slate-700 text-sm">
                                    <?= (int)$i->unit ?>
                                </td>
                                
                                <td class="py-4 px-6 text-right font-bold text-slate-900 text-sm font-mono">
                                    RM <?= number_format((float)$i->subtotal, 2) ?>
                                </td>
                                
                                <td class="py-4 px-6 text-center">
                                    <?php if (!empty($i->image)): ?>
                                        <div class="inline-block w-16 h-11 rounded-lg bg-slate-50 border border-slate-200 overflow-hidden relative shadow-xs hover:shadow-md hover:border-blue-400 transition-all duration-200 group/img shrink-0 cursor-zoom-in">
                                            <img 
                                                src="/products/<?= htmlspecialchars($i->image) ?>" 
                                                class="absolute inset-0 w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-300" 
                                                onclick="showPopup(this.src)"
                                                alt="Thumb"
                                            >
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs font-bold text-slate-400 italic select-none">No Photo</span>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    
                    <tfoot>
                        <tr class="bg-slate-50/80 font-bold border-t border-slate-200/80">
                            <td colspan="3" class="py-4 px-6 text-slate-500 text-xs text-right uppercase tracking-wider select-none">Calculated Aggregations:</td>
                            <td class="py-4 px-6 text-center text-slate-900 text-sm bg-slate-100/40">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 bg-slate-200 text-slate-800 text-xs font-bold rounded">
                                    <?= (int)$o->count ?> Units
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right text-sm text-blue-600 font-black tracking-tight font-mono">
                                RM <?= number_format((float)$o->total, 2) ?>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl p-4 shadow-sm">
            <a class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors duration-150 group" href="history.php">
                <span class="mr-2 transform group-hover:-translate-x-1 transition-transform duration-150">←</span> 
                Back to Purchase History
            </a>
            
            <a class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl transition duration-150 active:scale-[0.99] cursor-pointer shadow-md shadow-emerald-500/10 text-sm tracking-wide" href="payment.php?id=<?= intval($id) ?>">
                <span>Proceed to Payment</span>
                <span>➔</span>
            </a>
        </div>

    </div>
</div>

<script>
    // Lightbox Modal Mechanism Event Handler Pipelines
    function showPopup(src) {
        const bg = document.getElementById('popup-background');
        const img = document.getElementById('popup-image');
        
        img.src = src;
        
        // Wipe styles cleanly to fallback straight to natural Tailwind state parameters
        bg.style.display = 'block';
        img.style.display = 'block';
    }

    function hidePopup() {
        document.getElementById('popup-background').style.display = 'none';
        document.getElementById('popup-image').style.display = 'none';
    }
</script>

<?php 
include '../foot.php'; 
?>