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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Selling</title>
    <link rel="icon" type="image/png" href="../images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen">

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm shadow-slate-100/40 px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between transition-all duration-300">
        
        <input type="checkbox" name="toggler" id="toggler" class="peer hidden">
        <label for="toggler" class="bar md:hidden text-2xl text-slate-600 hover:text-slate-900 cursor-pointer transition select-none order-2 md:order-none">
            ☰
        </label>
        
        <div class="flex items-center order-1 md:order-none">
            <?php if (!empty($query) && is_array($query)) : ?>
                <a href="../profile.php?id=<?php echo e($query['user_id']); ?>" title="Profile" class="group flex items-center space-x-2.5 font-bold text-xl text-slate-800 tracking-tight transition">
                    <img class="h-9 w-9 rounded-full object-cover border border-slate-200 shadow-xs group-hover:scale-105 transition duration-200" src="../uploaded_img/<?php echo e($query['image']); ?>" alt="User Avatar">
                    <span><?php echo e($query['username']); ?><span class="text-orange-500">.</span></span>
                </a>
            <?php else : ?>
                <a href="login.php" title="Login" class="font-bold text-xl text-slate-800 tracking-tight">
                    Guest<span class="text-orange-500">.</span>
                </a>
            <?php endif; ?>
        </div>

        <nav class="navbar absolute md:relative top-full left-0 w-full md:w-auto bg-white md:bg-transparent border-b border-slate-100 md:border-b-0 p-4 md:p-0 flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-8 font-medium text-slate-600 hidden peer-checked:flex md:flex items-start md:items-center shadow-lg md:shadow-none transition-all duration-300">
            <a href="../index.php" class="hover:text-orange-500 transition duration-150 py-1 md:py-0 w-full md:w-auto">Home</a>
            <a href="../index.php" class="hover:text-orange-500 transition duration-150 py-1 md:py-0 w-full md:w-auto">About</a>
            <a href="../index.php" class="hover:text-orange-500 transition duration-150 py-1 md:py-0 w-full md:w-auto">Products</a>
            <a href="../review.php?id=<?php echo e($query['user_id']); ?>" class="hover:text-orange-500 transition duration-150 py-1 md:py-0 w-full md:w-auto">Review</a>
            <a href="../index.php" class="hover:text-orange-500 transition duration-150 py-1 md:py-0 w-full md:w-auto">Location</a>
        </nav>

        <div class="icons flex items-center space-x-2 sm:space-x-4 text-sm font-semibold order-3 md:order-none">
            <a href="/order/cart.php" class="shoppingCart inline-flex items-center space-x-1 px-3 py-1.5 bg-slate-100 hover:bg-orange-50 hover:text-orange-600 rounded-xl transition duration-150 text-slate-700">
                <span>🛒</span>
                <span class="hidden sm:inline">Cart</span>
                <span class="bg-white px-1.5 py-0.5 rounded-md border border-slate-200 text-xs font-bold text-slate-800 ml-1 group-hover:border-orange-200"><?= cart_quantity() ?></span>
            </a>
            
            <a href="/order/history.php" class="OrderHistory inline-flex items-center space-x-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl transition duration-150 text-slate-700">
                <span>🧾</span>
                <span class="hidden sm:inline">History</span>
            </a>
            
            <a href="../logout.php" class="Logout inline-flex items-center space-x-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition duration-150" title="Logout">
                <span>🚪</span>
                <span class="hidden sm:inline">Logout</span>
            </a>
        </div>
    </header>