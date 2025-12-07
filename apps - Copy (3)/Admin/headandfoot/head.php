<?php
require '../_base.php';
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require '../Homepage/loginfunction/loginfunction.php';

if($query['status'] !== 'admin'){
    header("Location: ../login.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$search = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 , maximum-scale=1">
    <title>Admin page</title>
    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <input type="checkbox" id="nav-toggle">
    <div class="sidebar">
        <div class="sidebar-brand">
            <h2><span>&#128187;</span> <span>WEIJIAN180</span></h2>
        </div>
        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="adminHomepage.php" class="<?= ($currentPage === 'adminHomepage.php') ? 'active' : '' ?>"><span> &#127968;</span> <span>Dashboard</span></a>
                </li>
                <li>
                    <a href="member-listing.php" class="<?= ($currentPage === 'member-listing.php') ? 'active' : '' ?>"><span>&#128101;</span><span>Customers</span></a>
                </li>
                <li>
                    <a href="createproduct.php" class="<?= ($currentPage === 'createproduct.php') ? 'active' : '' ?>"><span>&#128221;</span><span>Create Product</span></a>
                </li>
                <li>
                    <a href="orderlist.php" class="<?= ($currentPage === 'orderlist.php') ? 'active' : '' ?>"><span>&#128722;</span><span>Orders</span></a>
                </li>
                <li>
                    <a href="productlist.php" class="<?= ($currentPage === 'productlist.php') ? 'active' : '' ?>"><span>&#129534;</span><span>Product list</span></a>
                </li>
                <li>
                    <a href="reviewlist.php" class="<?= ($currentPage === 'reviewlist.php') ? 'active' : '' ?>"><span>&#128203;</span><span>Review list</span></a>
                </li>
                <li>
                    <a href="logout.php" class="<?= ($currentPage === 'logout.php') ? 'active' : '' ?>"><span>&#128682;</span><span>Logout</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="main-content">
        <header>
            <div class="header-title">
                <h2>
                    <label for="nav-toggle">
                        <span class="las la-bars"></span>
                    </label>
                    Dashboard
                </h2>
            </div>
            
            <div class="user-wrapper">
                <img src="amos2.jpg" width="40px" height="40px" alt="">
                <div>
                    <h4 style="color:black;"><?php echo e($query['username']); ?></h4>
                    <small>Admin</small>
                </div>
            </div>
        </header>
        <main>