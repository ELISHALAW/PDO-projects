<?php 

require __DIR__ . '/headandFoot/head.php';

$stmt = $_db->prepare("SELECT * FROM review");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);



$totalStmt = $_db->prepare("SELECT COUNT(*) AS total_reviews FROM review");
$totalStmt->execute();
$totalReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$num = 0;

$customerStmt = $_db->prepare('SELECT * FROM user LIMIT 5');
$customerStmt->execute();
$totalCustomers = $customerStmt->fetchAll(PDO::FETCH_ASSOC);


?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php 
    $stats = [
        ['val' => countAllCustomer(), 'label' => 'Customers', 'icon' => '👥', 'color' => 'from-blue-500/20 to-transparent'],
        ['val' => countAllOrder(), 'label' => 'Orders', 'icon' => '📝', 'color' => 'from-emerald-500/20 to-transparent'],
        ['val' => countAllUnits(), 'label' => 'Sum of Quantity', 'icon' => '🛒', 'color' => 'from-amber-500/20 to-transparent'],
        ['val' => 'RM ' . number_format(countAllSubtotal(), 2), 'label' => 'Income', 'icon' => '💰', 'color' => 'from-purple-500/20 to-transparent']
    ];
    foreach($stats as $stat): ?>
        <div class="bg-slate-800/50 backdrop-blur-md p-6 rounded-xl border border-slate-700/60 flex items-center justify-between shadow-lg hover:border-slate-600 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r <?= $stat['color'] ?> opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            <div class="relative z-10">
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider block mb-1"><?= $stat['label'] ?></span>
                <h1 class="text-3xl font-extrabold text-white tracking-tight"><?= $stat['val'] ?></h1>
            </div>
            <div class="text-3xl bg-slate-700/50 p-3 rounded-lg relative z-10 group-hover:scale-110 transition-transform duration-300">
                <?= $stat['icon'] ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 bg-slate-800/40 backdrop-blur-md rounded-xl border border-slate-700/60 shadow-lg overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-5 border-b border-slate-700/60 flex justify-between items-center bg-slate-800/20">
                <h3 class="font-bold text-lg text-white tracking-wide">Recent Reviews</h3>
                <a href="reviewlist.php" class="text-xs font-semibold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-wider">See All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase text-xs tracking-wider font-semibold border-b border-slate-700/40">
                        <tr>
                            <th class="px-6 py-3.5 w-16 text-center">Num</th>
                            <th class="px-6 py-3.5">Customer Name</th>
                            <th class="px-6 py-3.5 text-right pr-12">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40 text-slate-300">
                        <?php $num = 1; foreach($reviews as $review): ?>
                        <tr class="hover:bg-slate-700/20 transition-colors group">
                            <td class="px-6 py-4 text-center font-mono text-slate-500 group-hover:text-slate-400"><?= $num++ ?></td>
                            <td class="px-6 py-4 font-medium text-slate-200 group-hover:text-white transition-colors"><?= e($review['name']) ?></td>
                            <td class="px-6 py-4 text-right pr-12 text-sm tracking-wide text-amber-400">
                                <?= str_repeat('★', $review['number_of_star'] ?? 0); ?><span class="text-slate-700"><?= str_repeat('★', 5 - ($review['number_of_star'] ?? 0)); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-slate-800/40 backdrop-blur-md rounded-xl border border-slate-700/60 shadow-lg p-5 flex flex-col">
        <div class="flex justify-between items-center mb-5 pb-4 border-b border-slate-700/60">
            <h3 class="font-bold text-lg text-white tracking-wide">New Customers</h3>
            <a href="customerlist.php" class="text-xs font-semibold text-blue-400 hover:text-blue-300 transition-colors uppercase tracking-wider">See All</a>
        </div>
        <div class="space-y-4 overflow-y-auto max-h-[360px] pr-1 scrollbar-thin scrollbar-thumb-slate-700">
            <?php foreach($totalCustomers as $totalCustomer) : ?>
                <div class="flex items-center justify-between p-3 rounded-lg bg-slate-800/30 border border-slate-700/30 hover:border-slate-600/50 hover:bg-slate-800/60 transition-all group">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-slate-700 text-slate-200 flex items-center justify-center font-bold text-sm uppercase group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <?= mb_substr(e($totalCustomer['name']), 0, 1); ?>
                        </div>
                        <div>
                            <h4 class="font-medium text-slate-200 group-hover:text-white transition-colors text-sm"><?= e($totalCustomer['name']); ?></h4>
                            <span class="text-xs text-slate-400">@<?= e($totalCustomer['username']); ?></span>
                        </div>
                    </div>
                    <a href="mailto:<?= htmlspecialchars($totalCustomer['email'] ?? ''); ?>" 
                       class="p-2 rounded-md bg-slate-700/40 text-slate-400 hover:text-blue-400 hover:bg-slate-700/80 transition-all"
                       title="Email Customer">
                        <i class="las la-envelope text-lg"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>