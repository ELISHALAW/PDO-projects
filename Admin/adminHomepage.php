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
        ['val' => countAllCustomer(), 'label' => 'Customers', 'icon' => '👥'],
        ['val' => countAllOrder(), 'label' => 'Orders', 'icon' => '📝'],
        ['val' => countAllUnits(), 'label' => 'Sum of Quantity', 'icon' => '🛒'],
        ['val' => 'RM' . countAllSubtotal(), 'label' => 'Income', 'icon' => '💰']
    ];
    foreach($stats as $stat): ?>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= $stat['val'] ?></h1>
                <span class="text-gray-500 text-sm"><?= $stat['label'] ?></span>
            </div>
            <div class="text-2xl"><?= $stat['icon'] ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b flex justify-between items-center">
            <h3 class="font-bold text-lg">Review</h3>
            <a href="reviewlist.php" class="text-blue-600 hover:underline">See All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Num</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Stars</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $num = 1; foreach($reviews as $review): ?>
                    <tr>
                        <td class="px-6 py-4"><?= $num++ ?></td>
                        <td class="px-6 py-4 font-medium"><?= e($review['name']) ?></td>
                        <td class="px-6 py-4 text-yellow-500"><?= str_repeat('⭐', $review['number_of_star'] ?? 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-lg">Customer</h3>
            <a href="reviewlist.php" class="text-blue-600 hover:underline">See All</a>
        </div>
        <div class="space-y-4">
            <?php foreach($totalCustomers as $totalCustomer) : ?>
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800"><?= e($totalCustomer['name']); ?></h4>
                        <small class="text-gray-400"><?= e($totalCustomer['username']); ?></small>
                    </div>
                    <a href="mailto:<?= htmlspecialchars($totalCustomer['email'] ?? ''); ?>" class="text-blue-500 text-xl">
                        <i class="las la-envelope"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>