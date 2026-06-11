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
    'member-listing.php' => 'Customers',
    'createproduct.php' => 'Create Product',
    'orderlist.php' => 'Orders',
    'productlist.php' => 'Product List',
    'reviewlist.php' => 'Reviews',
];

$pageSubtitles = [
    'adminHomepage.php' => 'Overview of your store performance.',
    'member-listing.php' => 'Manage and monitor your customers.',
    'createproduct.php' => 'Add new products to your catalog.',
    'orderlist.php' => 'Review and process customer orders.',
    'productlist.php' => 'Manage your entire product inventory.',
    'reviewlist.php' => 'Moderate customer reviews and feedback.',
];

$adminPageTitle = $pageTitles[$currentPage] ?? 'Admin Panel';
$adminPageSubtitle = $pageSubtitles[$currentPage] ?? 'Manage your store';
$search = '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEIJIAN180 Admin</title>
    <link rel="icon" type="image/png" href="../images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* CSS Cleanups */
        .nav-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link:hover {
            transform: translateX(4px);
        }
        .active {
            background-color: #1f2937;
            color: white;
            border-left: 4px solid #3b82f6;
        }
        
        /* Smooth placeholder tracking transitions */
        input::placeholder {
            color: #6b7280 !important;
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-200 overflow-hidden antialiased">
    <div class="flex h-screen w-screen overflow-hidden">
        
        <aside class="w-72 bg-gray-900 border-r border-gray-800 flex-shrink-0 flex flex-col z-20">
            <div class="p-6 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-2xl shadow-lg shadow-blue-600/10">
                        💻
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white">WEIJIAN180</h1>
                        <p class="text-xs text-gray-500 -mt-1">Admin Portal</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 overflow-y-auto">
                <ul class="space-y-1">
                    <li>
                        <a href="adminHomepage.php" 
                           class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 <?= ($currentPage === 'adminHomepage.php') ? 'active' : 'hover:bg-gray-800' ?>">
                            <span class="text-xl la la-home"></span>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="member-listing.php" 
                           class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 <?= ($currentPage === 'member-listing.php') ? 'active' : 'hover:bg-gray-800' ?>">
                            <span class="text-xl la la-users"></span>
                            <span class="font-medium">Customers</span>
                        </a>
                    </li>
                    <li>
                        <a href="createproduct.php" 
                           class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 <?= ($currentPage === 'createproduct.php') ? 'active' : 'hover:bg-gray-800' ?>">
                            <span class="text-xl la la-plus-circle"></span>
                            <span class="font-medium">Create Product</span>
                        </a>
                    </li>
                    <li>
                        <a href="orderlist.php" 
                           class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 <?= ($currentPage === 'orderlist.php') ? 'active' : 'hover:bg-gray-800' ?>">
                            <span class="text-xl la la-shopping-cart"></span>
                            <span class="font-medium">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a href="productlist.php" 
                           class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 <?= ($currentPage === 'productlist.php') ? 'active' : 'hover:bg-gray-800' ?>">
                            <span class="text-xl la la-box"></span>
                            <span class="font-medium">Product List</span>
                        </a>
                    </li>
                    <li>
                        <a href="reviewlist.php" 
                           class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-gray-300 <?= ($currentPage === 'reviewlist.php') ? 'active' : 'hover:bg-gray-800' ?>">
                            <span class="text-xl la la-star"></span>
                            <span class="font-medium">Review List</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800 mt-auto">
                <a href="logout.php" 
                   class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-950/40 hover:text-red-300">
                    <span class="text-xl la la-sign-out-alt"></span>
                    <span class="font-medium">Logout</span>
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <header class="w-full bg-gray-900 border-b border-gray-800 px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between transition-all duration-200 z-10 flex-shrink-0">
                <div class="flex items-center gap-4 flex-shrink-0">
                    <button id="mobile-menu" class="lg:hidden text-2xl text-gray-400 hover:text-white transition-colors">
                        <span class="la la-bars"></span>
                    </button>
                    <div>
                        <h2 class="text-2xl font-semibold text-white tracking-tight"><?php echo htmlspecialchars($adminPageTitle); ?></h2>
                        <p class="text-sm text-gray-500 mt-0.5"><?php echo htmlspecialchars($adminPageSubtitle); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-6 flex-grow lg:flex-1 justify-end max-w-full pl-6">
                    <form method="get" class="hidden md:flex items-center bg-gray-800/60 border border-gray-700/40 rounded-2xl px-4 py-2.5 w-96 focus-within:w-[36rem] lg:focus-within:w-[44rem] transition-all duration-300 ease-in-out shadow-inner">
                        <span class="la la-search text-gray-500 text-lg"></span>
                        <input 
                            name="search" 
                            type="search" 
                            placeholder="Search products, orders..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="bg-transparent border-0 outline-none focus:outline-none focus:ring-0 ml-3 text-sm flex-1 placeholder-gray-500 text-slate-200"
                        >
                    </form>

                    <button class="relative p-3 bg-gray-800/40 hover:bg-gray-800 border border-gray-800 rounded-2xl transition-all duration-150 flex-shrink-0 group">
                        <span class="la la-bell text-2xl text-gray-400 group-hover:text-gray-200 transition-colors"></span>
                        <span class="absolute top-2 right-2 w-5 h-5 bg-red-500 text-[10px] font-bold flex items-center justify-center rounded-full text-white shadow-md shadow-red-500/20">3</span>
                    </button>

                    <div class="flex items-center gap-3 pl-3 border-l border-gray-800 flex-shrink-0">
                        <img src="amos2.jpg" alt="Admin" 
                             class="w-10 h-10 rounded-2xl object-cover border border-gray-700/60 shadow-md">
                        <div class="hidden sm:block text-left">
                            <div class="font-semibold text-sm text-slate-200 leading-tight"><?php echo htmlspecialchars($query['username']); ?></div>
                            <div class="text-[11px] text-emerald-400 font-medium flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Online
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-950 p-6 md:p-8">