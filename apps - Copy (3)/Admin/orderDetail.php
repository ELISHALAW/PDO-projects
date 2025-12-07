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
            u.user_id, u.name, u.username, u.email
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .order-info, .user-info {
            margin-bottom: 20px;
        }

        .order-info h3, .user-info h3 {
            color: black;
            margin-bottom: 10px;
        }

        .order-info p, .user-info p {
            margin: 5px 0;
            color: #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th, .items-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .items-table th {
            background-color: black;
            color: white;
            text-transform: uppercase;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Details</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Order Information -->
            <div class="order-info">
                <h3>Order Information</h3>
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($order['date']); ?></p>
                <p><strong>Item Count:</strong> <?php echo htmlspecialchars($order['count']); ?></p>
                <p><strong>Total:</strong>RM<?php echo number_format($order['total'], 2); ?></p>
            </div>

            <!-- User Information -->
            <div class="user-info">
                <h3>User Information</h3>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name'] ?? 'N/A'); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
            </div>

            <!-- Order Items -->
            <h3>Order Items</h3>
            <?php if (empty($items)): ?>
                <p>No items found for this order.</p>
            <?php else: ?>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['order_item_id']); ?></td>
                                <td><?php echo htmlspecialchars($item['product_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                                <td>RM<?php echo number_format($item['item_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($item['unit']); ?></td>
                                <td>RM<?php echo number_format($item['subtotal'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <a href="orderlist.php" class="back-link">Back to Homepage</a>
    </div>
</body>
</html>


<?php include './headandfoot/foot.php'; ?>
