<?php 
require '../_base.php';

if(is_post() && isset($_POST['product_id']) && is_numeric($_POST['product_id'])){
    $product_id = $_POST['product_id'];

    $stmt = $_db->prepare("DELETE FROM product WHERE product_id=:product_id");
    $stmt->bindParam(':product_id',$product_id,PDO::PARAM_INT);

    if($stmt->execute()){
        header("Location: productlist.php?message=User deleted successfully");
        exit();
    }else{
        echo "Error deleting user";
    }
}else{
    echo "Invalid request" ;
}
?>