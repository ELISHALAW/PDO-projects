<?php require __DIR__ . '/headandFoot/head.php';?>

<?php 

$id = $_GET['id'];
if(!$id){
    header("Location: reviewlist.php");
    exit();
}


$stmt = $_db->prepare("SELECT * FROM review WHERE review_id=:review_id");
$stmt->bindParam(":review_id",$id,PDO::PARAM_INT);
$stmt->execute();

$review = $stmt->fetch(PDO::FETCH_ASSOC);

?>


<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="bg-slate-800/40 backdrop-blur-md rounded-2xl shadow-xl border border-slate-700/60 overflow-hidden">
        
        <div class="bg-slate-800/60 px-8 py-6 border-b border-slate-700/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Reviewer Profile</span>
                <h1 class="text-xl font-bold text-white tracking-wide mt-0.5">
                    <?php echo e($review['name']); ?>
                </h1>
            </div>

            <div class="bg-slate-900/60 border border-slate-700/50 px-3 py-1.5 rounded-xl font-serif text-base text-amber-400 tracking-wide select-none self-start sm:self-auto">
                <?php echo str_repeat("⭐", (int)$review['number_of_star']); ?>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <div class="bg-slate-900/50 border border-slate-700/40 rounded-xl p-6 relative shadow-inner">
                <span class="absolute right-6 top-2 font-serif text-6xl text-slate-800/40 select-none pointer-events-none">”</span>
                
                <p class="text-slate-300 leading-relaxed text-sm italic relative z-10">
                    "<?php echo e($review['textarea']); ?>"
                </p>
            </div>

            <div class="flex items-center justify-end pt-2">
                <a href="reviewlist.php"
                   class="inline-flex items-center px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700/60 text-slate-300 hover:text-white font-semibold text-xs rounded-xl transition-all shadow-sm">
                    ← Return to Review Feed
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>