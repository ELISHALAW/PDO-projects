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

<style>
    table {
        border-collapse: collapse;
        width: 80%;
        margin: 30px auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    thead {
        background-color: #f8f8f8;
    }

    th,
    td {
        padding: 15px;
        border: 1px solid #ddd;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    th {
        font-size: 18px;
    }

    td {
        font-size: 16px;
    }

    .stars {
        font-size: 20px;
        color: #FFD700;
    }

    .delete-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    /* Pagination */
    .pagination {
        text-align: center;
        margin: 20px 0;
    }

    .pagination a {
        padding: 8px 12px;
        margin: 0 3px;
        border: 1px solid #ccc;
        text-decoration: none;
        color: black;
    }

    .pagination a.active {
        background: #333;
        color: white;
    }

    .pagination a:hover {
        background: #555;
        color: white;
    }
</style>

<table>
    <thead>
        <tr>
            <th>Num</th>
            <th>Name</th>
            <th>Review</th>
            <th>Rating</th>
            <th>View</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($reviews): ?>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td><?= ++$num ?></td>
                    <td><?= e($review['name']) ?></td>
                    <td><?= e($review['textarea']) ?></td>
                    <td class="stars">
                        <?= str_repeat("⭐", (int)$review['number_of_star']) ?>
                    </td>
                    <td>
                        <a href="reviewDetail.php?id=<?= e($review['review_id']) ?>">View</a>
                    </td>
                    <td>
                        <form action="reviewdelete.php" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this review?');">
                            <input type="hidden" name="review_id" value="<?= e($review['review_id']) ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No reviews found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Next</a>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>