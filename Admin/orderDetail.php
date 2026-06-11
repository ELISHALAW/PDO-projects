<?php
include './headandfoot/head.php';

// Get the order_id from the URL
$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Initialize variables
$order = null;
$items = [];
$user = null;
$errors = [];

if ($order_id <= 0) {
    $errors[] = "Invalid order ID";
} else {
    // Fetch order details with user information
    $stmt = $_db->prepare("
        SELECT 
            o.order_id, o.date, o.count, o.total,
            u.user_id, u.name, u.username, u.email, u.phone_number
        FROM orders o
        LEFT JOIN user u ON o.user_id = u.user_id
        WHERE o.order_id = :order_id
    ");
    $stmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $errors[] = "Order not found";
    } else {
        // Fetch order items with product and category details
        $itemStmt = $_db->prepare("
            SELECT 
                oi.order_item_id, oi.price AS item_price, oi.unit, oi.subtotal,
                p.product_id, p.product_name, p.price AS product_price, p.quantity, p.detail,
                c.category
            FROM order_item oi
            LEFT JOIN product p ON oi.product_id = p.product_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE oi.order_id = :order_id
        ");
        $itemStmt->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        $itemStmt->execute();
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<div class="max-w-5xl mx-auto py-10 px-4">
    <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl shadow-xl border border-slate-700/60 overflow-hidden">
        <div class="bg-slate-800/60 px-8 py-6 border-b border-slate-700/60 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white tracking-wide">Order Invoice Receipt</h2>
                <p class="text-xs text-slate-400 mt-1">Detailed breakdowns for verification logs.</p>
            </div>
            <a href="orderlist.php" class="text-xs font-semibold text-slate-400 hover:text-white transition-colors">
                ← Return to List
            </a>
        </div>

        <div class="p-8">
            <?php if (!empty($errors)): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-sm font-medium"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-slate-900/40 p-6 rounded-xl border border-slate-700/40 shadow-inner space-y-3">
                        <h3 class="text-sm font-bold text-blue-400 uppercase tracking-wider border-b border-slate-700/40 pb-2 mb-2">Order Information</h3>
                        <div class="space-y-2 text-sm text-slate-300">
                            <div class="flex justify-between"><span class="text-slate-500">Order ID:</span> <span class="font-mono font-bold text-white">#<?= e($order['order_id']) ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Date Logged:</span> <span><?= e($order['date']) ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Cart Items:</span> <span class="font-mono"><?= e($order['count']) ?> units</span></div>
                            <div class="flex justify-between pt-2 border-t border-dashed border-slate-700/60"><span class="text-slate-500 font-semibold">Total Revenue:</span> <span class="font-bold font-mono text-emerald-400 text-base">RM <?= number_format($order['total'], 2) ?></span></div>
                        </div>
                    </div>

                    <div class="bg-slate-900/40 p-6 rounded-xl border border-slate-700/40 shadow-inner space-y-3">
                        <h3 class="text-sm font-bold text-purple-400 uppercase tracking-wider border-b border-slate-700/40 pb-2 mb-2">Buyer Account Details</h3>
                        <div class="space-y-2 text-sm text-slate-300">
                            <div class="flex justify-between"><span class="text-slate-500">Full Name:</span> <span class="font-medium text-white"><?= e($order['name'] ?? 'N/A') ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Email Address:</span> <span class="font-mono text-sm"><?= e($order['email'] ?? 'N/A') ?></span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Phone Contact:</span> <span class="font-mono"><?= e($order['phone_number'] ?? 'N/A') ?></span></div>
                        </div>
                    </div>
                </div>

                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Itemized Cart Contents</h3>
                <div class="border border-slate-700/60 rounded-xl overflow-hidden shadow-lg">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-800/60 text-slate-400 uppercase text-xs font-bold tracking-wider border-b border-slate-700/40">
                            <tr>
                                <th class="px-6 py-4">Product Descriptor</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4 text-right">Unit cost</th>
                                <th class="px-6 py-4 text-center w-24">Qty</th>
                                <th class="px-6 py-4 text-right w-36">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/40 text-slate-300 text-sm">
                            <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-700/10 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-200"><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 text-slate-400 text-xs"><span class="px-2 py-1 rounded bg-slate-800 border border-slate-700/50"><?= htmlspecialchars($item['category'] ?? 'N/A') ?></span></td>
                                <td class="px-6 py-4 text-right font-mono text-slate-400">RM <?= number_format($item['item_price'], 2) ?></td>
                                <td class="px-6 py-4 text-center font-mono text-slate-300"><?= htmlspecialchars($item['unit']) ?></td>
                                <td class="px-6 py-4 text-right font-bold text-white font-mono">RM <?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require './headandfoot/foot.php'; ?>