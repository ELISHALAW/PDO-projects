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

<div class="max-w-6xl mx-auto py-2 px-4">
    <div class="mb-3">
        <h1 class="text-2xl font-bold text-gray-800">Order Management</h1>
        <p class="text-gray-500 text-sm">Overview of all platform transactions.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($datas)): ?>
                        <?php foreach ($datas as $data): ?>
                        <tr class="hover:bg-gray-50 transition-colors text-sm">
                            <td class="px-6 py-4 font-mono font-medium text-gray-800">#<?= e($data['order_id']) ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= e($data['date']) ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= e($data['count']) ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-900">$<?= number_format($data['total'], 2) ?></td>
                            <td class="px-6 py-4 text-gray-700"><?= e($data['name']) ?></td>
                            <td class="px-6 py-4 text-gray-500">@<?= e($data['username']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <a href="orderDetail.php?id=<?= e($data['order_id']) ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs uppercase tracking-wide">
                                   View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <div class="text-sm text-gray-600">
                Page <span class="font-bold"><?= $page ?></span> of <?= $totalPages ?>
            </div>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= ($page - 1) ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition-all">Previous</a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= ($page + 1) ?>" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800 transition-all">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>