<?php
require __DIR__ . '/headandFoot/head.php';

/* =====================
   PAGINATION SETTINGS
===================== */
$limit = 5; // records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

/* =====================
   FETCH REVIEWS
===================== */
$stmt = $_db->prepare("
    SELECT * 
    FROM review 
    ORDER BY review_id DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =====================
   TOTAL RECORDS
===================== */
$totalStmt = $_db->prepare("SELECT COUNT(*) AS total_reviews FROM review");
$totalStmt->execute();
$result = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalReviews = $result['total_reviews'];
$totalPages = ceil($totalReviews / $limit);

/* =====================
   ROW NUMBERING
===================== */
$num = $offset;
?>

<div class="max-w-6xl mx-auto py-4 px-4">
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Review Management</h1>
        <p class="text-gray-500 text-sm">Monitor and moderate user feedback.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Review</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($reviews)): foreach ($reviews as $review): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono"><?= ++$num ?></td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800"><?= e($review['name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate"><?= e($review['textarea']) ?></td>
                        <td class="px-6 py-4 text-sm text-yellow-400">
                            <?= str_repeat("★", (int)$review['number_of_star']) ?>
                            <span class="text-gray-300"><?= str_repeat("★", 5 - (int)$review['number_of_star']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <a href="reviewDetail.php?id=<?= e($review['review_id']) ?>" class="text-indigo-600 font-bold text-xs uppercase hover:underline">View</a>
                            <form action="reviewdelete.php" method="POST" class="inline" onsubmit="return confirm('Delete this review?');">
                                <input type="hidden" name="review_id" value="<?= e($review['review_id']) ?>">
                                <button type="submit" class="text-red-500 font-bold text-xs uppercase hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No reviews found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-all <?= $i == $page ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 hover:bg-gray-200 border border-gray-200' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>