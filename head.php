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

    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-xl shadow-sm transition duration-300">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="toggler" id="toggler" class="peer hidden">
                    <label for="toggler" class="bar md:hidden inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-2xl text-slate-600 transition hover:border-slate-300 hover:bg-slate-100 cursor-pointer select-none">☰</label>
                    <a href="../index.php" class="inline-flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-slate-300 hover:bg-slate-100">
                        <img src="../images/computer.webp" alt="Logo" class="h-11 w-11 rounded-2xl object-cover border border-slate-200 bg-white" />
                        <div class="min-w-0">
                            <p class="text-base font-semibold text-slate-900">WEIJIAN180</p>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">Store Portal</p>
                        </div>
                    </a>
                </div>

                <form action="#" class="hidden flex-1 items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-2 shadow-sm md:flex">
                    <span class="text-slate-400">🔍</span>
                    <input type="search" name="q" placeholder="Search products, orders..." class="w-full bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400" />
                </form>

                <div class="flex items-center gap-2">
                    <a href="/order/cart.php" class="inline-flex items-center gap-2 rounded-3xl border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition duration-150 hover:bg-orange-50 hover:text-orange-600">
                        <span>🛒</span>
                        <span class="hidden sm:inline">Cart</span>
                        <span class="rounded-full bg-white px-2 py-0.5 text-xs font-bold text-slate-800"><?= cart_quantity() ?></span>
                    </a>
                    <?php if (isset($_SESSION['id'])): ?>
                    <a href="/wishlist.php" class="inline-flex items-center gap-2 rounded-3xl border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition duration-150 hover:bg-pink-50 hover:text-pink-600">
                        <span>❤️</span>
                        <span class="hidden sm:inline">Wishlist</span>
                        <span class="rounded-full bg-white px-2 py-0.5 text-xs font-bold text-slate-800"><?= wishlist_quantity($_SESSION['id']) ?></span>
                    </a>
                    <?php endif; ?>
                    <a href="/order/history.php" class="inline-flex items-center gap-2 rounded-3xl border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition duration-150 hover:bg-slate-200">
                        <span>🧾</span>
                        <span class="hidden sm:inline">History</span>
                    </a>
                    <a href="../logout.php" class="inline-flex items-center gap-2 rounded-3xl border border-red-100 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 shadow-sm transition duration-150 hover:bg-red-100" title="Logout">
                        <span>🚪</span>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                </div>
            </div>

            <nav class="hidden md:flex items-center justify-center gap-4 text-sm font-medium text-slate-600">
                <a href="../index.php" class="rounded-full px-4 py-2 text-slate-700 transition duration-150 hover:bg-slate-100 hover:text-orange-500">Home</a>
                <a href="../index.php" class="rounded-full px-4 py-2 text-slate-700 transition duration-150 hover:bg-slate-100 hover:text-orange-500">About</a>
                <a href="../index.php" class="rounded-full px-4 py-2 text-slate-700 transition duration-150 hover:bg-slate-100 hover:text-orange-500">Products</a>
                <a href="../review.php?id=<?php echo e($query['user_id']); ?>" class="rounded-full px-4 py-2 text-slate-700 transition duration-150 hover:bg-slate-100 hover:text-orange-500">Review</a>
                <?php if (isset($_SESSION['id'])): ?>
                <a href="../wishlist.php" class="rounded-full px-4 py-2 text-slate-700 transition duration-150 hover:bg-slate-100 hover:text-pink-500">Wishlist</a>
                <?php endif; ?>
                <a href="../index.php" class="rounded-full px-4 py-2 text-slate-700 transition duration-150 hover:bg-slate-100 hover:text-orange-500">Location</a>
            </nav>

            <div class="hidden md:flex items-center justify-end gap-3">
                <?php if (!empty($query) && is_array($query)) : ?>
                <a href="../profile.php?id=<?php echo e($query['user_id']); ?>" title="Profile" class="group inline-flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 transition hover:border-orange-200 hover:bg-slate-100">
                    <img class="h-10 w-10 rounded-full object-cover border border-slate-200 shadow-xs transition duration-200" src="../uploaded_img/<?php echo e($query['image']); ?>" alt="User Avatar">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900"><?php echo e($query['username']); ?></p>
                        <p class="text-xs text-slate-500">My Account</p>
                    </div>
                </a>
                <?php else : ?>
                <a href="login.php" title="Login" class="inline-flex items-center rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-100">
                    Guest<span class="text-orange-500 ml-1">.</span>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <nav class="navbar hidden peer-checked:flex md:hidden flex-col gap-3 border-t border-slate-200 bg-white px-4 pb-4 pt-3 text-sm font-medium text-slate-600 shadow-lg">
            <a href="../index.php" class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 transition duration-150 hover:bg-orange-50 hover:text-orange-600">Home</a>
            <a href="../index.php" class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 transition duration-150 hover:bg-orange-50 hover:text-orange-600">About</a>
            <a href="../index.php" class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 transition duration-150 hover:bg-orange-50 hover:text-orange-600">Products</a>
            <a href="../review.php?id=<?php echo e($query['user_id']); ?>" class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 transition duration-150 hover:bg-orange-50 hover:text-orange-600">Review</a>
            <?php if (isset($_SESSION['id'])): ?>
            <a href="../wishlist.php" class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 transition duration-150 hover:bg-pink-50 hover:text-pink-600">Wishlist</a>
            <?php endif; ?>
            <a href="../index.php" class="rounded-2xl bg-slate-50 px-4 py-3 text-slate-700 transition duration-150 hover:bg-orange-50 hover:text-orange-600">Location</a>
        </nav>
    </header>