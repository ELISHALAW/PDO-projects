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

<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-3 gap-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Product Management</h1>
            <p class="text-gray-500 text-sm">Total Products: <span class="font-bold text-gray-900"><?= e($totalProducts ?? 0) ?></span></p>
        </div>

        <form action="productlist.php" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search products..." 
                   class="w-full md:w-64 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" class="px-5 py-2 bg-gray-900 text-white font-semibold rounded-lg hover:bg-indigo-600 transition-all">Search</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Image</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (count($results) > 0): foreach ($results as $result): ?>
                    <tr class="hover:bg-gray-50 transition-colors text-sm">
                        <td class="px-6 py-4 font-mono font-bold text-gray-800">P<?= e($result['product_id']) ?></td>
                        <td class="px-6 py-4">
                            <img src="../products/<?= e($result['image']) ?>" class="w-16 h-16 object-cover rounded-lg shadow-sm" alt="Product">
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800"><?= e($result['Product_name']) ?></td>
                        <td class="px-6 py-4 text-gray-600">$<?= number_format($result['price'], 2) ?></td>
                        <td class="px-6 py-4 text-gray-600"><?= e($result['quantity']) ?></td>
                        <td class="px-6 py-4 text-gray-500"><?= e($result['category']) ?></td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <a href="productdetail.php?id=<?= e($result['product_id']) ?>" class="text-indigo-600 font-bold text-xs uppercase hover:underline">View</a>
                            <form action="delete.php" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                                <input type="hidden" name="product_id" value="<?= e($result['product_id']) ?>">
                                <button type="submit" class="text-red-500 font-bold text-xs uppercase hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">No products found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&q=<?= e($_GET['q'] ?? '') ?>" 
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all <?= $i === $page ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 hover:bg-gray-200 border border-gray-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>