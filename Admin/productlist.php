<?php require __DIR__ . '/headandFoot/head.php'; ?>


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

<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Product Management</h1>
            <p class="text-slate-400 text-sm mt-0.5">
                Total Listed Items: <span class="font-bold text-blue-400 font-mono"><?= e($totalProducts ?? 0) ?></span>
            </p>
        </div>

        <form action="productlist.php" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search products..." 
                   class="w-full md:w-64 px-4 py-2.5 bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all shadow-inner text-sm">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm rounded-xl transition-all shadow-md shadow-blue-600/10">
                Search
            </button>
        </form>
    </div>

    <div class="bg-slate-800/40 backdrop-blur-md rounded-xl border border-slate-700/60 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/60 text-slate-400 uppercase text-xs tracking-wider font-semibold border-b border-slate-700/40">
                    <tr>
                        <th class="px-6 py-4 w-24">ID</th>
                        <th class="px-6 py-4 w-28 text-center">Preview</th>
                        <th class="px-6 py-4">Item Descriptor</th>
                        <th class="px-6 py-4">Unit Price</th>
                        <th class="px-6 py-4">Stock Level</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4 text-center">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 text-slate-300">
                    <?php if (count($results) > 0): foreach ($results as $result): ?>
                    <tr class="hover:bg-slate-700/20 transition-colors text-sm group">
                        <td class="px-6 py-4 font-mono text-xs text-blue-400 font-bold">P<?= e($result['product_id']) ?></td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <img src="../products/<?= e($result['image']) ?>" class="w-12 h-12 object-cover rounded-lg bg-slate-900 border border-slate-700/60 shadow-md group-hover:scale-105 transition-transform" alt="Product">
                            </div>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-200 group-hover:text-white transition-colors"><?= e($result['Product_name']) ?></td>
                        <td class="px-6 py-4 font-mono font-semibold text-emerald-400">RM <?= number_format($result['price'], 2) ?></td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-xs px-2.5 py-1 rounded-md font-semibold <?= (int)$result['quantity'] > 5 ? 'bg-slate-900/60 text-slate-300 border border-slate-700/40' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' ?>">
                                <?= e($result['quantity']) ?> left
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs"><span class="px-2.5 py-1 bg-slate-900/40 border border-slate-700/50 rounded-lg"><?= e($result['category']) ?></span></td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="productdetail.php?id=<?= e($result['product_id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">View</a>
                            <form action="delete.php" method="POST" class="inline" onsubmit="return confirm('Delete this product?');">
                                <input type="hidden" name="product_id" value="<?= e($result['product_id']) ?>">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-slate-900 border border-slate-700/60 hover:bg-rose-600/20 hover:border-rose-500/40 text-slate-400 hover:text-rose-400 text-xs font-semibold rounded-lg transition-all">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="px-6 py-16 text-center text-slate-500 italic bg-slate-800/10">No stock entities match database parameters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-slate-800/40 border-t border-slate-700/40 flex justify-center gap-1.5">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&q=<?= e($_GET['q'] ?? '') ?>" 
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all <?= $i === $page ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 border border-slate-700/60' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>