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

$pageTitles = [
    'adminHomepage.php' => 'Dashboard',
    'member-listing.php' => 'Customer Management',
    'createproduct.php' => 'Create Product',
    'orderlist.php' => 'Order Management',
    'productlist.php' => 'Product Management',
    'reviewlist.php' => 'Review Management',
];

$pageSubtitles = [
    'adminHomepage.php' => 'Overview of your admin dashboard.',
    'member-listing.php' => 'Manage and monitor your platform members.',
    'createproduct.php' => 'Add a new product to your store.',
    'orderlist.php' => 'Review and manage customer orders.',
    'productlist.php' => 'View and manage all products.',
    'reviewlist.php' => 'Monitor and moderate user feedback.',
];

$adminPageTitle = $pageTitles[$currentPage] ?? 'Admin Panel';
$adminPageSubtitle = $pageSubtitles[$currentPage] ?? 'Manage your admin settings.';

$search = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 , maximum-scale=1">
    <title>Admin page</title>
    <link rel="icon" type="image/png" href="../images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="overflow-hidden">
    <div class="flex h-screen overflow-hidden bg-gray-100">
        <aside class="w-64 bg-white border-r">
            <div class="p-6">
                <h2 class="text-xl font-semibold flex items-center gap-2"><span class="text-2xl">&#128187;</span><span>WEIJIAN180</span></h2>
            </div>
            <nav class="px-4 pb-6">
                <ul class="space-y-1">
                    <li>
                        <a href="adminHomepage.php" class="<?= ($currentPage === 'adminHomepage.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span class="text-lg"> &#127968;</span>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="member-listing.php" class="<?= ($currentPage === 'member-listing.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span>&#128101;</span>
                            <span>Customers</span>
                        </a>
                    </li>
                    <li>
                        <a href="createproduct.php" class="<?= ($currentPage === 'createproduct.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span>&#128221;</span>
                            <span>Create Product</span>
                        </a>
                    </li>
                    <li>
                        <a href="orderlist.php" class="<?= ($currentPage === 'orderlist.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span>&#128722;</span>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="productlist.php" class="<?= ($currentPage === 'productlist.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span>&#129534;</span>
                            <span>Product list</span>
                        </a>
                    </li>
                    <li>
                        <a href="reviewlist.php" class="<?= ($currentPage === 'reviewlist.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span>&#128203;</span>
                            <span>Review list</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" class="<?= ($currentPage === 'logout.php') ? 'flex items-center gap-3 px-4 py-2 rounded bg-gray-200 text-black' : 'flex items-center gap-3 px-4 py-2 rounded text-gray-700 hover:bg-gray-100' ?>">
                            <span>&#128682;</span>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="w-full flex items-center justify-between px-4 py-3 bg-white border-b shadow-sm sticky top-0 z-20">
                <div class="flex items-center gap-4">
                    <label for="nav-toggle" class="cursor-pointer text-2xl p-2 rounded-md hover:bg-gray-100">
                        <span class="las la-bars"></span>
                    </label>
                    <div>
                        <h2 class="text-lg font-semibold"><?php echo e($adminPageTitle); ?></h2>
                        <p class="text-sm text-gray-500"><?php echo e($adminPageSubtitle); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <form method="get" action="" class="hidden md:flex items-center bg-gray-100 rounded-full px-3 py-1 gap-2 w-80">
                        <input name="search" type="search" placeholder="Search products..." value="<?php echo e($search); ?>" class="bg-transparent outline-none w-full text-sm text-gray-700" />
                        <button type="submit" class="bg-slate-800 text-white px-3 py-1 rounded-full text-sm">Search</button>
                    </form>

                    <button type="button" title="Notifications" class="relative p-2 rounded-md hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-xs bg-red-500 text-white rounded-full">3</span>
                    </button>

                    <div class="flex items-center gap-3">
                        <img src="amos2.jpg" class="w-10 h-10 rounded-full object-cover" alt="">
                        <div class="hidden sm:block">
                            <h4 class="text-black"><?php echo e($query['username']); ?></h4>
                            <small class="text-gray-500">Admin</small>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto mt-0 min-h-0 h-full bg-transparent">