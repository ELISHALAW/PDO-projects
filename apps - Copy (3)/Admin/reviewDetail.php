<?php require __DIR__ . '/headandFoot/head.php';?>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 0;
}

.table-container {
    max-height: 1000px   ;
    max-width: 900px;
    margin: 50px auto;
    background-color: #ffffff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    display: flex;
}

.inputName {
    margin-top: 20px;
    text-align: center;
}

.inputName h1 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #333;
}

.inputName p {
    font-size: 20px;
    color: #ffcc00;
    letter-spacing: 2px;
}

.inputReview {
    font-size: 16px;
    width: 600px;
    line-height: 1.8;
    color: #444;
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
}
</style>

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


<div class="table-container">
    <div class="inputName">
        <h1><?php echo e($review['name']); ?></h1>
        <p><?php echo str_repeat("⭐", (int)$review['number_of_star']); ?></p>
    </div>

    <div class="inputReview">
        <p><?php echo e($review['textarea']); ?> <a href="reviewlist.php">Back to review</a></p>
    </div>

</div>

<?php require __DIR__ . '/headandFoot/foot.php'; ?>