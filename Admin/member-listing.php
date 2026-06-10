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

<div class="max-w-6xl mx-auto py-2 px-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-3 gap-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Member Management</h1>
            <p class="text-gray-500 text-sm">Manage and view your platform members.</p>
        </div>
        
        <form action="member-listing.php" method="get" class="relative w-full md:w-72">
            <input type="text" name="search" placeholder="Search by name, email..." 
                   value="<?= e($search) ?>" 
                   class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-indigo-600">
                🔍
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Num</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (isset($error)): ?>
                        <tr><td colspan="6" class="px-6 py-10 text-center text-red-500"><?= e($error) ?></td></tr>
                    <?php elseif (!empty($users)): 
                        $num = $offset + 1;
                        foreach ($users as $user): ?>
                            <tr class="hover:bg-indigo-50/50 transition-colors">
                                <td class="px-6 py-4 text-gray-500 font-medium"><?= $num++ ?></td>
                                <td class="px-6 py-4 text-gray-900 font-mono">M<?= e($user['user_id']) ?></td>
                                <td class="px-6 py-4 font-semibold text-gray-800"><?= e($user['name']) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= e($user['username']) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= e($user['email']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="memberDetail.php?id=<?= e($user['user_id']) ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-indigo-600 transition-all shadow-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; 
                    else: ?>
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">No members found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalUsers > $perPage): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-center items-center gap-1">
                <?php
                    $totalPages = ceil($totalUsers / $perPage);
                    $searchQuery = $search !== '' ? '&search=' . urlencode($search) : '';
                    for ($i = 1; $i <= $totalPages; $i++):
                ?>
                    <a href="?page=<?= $i . $searchQuery ?>" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all <?= $i == $page ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>