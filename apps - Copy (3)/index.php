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


$productStmt = $_db->prepare("SELECT * FROM product ORDER BY product_id DESC LIMIT 9");
$productStmt->execute();
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

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
            <a href="profile.php?id=<?php echo e($query['user_id']); ?>" title="Profile" class="logo">
                <img height="30px" style="border-radius:50px;" width="30px" src="../uploaded_img/<?php echo e($query['image']); ?>" alt="">
                <?php echo e($query['username']); ?><span>.</span>
            </a>
        <?php else : ?>
            <a href="login.php" title="Login" class="logo">Guest<span>.</span></a>
        <?php endif; ?>
        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#product">Products</a>
            <a href="review.php?id=<?php echo e($query['user_id']); ?>">Review</a>
            <a href="#location">Location</a>
        </nav>
        <div class="icons">
        <a href="/order/cart.php" class="shoppingCart">🛒Cart(<?= cart_quantity() ?>)</a>
            <a href="/order/history.php" class="OrderHistory">🧾 History</a>
            <a href="../logout.php" class="Logout" title="Logout">🚪Logout</a>
        </div>
    </header>
    <!-- Header section end -->

    <!-- Home section start -->
    <section class="home" id="home">
        <div class="content">
            <h3>Powerful Laptops</h3>
            <span>High performance & sleek design</span>
            <p>Experience top-tier processing power, stunning displays, and seamless multitasking with our latest laptop collection. Perfect for work, gaming, and creativity.</p>
            <a href="product/list.php" class="btn">Shop Now</a>
        </div>
    </section>
    <!-- Home section end -->

    <!-- About section start -->
    <section id="about" class="about">
        <h1 class="heading"><span>About</span> Us</h1>
        <div class="row">
            <div class="video-container">
                <video src="video/latitude-5000-business-laptop.mp4" loop autoplay muted></video>
                <h3>The best selling laptop</h3>
            </div>
            <div class="content">
                <h3>Why choose us</h3>
                <p>We provide high-performance laptops with cutting-edge technology, sleek designs, and long battery life. Our devices are perfect for gaming, business, and creative professionals. Enjoy competitive prices, excellent customer support, and a hassle-free warranty.</p>
            </div>
        </div>
    </section>
    <!-- About section end -->

    <!-- Icon section start -->
    <section class="icons-container">
        <div class="icons">
            <img src="images/erik-mclean-Z41_IZ6Ctis-unsplash.jpg" alt="">
            <div class="info">
                <h3>Free Delivery</h3>
                <span>on all orders</span>
            </div>
        </div>
        <div class="icons">
            <img src="images/franck-sNvBTRQR7eE-unsplash.jpg" alt="">
            <div class="info">
                <h3>Secure Payment</h3>
                <span>Protected payment</span>
            </div>
        </div>
        <div class="icons">
            <img src="images/revendo-7x0dGJqbfgk-unsplash.jpg" alt="">
            <div class="info">
                <h3>Laptop Repair Service</h3>
                <span>Full experience</span>
            </div>
        </div>
        <div class="icons">
            <img src="images/kira-auf-der-heide-IPx7J1n_xUc-unsplash.jpg" alt="">
            <div class="info">
                <h3>Special Gift</h3>
                <span>on all orders</span>
            </div>
        </div>
    </section>
    <!-- Icon section end -->

    <!-- Product section start -->
    <section id="product" class="products">
        <h1 class="heading">latest <span>Products</span></h1>
        <div class="box-container">
            <?php foreach($products as $product): ?>
                <div class="box">
                    <div class="image">
                        <img src="../products/<?php echo e($product['image']); ?>" style="height:25rem; width:25rem;">
                    </div>
                    <div class="content">
                        <h3><?php echo e($product['Product_name']); ?></h3>
                        <div class="price">RM<?php echo e($product['price']); ?></div>
                    </div>
                </div>



            <?php endforeach; ?>
            <!-- <div class="box">
                <span class="discount">-10%</span>
                <div class="image">
                    <img src="images/26612208349_LOQ15IAX9ELG_202407230229581728839736092.avif" style="height:25rem;" alt="">
                   
                </div>
                <div class="content">
                    <h3>Laptop</h3>
                    <div class="price">12.99 <span>$15.99</span></div>
                </div>
            </div> -->
        </div>
    </section>
    <!-- Product section end -->

    <!-- Review section start -->
    <section class="review" id="review">
        <h1 class="heading">Customer's <span>review</span></h1>
        <div class="box-container">
            <?php foreach ($reviews as $review): ?>
                <div class="box">
                    <div class="star">
                        <?php 
                        if ($review['number_of_star'] == 5) {
                            echo "⭐⭐⭐⭐⭐";
                        } else if ($review['number_of_star'] == 4) {
                            echo "⭐⭐⭐⭐";
                        } else if ($review['number_of_star'] == 3) {
                            echo "⭐⭐⭐";
                        } else if ($review['number_of_star'] == 2) {
                            echo "⭐⭐";
                        } else if ($review['number_of_star'] == 1) {
                            echo "⭐";
                        }
                        ?>
                    </div>
                    <p><?php echo e($review['textarea']); ?></p>
                    <div class="user">
                        <div class="user-info">
                            <h3><?php echo e($review['name']); ?></h3>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- Review section end -->
    <section id="location">
    <div class="map-container">
    <h1 class="heading"><span>Our</span> Place</h1>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.537791254662!2d101.72398217447048!3d3.2152605527437395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc3843bfb6a031%3A0x2dc5e067aae3ab84!2sTunku%20Abdul%20Rahman%20University%20of%20Management%20and%20Technology%20(TAR%20UMT)!5e0!3m2!1sen!2sus!4v1745571985321!5m2!1sen!2sus" 
                width="800" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <section class="footer">
        <div class="credit">Created by <span>Law Seong Chun</span> | All rights reserved</div>
    </section>
    <!-- Footer section ends -->
</body>

</html>
