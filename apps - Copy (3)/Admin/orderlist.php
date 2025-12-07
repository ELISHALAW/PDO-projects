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

<style>
.order-table {
    width: 100%;
    max-width: 1200px;
    margin: 20px auto;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.order-table th,
.order-table td {
    padding: 12px 15px;
    text-align: left;
    border: 1px solid #ddd;
}

.order-table th {
    background-color: black;
    color: white;
    text-transform: uppercase;
    font-weight: bold;
}

.order-table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

.order-table tbody tr:hover {
    background-color: #f1f1f1;
    cursor: pointer;
}

.order-table td {
    color: #333;
}

.order-table tbody tr:last-child td {
    border-bottom: none;
}
</style>

<!-- Display the total number of orders -->

<table class="order-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Count</th>
            <th>Total</th>
            <th>Name</th>
            <th>Username</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($datas as $data): ?>
            <tr>
                <td><?php echo e($data['order_id']); ?></td>
                <td><?php echo e($data['date']); ?></td>
                <td><?php echo e($data['count']); ?></td>
                <td><?php echo e($data['total']); ?></td>
                <td><?php echo e($data['name']); ?></td>
                <td><?php echo e($data['username']); ?></td>
                <td><a href="orderDetail.php?id=<?php echo e($data['order_id']); ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination controls -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo ($page - 1); ?>">Previous</a>
    <?php endif; ?>
    
    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
    
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo ($page + 1); ?>">Next</a>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>
