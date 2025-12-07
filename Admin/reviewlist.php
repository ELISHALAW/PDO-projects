<?php require __DIR__ . '/headandFoot/head.php';?>

<?php

    $stmt = $_db->prepare("SELECT * FROM review");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);



    $totalStmt = $_db->prepare("SELECT COUNT(*) AS total_reviews FROM review");
    $totalStmt->execute();
    $result = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalReviews = $result['total_reviews'];

    $num = 0;
    
?>

<style>
    table {
        border-collapse: collapse;
        width: 80%;
        margin: 30px auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }

    thead {
        background-color: #f8f8f8;
    }

    th, td {
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
        color: #FFD700; /* Gold */
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
        <?php foreach($reviews as $review): ?>
            <tr>
                <td><?php if($num <= $totalReviews){
                     $num++;
                    echo $num;
                   
                }; ?></td>
                <td><?php echo e($review['name']); ?></td>
                <td><?php echo e($review['textarea']); ?></td>
                <td class="stars">
                    <?php echo str_repeat("⭐", (int)$review['number_of_star']); ?>
                </td>
                <td><a href="reviewDetail.php?id=<?php echo e($review['review_id']) ?>">View</a></td>
                <td>
                    <form action="reviewdelete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        <input type="hidden" name="review_id" value="<?= e($review['review_id']) ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>
