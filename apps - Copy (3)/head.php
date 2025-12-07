<?php require __DIR__ . '/_base.php'; ?>
<?php require __DIR__ . '/Homepage/loginfunction/loginfunction.php'; ?>

<?php 


$currentPage = basename($_SERVER['PHP_SELF']);
if ($query['status'] == 'admin') {
    header("Location: login.php");
    exit();
}

$stmt = $_db->prepare("SELECT * FROM review ORDER BY review_id DESC LIMIT 3");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Selling</title>
    <link rel="stylesheet" href="../css/homepage.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        /* Add styling for the map and its title to match the page's aesthetic */
        .map-container {
            margin-top: 30px;
            text-align: center;
        }

        .map-container h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .map-container h2 span {
            color: #ff7800; /* Match the orange color used in other headings */
        }

        .map-container iframe {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            height: 400px;
            border: 0;
        }

        @media (max-width: 768px) {
            .map-container iframe {
                height: 300px;
            }

            .map-container h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header section start -->
    <header >
        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="bar">☰</label>
        <?php if (!empty($query) && is_array($query)) : ?>
            <a href="../profile.php?id=<?php echo e($query['user_id']); ?>" title="Profile" class="logo">
                <img height="30px" style="border-radius:50px;" width="30px" src="../uploaded_img/<?php echo e($query['image']); ?>" alt="">
                <?php echo e($query['username']); ?><span>.</span>
            </a>
        <?php else : ?>
            <a href="login.php" title="Login" class="logo">Guest<span>.</span></a>
        <?php endif; ?>
        <nav class="navbar">
            <a href="../index.php">Home</a>
            <a href="../index.php">About</a>
            <a href="../index.php">Products</a>
            <a href="../review.php?id=<?php echo e($query['user_id']); ?>">Review</a>
            <a href="../index.php">Location</a>
        </nav>
        <div class="icons">
            <a href="/order/cart.php" class="shoppingCart">🛒Cart(<?= cart_quantity() ?>)</a>
            <a href="/order/history.php" class="OrderHistory">🧾 History</a>
            <a href="../logout.php" class="Logout" title="Logout">🚪Logout</a>
        </div>
    </header>