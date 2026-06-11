<?php 
require __DIR__ . '/headandFoot/head.php'; 

// --- Logic Section ---
try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 9;
    $offset = ($page - 1) * $perPage;

    // Build the query dynamically
    $whereClause = "(status IS NULL OR status != 'admin')";
    $params = [];

    if ($search !== '') {
        $whereClause .= " AND (name LIKE :search OR username LIKE :search OR email LIKE :search)";
        $params['search'] = "%$search%";
    }

    // Get Total Count
    $countStmt = $_db->prepare("SELECT COUNT(*) FROM user WHERE $whereClause");
    $countStmt->execute($params);
    $totalUsers = $countStmt->fetchColumn();

    // Get Data
    $stmt = $_db->prepare("SELECT * FROM user WHERE $whereClause LIMIT $offset, $perPage");
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-wide">Member Management</h1>
            <p class="text-slate-400 text-sm mt-0.5">Manage, filter, and view your platform members.</p>
        </div>
        
        <form action="member-listing.php" method="get" class="relative w-full md:w-80">
            <input type="text" name="search" placeholder="Search by name, email..." 
                   value="<?= e($search) ?>" 
                   class="w-full pl-4 pr-12 py-2.5 bg-slate-800 border border-slate-700 text-slate-200 placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all shadow-inner">
            <button type="submit" class="absolute right-4 top-3 text-slate-400 hover:text-blue-400 transition-colors">
                <i class="las la-search text-lg"></i>
            </button>
        </form>
    </div>

    <div class="bg-slate-800/40 backdrop-blur-md rounded-xl border border-slate-700/60 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-800/60 text-slate-400 uppercase text-xs tracking-wider font-semibold border-b border-slate-700/40">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">Num</th>
                        <th class="px-6 py-4 w-24">ID</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Email Address</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 text-slate-300">
                    <?php if (isset($error)): ?>
                        <tr><td colspan="6" class="px-6 py-12 text-center text-red-400 bg-red-500/5 font-medium"><?= e($error) ?></td></tr>
                    <?php elseif (!empty($users)): 
                        $num = $offset + 1;
                        foreach ($users as $user): ?>
                            <tr class="hover:bg-slate-700/20 transition-colors group">
                                <td class="px-6 py-4 text-center font-mono text-slate-500 group-hover:text-slate-400"><?= $num++ ?></td>
                                <td class="px-6 py-4 font-mono text-xs text-blue-400 font-semibold">M<?= e($user['user_id']) ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-700 text-slate-200 flex items-center justify-center font-bold text-xs uppercase group-hover:bg-blue-600 transition-colors">
                                            <?= mb_substr(e($user['name']), 0, 1); ?>
                                        </div>
                                        <span class="font-medium text-slate-200 group-hover:text-white transition-colors"><?= e($user['name']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-400 text-sm">@<?= e($user['username']) ?></td>
                                <td class="px-6 py-4 text-slate-400 text-sm font-mono"><?= e($user['email']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="memberDetail.php?id=<?= e($user['user_id']) ?>" 
                                       class="inline-flex items-center px-4 py-1.5 bg-slate-700 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm group-hover:shadow-md">
                                        View Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; 
                    else: ?>
                        <tr><td colspan="6" class="px-6 py-16 text-center text-slate-500 italic bg-slate-800/10">No platform members match your criteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalUsers > $perPage): ?>
            <div class="px-6 py-4 border-t border-slate-700/40 bg-slate-800/40 flex justify-center items-center gap-2">
                <?php
                    $totalPages = ceil($totalUsers / $perPage);
                    $searchQuery = $search !== '' ? '&search=' . urlencode($search) : '';
                    for ($i = 1; $i <= $totalPages; $i++):
                ?>
                    <a href="?page=<?= $i . $searchQuery ?>" 
                       class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all <?= $i == $page ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-slate-800 text-slate-400 hover:bg-slate-700 border border-slate-700/60' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>