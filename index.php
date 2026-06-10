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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computer Selling</title>
    <link rel="icon" type="image/png" href="images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            orange: '#ff7800',
                            dark: '#0f172a', // Slate 900
                            navy: '#020617'  // Slate 950
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 pt-20 antialiased">

    <header class="fixed inset-x-0 top-0 z-[1000] bg-white/90 backdrop-blur-2xl border-b border-slate-200/70 shadow-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 md:px-12">
            <div class="flex items-center gap-6">
                <a href="index.php" class="inline-flex items-center gap-3 text-lg font-extrabold text-slate-900">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-orange text-white shadow-lg shadow-brand-orange/20">CS</span>
                    <span class="tracking-tight">CompuTech</span>
                </a>

                <div class="md:hidden flex items-center gap-3">
                    <input type="checkbox" name="" id="toggler" class="hidden peer">
                    <label for="toggler" class="text-2xl cursor-pointer inline-flex text-slate-700 hover:text-brand-orange transition-colors">☰</label>
                </div>
            </div>

            <nav class="absolute top-full left-0 right-0 z-50 bg-white/90 backdrop-blur-xl border-t border-slate-200/70 flex-col gap-4 p-6 hidden peer-checked:flex shadow-2xl md:shadow-none md:border-0 md:p-0 md:static md:flex md:flex-row md:items-center md:gap-8 font-semibold text-sm tracking-wide uppercase text-slate-600">
                <a href="#home" class="hover:text-brand-orange transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-brand-orange">Home</a>
                <a href="#about" class="hover:text-brand-orange transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-brand-orange">About</a>
                <a href="#product" class="hover:text-brand-orange transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-brand-orange">Products</a>
                <a href="review.php?id=<?php echo e($query['user_id']); ?>" class="hover:text-brand-orange transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-brand-orange">Review</a>
                <a href="#location" class="hover:text-brand-orange transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-brand-orange">Location</a>
            </nav>

            <div class="hidden sm:flex items-center gap-5 font-semibold text-sm">
                <a href="/order/cart.php" class="bg-slate-100 text-slate-800 px-4 py-2 rounded-full hover:bg-brand-orange hover:text-white transition-all duration-300 flex items-center gap-1.5 shadow-sm">
                    <span>🛒</span> Cart <span class="bg-white/20 px-1.5 py-0.5 rounded-full text-xs font-bold"><?= cart_quantity() ?></span>
                </a>
                <a href="/order/history.php" class="text-slate-600 hover:text-brand-orange transition-colors inline-flex items-center gap-1">🧾 History</a>
                <a href="../logout.php" class="text-slate-500 hover:text-red-600 transition-colors inline-flex items-center gap-1" title="Logout">🚪 Logout</a>
            </div>

            <div class="sm:hidden">
                <a href="/order/cart.php" class="bg-slate-100 text-slate-800 px-3 py-2 rounded-full hover:bg-brand-orange hover:text-white transition-all duration-300 shadow-sm">🛒 <?= cart_quantity() ?></a>
            </div>
        </div>
    </header>
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-navy via-slate-950 to-slate-900 py-24" id="home">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_25%_20%,_rgba(255,120,0,0.18),_transparent_22%)] pointer-events-none"></div>
        <div class="absolute right-0 top-0 h-72 w-72 rounded-full bg-brand-orange/10 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-[1.1fr_0.9fr] items-center md:px-0">
            <div class="space-y-6">
                <span class="text-brand-orange text-xs font-semibold tracking-[0.35em] uppercase bg-white/10 px-4 py-2 rounded-full inline-block border border-white/10">Next-Gen Hardware</span>
                <h3 class="text-4xl md:text-6xl font-black uppercase tracking-tight leading-none text-white">Powerful <br><span class="bg-gradient-to-r from-white via-slate-200 to-brand-orange bg-clip-text text-transparent">Laptops</span></h3>
                <p class="text-lg md:text-xl text-slate-300 max-w-xl leading-relaxed">High performance meets masterfully engineered sleek designs. Experience top-tier processing power, stunning displays, and seamless multitasking with our latest laptop collection.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="product/list.php" class="inline-flex items-center justify-center bg-brand-orange text-white px-8 py-4 rounded-full font-bold shadow-[0_24px_80px_-42px_rgba(255,120,0,0.85)] hover:bg-white hover:text-brand-navy transition-all duration-300 transform hover:-translate-y-1">Shop Collection</a>
                    <a href="#about" class="inline-flex items-center justify-center border border-slate-700 bg-slate-950/70 text-slate-200 px-8 py-4 rounded-full font-bold hover:border-brand-orange hover:text-white transition-all duration-300">Learn More</a>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.32em] text-slate-400">
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2">Free delivery</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2">24/7 support</span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-2">Expert warranty</span>
                </div>
            </div>
            <div class="relative">
                <div class="relative overflow-hidden rounded-[42px] border border-white/10 bg-slate-950/80 shadow-2xl shadow-black/40 ring-1 ring-white/10">
                    <img src="../Homepage/customerImage/andras-vas-Bd7gNnWJBkU-unsplash.jpg" alt="Laptop showcase" class="w-full h-full min-h-[520px] object-cover transition-transform duration-700 hover:scale-105">
                    <div class="absolute inset-x-6 bottom-6 rounded-3xl border border-white/15 bg-slate-950/80 p-4 backdrop-blur-sm">
                        <span class="text-xs uppercase tracking-[0.3em] text-slate-300">Featured model</span>
                        <p class="mt-2 text-base font-semibold text-white">Modern performance. Sleek design.</p>
                    </div>
                </div>
                <div class="pointer-events-none absolute -right-10 top-12 hidden h-32 w-32 rounded-full bg-brand-orange/15 blur-3xl md:block"></div>
            </div>
        </div>
    </section>
    <section id="about" class="py-24 px-6 md:px-12 max-w-7xl mx-auto">
        <div class="text-center max-w-xl mx-auto mb-16">
            <h1 class="text-3xl md:text-4xl font-extrabold uppercase text-slate-900 tracking-tight">
                <span class="text-brand-orange">About</span> Us
            </h1>
            <div class="h-1 w-12 bg-brand-orange mx-auto mt-3 rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 relative rounded-2xl overflow-hidden shadow-2xl bg-black aspect-video group">
                <video src="video/latitude-5000-business-laptop.mp4" loop autoplay muted class="w-full h-full object-cover opacity-85 group-hover:scale-102 transition duration-700"></video>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-80"></div>
                <h3 class="absolute bottom-6 left-6 text-xl font-bold text-white tracking-wide">The Best-Selling Business Lineup</h3>
            </div>
            <div class="lg:col-span-5 space-y-6">
                <div class="inline-block bg-slate-100 text-slate-800 font-bold text-xs px-3 py-1 rounded-md uppercase tracking-wider">Premium Experience</div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">Why choose our solutions?</h3>
                <p class="text-slate-600 leading-relaxed text-base">We provide high-performance laptops packed with cutting-edge technology, sleek industrial profiles, and exceptional battery lifespans. Our carefully curated devices fit the needs of demanding gamers, corporate leaders, and professional creators.</p>
                <p class="text-slate-600 leading-relaxed text-base">Enjoy highly competitive pricing structures, verified local customer support channels, and an entirely hassle-free replacement warranty scheme.</p>
            </div>
        </div>
    </section>
    <section class="bg-slate-950/5 py-16 px-6 md:px-12 border-y border-slate-100">
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="group overflow-hidden rounded-[26px] border border-slate-200/40 bg-white/90 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-3xl bg-brand-orange/10 text-brand-orange text-xl font-bold shadow-inner">🚚</div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Free Delivery</h3>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-[0.3em]">On all local orders</p>
            </div>
            <div class="group overflow-hidden rounded-[26px] border border-slate-200/40 bg-white/90 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-3xl bg-brand-orange/10 text-brand-orange text-xl font-bold shadow-inner">🔒</div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Secure Payment</h3>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-[0.3em]">Fully protected gateways</p>
            </div>
            <div class="group overflow-hidden rounded-[26px] border border-slate-200/40 bg-white/90 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-3xl bg-brand-orange/10 text-brand-orange text-xl font-bold shadow-inner">🛠️</div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Expert Repair</h3>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-[0.3em]">Certified technicians</p>
            </div>
            <div class="group overflow-hidden rounded-[26px] border border-slate-200/40 bg-white/90 p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="mb-4 inline-flex h-14 w-14 items-center justify-center rounded-3xl bg-brand-orange/10 text-brand-orange text-xl font-bold shadow-inner">🎁</div>
                <h3 class="font-bold text-slate-900 text-base mb-2">Special Gifts</h3>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-[0.3em]">Premium bundle perks</p>
            </div>
        </div>
    </section>
    <section id="product" class="py-24 px-6 md:px-12 max-w-7xl mx-auto">
        <div class="text-center max-w-xl mx-auto mb-16">
            <h1 class="text-3xl md:text-4xl font-extrabold uppercase text-slate-900 tracking-tight">
                Latest <span class="text-brand-orange">Products</span>
            </h1>
            <div class="h-1 w-12 bg-brand-orange mx-auto mt-3 rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($products as $product): ?>
                <div class="group overflow-hidden rounded-[32px] border border-slate-200/80 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div class="relative overflow-hidden rounded-t-[32px] bg-slate-50 transition duration-500 group-hover:bg-slate-100">
                        <img src="../products/<?php echo e($product['image']); ?>" alt="<?php echo e($product['Product_name']); ?>" class="h-80 w-full object-contain transition-transform duration-500 group-hover:scale-105">
                        <span class="absolute top-4 left-4 rounded-full bg-brand-orange text-white px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.3em] shadow-sm">New</span>
                    </div>
                    <div class="flex flex-col gap-5 p-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 transition-colors duration-300 group-hover:text-brand-orange"><?php echo e($product['Product_name']); ?></h3>
                            <p class="mt-3 text-sm text-slate-500 leading-relaxed">High performance laptop for modern workflows and gaming.</p>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Price</span>
                                <div class="text-2xl font-black text-brand-dark">RM<?php echo e($product['price']); ?></div>
                            </div>
                            <button type="button" class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-slate-900 text-white transition-all duration-200 hover:bg-brand-orange hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="py-24 px-6 md:px-12 bg-slate-100" id="review">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-xl mx-auto mb-16">
                <h1 class="text-3xl md:text-4xl font-extrabold uppercase text-slate-900 tracking-tight">
                    Customer <span class="text-brand-orange">Reviews</span>
                </h1>
                <div class="h-1 w-12 bg-brand-orange mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($reviews as $review): ?>
                    <div class="bg-white p-8 rounded-[28px] shadow-sm border border-slate-200/60 flex flex-col justify-between relative hover:shadow-2xl transition-all duration-300">
                        <span class="absolute top-6 right-8 text-6xl text-slate-100 font-serif pointer-events-none select-none">“</span>
                        <div class="relative z-10">
                            <div class="flex items-center gap-1 text-amber-400 text-sm tracking-widest mb-4">
                                <?php 
                                if ($review['number_of_star'] == 5) echo "⭐⭐⭐⭐⭐";
                                else if ($review['number_of_star'] == 4) echo "⭐⭐⭐⭐";
                                else if ($review['number_of_star'] == 3) echo "⭐⭐⭐";
                                else if ($review['number_of_star'] == 2) echo "⭐⭐";
                                else if ($review['number_of_star'] == 1) echo "⭐";
                                ?>
                            </div>
                            <p class="text-slate-600 italic leading-relaxed text-sm">"<?php echo e($review['textarea']); ?>"</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold uppercase">
                                <?= substr(e($review['name']), 0, 1); ?>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm"><?php echo e($review['name']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <section id="location" class="py-24 px-6 text-center bg-white">
        <div class="text-center max-w-xl mx-auto mb-12">
            <h1 class="text-3xl md:text-4xl font-extrabold uppercase text-slate-900 tracking-tight">
                <span class="text-brand-orange">Our</span> Place
            </h1>
            <div class="h-1 w-12 bg-brand-orange mx-auto mt-3 rounded-full"></div>
        </div>

        <div class="w-full max-w-4xl mx-auto p-2 bg-slate-100 rounded-2xl shadow-inner border border-slate-200/40">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.537791254662!2d101.72398217447048!3d3.2152605527437395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc3843bfb6a031%3A0x2dc5e067aae3ab84!2sTunku%20Abdul%20Rahman%20University%20of%20Management%20and%20Technology%20(TAR%20UMT)!5e0!3m2!1sen!2sus!4v1745571985321!5m2!1sen!2sus" 
                class="w-full h-[320px] md:h-[450px] border-0 rounded-xl shadow-sm"
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>
    <footer class="bg-brand-dark text-slate-400 py-8 text-center text-xs tracking-wider border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="credit">Created by <span class="text-brand-orange font-bold hover:underline cursor-pointer">Law Seong Chun</span> | All rights reserved</div>
            <div class="flex gap-4 text-slate-500 uppercase font-semibold text-[10px]">
                <a href="#home" class="hover:text-white transition-colors">Privacy</a>
                <span>•</span>
                <a href="#home" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>
    </body>
</html>