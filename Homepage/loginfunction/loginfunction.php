<?php 
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }

    if(!empty($_SESSION['id'])){
        $id = $_SESSION['id'];
        $result = $_db->prepare("SELECT * FROM user WHERE user_id=:user_id");
        $result->bindParam(":user_id",$id,PDO::PARAM_INT);  
        $result->execute();
        $query = $result->fetch(PDO::FETCH_ASSOC);
    }
?>