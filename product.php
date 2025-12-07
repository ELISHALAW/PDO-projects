<?php
require __DIR__ . '/_base.php';
require __DIR__ . '/./Homepage/loginfunction/loginfunction.php'; 

if(!isset($_SESSION['id'])){
    echo "<script>alert('Please log in'); window.location.href='login.php';</script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product listing</title>
    <link rel="stylesheet" href="./css/product.css">
</head>
<body>
<a href="index.php" style="color:white; justify-content:center;">Back to homepage</a>
    <div id="card-area">
        <div class="wrapper">
            <div class="box-area">
                
                <div class="box">
                    <img height="315px" width="315px" src="./images/dell.jpg" alt="">
                    <div class="overlay">
                        <h3>Dell</h3>
                        <p>Dell laptops are reliable, high-performance, and versatile for all users.</p>
                        <a href="example.php?category_id=">View More</a>
                    </div>
                </div>
                <div class="box">
                    <img height="315px" width="315px" src="./images/huawei.jpg" alt="">
                    <div class="overlay">
                        <h3>Huawei</h3>
                        <p>Huawei offers innovative, high-tech devices with advanced connectivity solutions.</p>
                        <a href="example.php">View More</a>
                    </div>
                </div>
                <div class="box">
                    <img height="315px" width="315px" src="./images/asus.jpg" alt="">
                    <div class="overlay">
                        <h3>Asus</h3>
                        <p>Asus provides powerful, innovative laptops and gaming hardware worldwide.</p>
                        <a href="example.php">View More</a>
                    </div>
                </div>
                <div class="box">
                    <img height="315px" width="315px" src="./images/acer.jpg" alt="">
                    <div class="overlay">
                        <h3>Acer</h3>
                        <p>Acer is a Taiwanese electronics company known for laptops and PCs.</p>
                        <a href="example.php">View More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
</body>
</html>