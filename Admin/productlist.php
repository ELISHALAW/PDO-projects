<?php require __DIR__ . '/headandFoot/head.php'; ?>

<style>
    .container {
        max-width: 1000px;
        margin: auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #000;
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 8px;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .header-container form {
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }

    .header-container input[type="search"] {
        background-color: #222;
        color: #fff;
        border: 1px solid #007BFF;
        padding: 6px 10px;
        border-radius: 5px 0 0 5px;
        outline: none;
    }

    .header-container button.searchButton {
        padding: 6px 12px;
        border: none;
        background-color: #007BFF;
        color: #fff;
        border-radius: 0 5px 5px 0;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .header-container button.searchButton:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        color: #fff;
    }

    th {
        background-color: #333;
    }

    img {
        border-radius: 8px;
        transition: transform 0.3s ease;
    }

    img:hover {
        transform: scale(1.1);
    }

    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a {
        color: white;
        padding: 8px 12px;
        margin: 0 3px;
        background-color: #007BFF;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .pagination a:hover {
        background-color: #0056b3;
    }

    .pagination .active {
        background-color: #0056b3;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            gap: 10px;
        }

        table, th, td {
            font-size: 14px;
        }
    }

    table {
        text-align: center;
    }

    .delete-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<?php
// Handle search query
$totalStmt = $_db->prepare("SELECT COUNT(*) AS total_products FROM product");
$totalStmt->execute();
$result = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalProducts = $result['total_products'];

$searchQuery = isset($_GET['q']) && $_GET['q'] !== '' ? '%' . $_GET['q'] . '%' : '%';

// Pagination logic
$itemsPerPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $itemsPerPage;

// Calculate starting number for the current page
$num = ($page - 1) * $itemsPerPage + 1;

// Modify query to include search filter
$sql = "SELECT COUNT(*) FROM product WHERE Product_name LIKE :searchQuery";
$totalStmt = $_db->prepare($sql);
$totalStmt->bindValue(':searchQuery', $searchQuery, PDO::PARAM_STR);
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $itemsPerPage);

// Query to fetch filtered products
$stmt = $_db->prepare("SELECT product.*, category.category 
    FROM product 
    LEFT JOIN category ON product.category_id = category.category_id 
    WHERE Product_name LIKE :searchQuery
    ORDER BY product.product_id DESC 
    LIMIT :limit OFFSET :offset");
$stmt->bindValue(':searchQuery', $searchQuery, PDO::PARAM_STR);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Total Product: <?php echo e($totalProducts); ?></h1>

    <div class="header-container">
        <form action="productlist.php" method="GET">
            <?= html_search('search', 'q', 'Search...', e($_GET['q'] ?? '', '')) ?>
            <button type="submit" class="searchButton">Search</button>
        </form>
    </div>

    <hr>

    <?php if (count($results) > 0): ?>
    <table border="2">
        <tr>
            <th>ID</th>
            <th>Number</th>
            <th>Name</th>
            <th>Price ($)</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Category</th>
            <th>Detail</th>
            <th>Delete</th>
        </tr>
        <?php foreach ($results as $result): ?>
            <tr>
                <!-- Format ID to start with 'P' and end with the number -->
                <td><?php echo e('P' . $result['product_id']); ?></td>
                <td><?php echo $num++; ?></td>
                <td><?php echo e($result['Product_name']) ?></td>
                <td><?php echo e($result['price']) ?></td>
                <td>
                    <img height="80" width="80" src="../products/<?php echo e($result['image']) ?>" alt="Product Image">
                </td>
                <td><?php echo e($result['quantity']) ?></td>
                <td><?php echo e($result['category']) ?></td>
                <td><a href="productdetail.php?id=<?= e($result['product_id']) ?>">View</a></td>
                <td>
                    <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <input type="hidden" name="product_id" value="<?= e($result['product_id']) ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&<?= isset($_GET['q']) ? 'q=' . e($_GET['q']) : '' ?>" title="First page">« First</a>
            <a href="?page=<?php echo $page - 1; ?>&<?= isset($_GET['q']) ? 'q=' . e($_GET['q']) : '' ?>" title="Previous page">« Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&<?= isset($_GET['q']) ? 'q=' . e($_GET['q']) : '' ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>&<?= isset($_GET['q']) ? 'q=' . e($_GET['q']) : '' ?>" title="Next page">Next »</a>
            <a href="?page=<?php echo $totalPages; ?>&<?= isset($_GET['q']) ? 'q=' . e($_GET['q']) : '' ?>" title="Last page">Last »</a>
        <?php endif; ?>
    </div>

    <?php else: ?>
        <p style="text-align: center; color: #fff;">No products found.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>