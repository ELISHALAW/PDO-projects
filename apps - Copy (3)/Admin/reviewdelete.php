<?php 
require '../_base.php';

if(is_post() && isset($_POST['review_id']) && is_numeric($_POST['review_id'])){
    $review_id = $_POST['review_id'];

    $stmt = $_db->prepare("DELETE FROM review WHERE review_id=:review_id");
    $stmt->bindParam(':review_id',$review_id,PDO::PARAM_INT);

    if($stmt->execute()){
        header("Location: reviewlist.php?message=Review deleted successfully");
        exit();
    }else{
        echo "Error deleting user";
    }
}else{
    echo "Invalid request" ;
}
?>