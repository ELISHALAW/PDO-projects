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


<div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-6">

    <div class="border-b pb-4 mb-4">
        <h1 class="text-3xl font-bold text-gray-800">
            <?php echo e($review['name']); ?>
        </h1>

        <p class="text-yellow-500 text-xl mt-2">
            <?php echo str_repeat("⭐", (int)$review['number_of_star']); ?>
        </p>
    </div>

    <div>
        <p class="text-gray-700 leading-relaxed">
            <?php echo e($review['textarea']); ?>
        </p>

        <a href="reviewlist.php"
           class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Review
        </a>
    </div>

</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>