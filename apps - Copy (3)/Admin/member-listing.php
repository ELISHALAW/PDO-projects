<?php require __DIR__ . '/headandFoot/head.php'; ?>

<style>
    .table-container {
        width: 95%;
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
    }

    .styled-table th, .styled-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .styled-table thead {
        background: #f4f4f4;
    }

    .styled-table tbody tr:hover {
        background: #f9f9f9;
    }

    .edit-btn {
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        margin: 2px;
        background: black;
        color: white;
    }

    .edit-btn:hover {
        background: #45a049;
    }

    .search-wrapper {
        width: 300px;
        border: 1px solid #ccc;
        border-radius: 30px;
        height: 50px;
        display: flex;
        align-items: center;
        margin: 20px auto;
        background-color: #fff;
    }

    .search-wrapper input {
        height: 40px;
        padding: 0 1rem;
        border: none;
        outline: none;
        font-size: 0.8rem;
        border-radius: 40px;
        background-color: #fff;
        flex: 1;
    }

    .submit {
        color: #3498db;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: none;
        background-color: transparent;
    }

    .pagination {
        text-align: center;
        margin: 20px 0;
    }

    .pagination a {
        color: white;
        background: black;
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid #ddd;
        margin: 0 2px;
        border-radius: 4px;
    }

    .pagination a.active {
        background-color:black;
        color: white;
        border: 1px solid white;
    }

    .pagination a:hover {
        background-color: #ddd;
    }
</style>

<div class="search-wrapper">
    <form action="member-listing.php" method="get" style="display: flex; width: 100%;">
        <input type="text" name="search" placeholder="Search" value="<?= e($_GET['search'] ?? '') ?>">
        <button type="submit" class="submit">🔍</button>
    </form>
</div>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>Num</th>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
<?php
try {
    $searchSubmitted = isset($_GET['search']);
    $search = $searchSubmitted ? trim($_GET['search']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 9;
    $offset = ($page - 1) * $perPage;

    $users = [];
    $totalUsers = 0;

    if ($searchSubmitted && $search !== '') {
        $countStmt = $_db->prepare("SELECT COUNT(*) FROM user WHERE (name LIKE :search OR username LIKE :search OR email LIKE :search) AND (status IS NULL OR status != 'admin')");
        $countStmt->execute(['search' => "%$search%"]);
        $totalUsers = $countStmt->fetchColumn();

        $stmt = $_db->prepare("SELECT * FROM user WHERE (name LIKE :search OR username LIKE :search OR email LIKE :search) AND (status IS NULL OR status != 'admin') LIMIT :offset, :perPage");
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (!$searchSubmitted) {
        $countStmt = $_db->query("SELECT COUNT(*) FROM user WHERE (status IS NULL OR status != 'admin')");
        $totalUsers = $countStmt->fetchColumn();

        $stmt = $_db->prepare("SELECT * FROM user WHERE (status IS NULL OR status != 'admin') LIMIT :offset, :perPage");
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($users)) {
        $num = $offset + 1;
        foreach ($users as $user) {
?>
            <tr>
                <td><?= $num++ ?></td>
                <td>M<?= e($user['user_id']) ?></td>
                <td><?= e($user['name']) ?></td>
                <td><?= e($user['username']) ?></td>
                <td><?= e($user['email']) ?></td>
                <td>
                    <a href="memberDetail.php?id=<?= e($user['user_id']) ?>" class="edit-btn">View</a>
                </td>
            </tr>
<?php
        }
    } elseif ($searchSubmitted) {
        echo '<tr style="text-align:center;"><td colspan="5">No users found.</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="5" style="color:red;">Database error: ' . e($e->getMessage()) . '</td></tr>';
}
?>
        </tbody>
    </table>

    <?php if ($totalUsers > $perPage): ?>
        <div class="pagination">
            <?php
                $totalPages = ceil($totalUsers / $perPage);
                $searchQuery = $searchSubmitted ? '&search=' . urlencode($search) : '';

                for ($i = 1; $i <= $totalPages; $i++):
            ?>
                <a href="?page=<?= $i . $searchQuery ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>
