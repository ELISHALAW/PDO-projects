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

<div class="max-w-4xl mx-auto py-12 px-4">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800">Order Details</h2>
        </div>

        <div class="p-8">
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg mb-6">
                    <?php foreach ($errors as $error): ?>
                        <p class="text-sm font-medium"><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 gap-8 mb-10">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Order Information</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-bold text-gray-800">Order ID:</span> #<?= e($order['order_id']) ?></p>
                            <p><span class="font-bold text-gray-800">Date:</span> <?= e($order['date']) ?></p>
                            <p><span class="font-bold text-gray-800">Item Count:</span> <?= e($order['count']) ?></p>
                            <p><span class="font-bold text-gray-800">Total:</span> RM<?= number_format($order['total'], 2) ?></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">User Information</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-bold text-gray-800">Name:</span> <?= e($order['name'] ?? 'N/A') ?></p>
                            <p><span class="font-bold text-gray-800">Email:</span> <?= e($order['email'] ?? 'N/A') ?></p>
                            <p><span class="font-bold text-gray-800">Phone:</span> <?= e($order['phone_number'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-4">Order Items</h3>
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">Product</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Price</th>
                                <th class="px-6 py-4">Qty</th>
                                <th class="px-6 py-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($item['category'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 text-gray-600">RM<?= number_format($item['item_price'], 2) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($item['unit']) ?></td>
                                <td class="px-6 py-4 font-bold text-gray-900">RM<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="mt-8 text-center">
                <a href="orderlist.php" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">← Back to Orders</a>
            </div>
        </div>
    </div>
</div>

<?php require './headandfoot/foot.php'; ?>