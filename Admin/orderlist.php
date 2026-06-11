<?php require __DIR__ . '/headandFoot/head.php'; ?>

<?php
// Set the number of orders per page
$ordersPerPage = 8;

// Get the current page from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the query
$offset = ($page - 1) * $ordersPerPage;

// Fetch order and user data with pagination
$stmt = $_db->prepare("SELECT 
    o.order_id, o.date, o.count, o.total,
    u.user_id, u.name, u.username
FROM orders o
LEFT JOIN user u ON o.user_id = u.user_id
LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $ordersPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$datas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of orders
$totalStmt = $_db->prepare("SELECT COUNT(*) AS total_orders FROM orders");
$totalStmt->execute();
$totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalOrders = $totalResult['total_orders'];

// Calculate the total number of pages
$totalPages = ceil($totalOrders / $ordersPerPage);
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white tracking-wide">Order Management</h1>
        <p class="text-slate-400 text-sm mt-0.5">Overview of all platform sales metrics and transactions.</p>
    </div>

    <div class="bg-slate-800/40 backdrop-blur-md rounded-xl border border-slate-700/60 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/60 text-slate-400 uppercase text-xs tracking-wider font-semibold border-b border-slate-700/40">
                    <tr>
                        <th class="px-6 py-4 w-28">Order ID</th>
                        <th class="px-6 py-4">Transaction Date</th>
                        <th class="px-6 py-4 text-center w-24">Items</th>
                        <th class="px-6 py-4">Total Price</th>
                        <th class="px-6 py-4">Customer Name</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4 text-center">Operation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 text-slate-300">
                    <?php if (!empty($datas)): ?>
                        <?php foreach ($datas as $data): ?>
                        <tr class="hover:bg-slate-700/20 transition-colors text-sm group">
                            <td class="px-6 py-4 font-mono text-xs text-blue-400 font-bold">#<?= e($data['order_id']) ?></td>
                            <td class="px-6 py-4 text-slate-300"><?= e($data['date']) ?></td>
                            <td class="px-6 py-4 text-center font-mono text-slate-400 group-hover:text-slate-200 transition-colors"><?= e($data['count']) ?></td>
                            <td class="px-6 py-4 font-bold text-emerald-400 font-mono">RM <?= number_format($data['total'], 2) ?></td>
                            <td class="px-6 py-4 font-medium text-slate-200 group-hover:text-white transition-colors"><?= e($data['name']) ?></td>
                            <td class="px-6 py-4 text-slate-400">@<?= e($data['username']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="orderDetail.php?id=<?= e($data['order_id']) ?>" 
                                   class="inline-flex items-center px-4 py-1.5 bg-slate-700 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="px-6 py-16 text-center text-slate-500 italic bg-slate-800/10">No records found within active transaction histories.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-slate-700/40 flex items-center justify-between bg-slate-800/40">
            <div class="text-xs font-medium text-slate-400">
                Displaying Page <span class="text-white font-bold font-mono"><?= $page ?></span> of <?= $totalPages ?>
            </div>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= ($page - 1) ?>" class="px-3.5 py-1.5 bg-slate-800 text-slate-300 border border-slate-700/60 rounded-lg text-xs font-semibold hover:bg-slate-700 transition-all">Previous</a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= ($page + 1) ?>" class="px-3.5 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-500 transition-all shadow-md shadow-blue-600/10">Next Page</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>