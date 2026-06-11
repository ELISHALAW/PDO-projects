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
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white tracking-wide">Review Management</h1>
        <p class="text-slate-400 text-sm mt-0.5">Monitor, moderate, and assess qualitative user feedback metrics.</p>
    </div>

    <div class="bg-slate-800/40 backdrop-blur-md rounded-xl border border-slate-700/60 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/60 text-slate-400 uppercase text-xs tracking-wider font-semibold border-b border-slate-700/40">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">#</th>
                        <th class="px-6 py-4 w-48">Reviewer</th>
                        <th class="px-6 py-4">Feedback Content</th>
                        <th class="px-6 py-4 w-36">Rating Score</th>
                        <th class="px-6 py-4 text-center w-40">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 text-slate-300">
                    <?php if (!empty($reviews)): foreach ($reviews as $review): ?>
                    <tr class="hover:bg-slate-700/20 transition-colors text-sm group">
                        <td class="px-6 py-4 text-center font-mono text-slate-500"><?= ++$num ?></td>
                        <td class="px-6 py-4 font-semibold text-slate-200 group-hover:text-white transition-colors"><?= e($review['name']) ?></td>
                        <td class="px-6 py-4 text-slate-400 max-w-xs truncate font-normal italic">"<?= e($review['textarea']) ?>"</td>
                        <td class="px-6 py-4 text-amber-400 font-serif text-base tracking-wide select-none">
                            <?= str_repeat("★", (int)$review['number_of_star']) ?><span class="text-slate-700"><?= str_repeat("★", 5 - (int)$review['number_of_star']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="reviewDetail.php?id=<?= e($review['review_id']) ?>" class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm">View</a>
                            <form action="reviewdelete.php" method="POST" class="inline" onsubmit="return confirm('Delete this review?');">
                                <input type="hidden" name="review_id" value="<?= e($review['review_id']) ?>">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-slate-900 border border-slate-700/60 hover:bg-rose-600/20 hover:border-rose-500/40 text-slate-400 hover:text-rose-400 text-xs font-semibold rounded-lg transition-all">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="px-6 py-16 text-center text-slate-500 italic bg-slate-800/10">No public content feedback has been recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($totalPages ?? 0) > 1): ?>
        <div class="px-6 py-4 bg-slate-800/40 border-t border-slate-700/40 flex justify-center gap-1.5">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all <?= $i == $page ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 border border-slate-700/60' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>