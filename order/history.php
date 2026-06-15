<?php
include '../head.php';

// ----------------------------------------------------------------------------

// (1) Authorization (member)
if (!isset($_SESSION['id'])) {
    echo "<script>alert('Please log in'); window.location.href='../login.php';</script>";
    exit();
}

// (2) Return orders belonging to the user (descending) with pagination
$perPage = 10; // change this to adjust items per page
$page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = ($page - 1) * $perPage;

// total count
$stm = $_db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = :uid');
$stm->execute([':uid' => $_SESSION['id']]);
$total = (int)$stm->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

// fetch page
$stm = $_db->prepare('SELECT * FROM orders WHERE user_id = :uid ORDER BY order_id DESC LIMIT :limit OFFSET :offset');
$stm->bindValue(':uid', $_SESSION['id'], PDO::PARAM_INT);
$stm->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stm->bindValue(':offset', $offset, PDO::PARAM_INT);
$stm->execute();
$arr = $stm->fetchAll();

// ----------------------------------------------------------------------------

$_title = 'Order | History';

?>
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-[0_20px_60px_rgba(15,23,42,0.08)]">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-indigo-600">Order History</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Your purchase history</h1>
                <p class="mt-3 max-w-2xl text-sm text-slate-500">Browse recent orders, review totals, and inspect product thumbnails with a modern, responsive layout.</p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 shadow-sm"><?= $total ?> records • page <?= $page ?> of <?= $totalPages ?></div>
                <button data-post="reset.php" data-confirm class="inline-flex items-center justify-center rounded-3xl bg-gradient-to-r from-indigo-600 to-violet-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-200/30 transition duration-200 hover:-translate-y-0.5 hover:shadow-violet-300/40">Reset</button>
            </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-[26px] border border-slate-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Order</th>
                            <th class="px-5 py-4 font-semibold">Date</th>
                            <th class="px-5 py-4 text-right font-semibold">Items</th>
                            <th class="px-5 py-4 text-right font-semibold">Total (RM)</th>
                            <th class="px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white text-slate-700">
                        <?php foreach ($arr as $o): ?>
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">#<?= $o->order_id ?></td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-500"><?= date('d M Y H:i', strtotime($o->date)) ?></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700"><?= $o->count ?></td>
                                <td class="whitespace-nowrap px-5 py-4 text-right text-slate-900">RM <?= number_format((float)$o->total, 2) ?></td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                                        <a href="detail.php?id=<?= $o->order_id ?>&p=<?= $page ?>" class="inline-flex items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition duration-150 hover:bg-indigo-700">View details</a>
                                        <div class="flex w-full flex-wrap items-center gap-3 overflow-x-auto py-1 sm:w-auto">
                                            <?php
                                            $stm = $_db->prepare('SELECT p.image FROM order_item AS i, product AS p WHERE i.product_id = p.product_id AND i.order_id = ?');
                                            $stm->execute([$o->order_id]);
                                            $photos = $stm->fetchAll(PDO::FETCH_COLUMN);
                                            foreach ($photos as $photo) {
                                                echo '<img src="/products/' . htmlspecialchars($photo, ENT_QUOTES, 'UTF-8') . '" alt="Product image" class="h-14 w-14 rounded-3xl border border-slate-200 object-cover shadow-sm">';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <?php if ($page > 1): ?>
                    <a href="history.php?p=<?= $page-1 ?>" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition duration-150 hover:bg-slate-50">← Prev</a>
                <?php endif ?>

                <?php
                $start = max(1, $page - 3);
                $end = min($totalPages, $page + 3);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <?php if ($i == $page): ?>
                        <span class="inline-flex h-11 items-center justify-center rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white"><?= $i ?></span>
                    <?php else: ?>
                        <a href="history.php?p=<?= $i ?>" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition duration-150 hover:bg-slate-50"><?= $i ?></a>
                    <?php endif ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="history.php?p=<?= $page+1 ?>" class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition duration-150 hover:bg-slate-50">Next →</a>
                <?php endif ?>
            </div>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <a href="../index.php" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-700 transition duration-150 hover:bg-slate-100">← Back to Homepage</a>
        </div>
    </div>
</div>

<?php
include '../foot.php';
