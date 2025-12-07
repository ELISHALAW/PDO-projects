
<?php 
    require './db_folder/config.php';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $brand = $_POST['brand'];
        $cost = $_POST['cost'];
        $detail = $_POST['detail'];

        $target_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check == false){
            die("File is not an image");
        }

        $allowed_types = ["jpg","png","jpeg","gif"];

        if(!in_array($imageFileType,$allowed_types)){
            die("Only jpg,jpeg,png & GIF files are allowed");
        }

        if(move_uploaded_file($_FILES["image"]["tmp_name"],$target_file)){
            try{
                $stmt = $_db->prepare("INSERT INTO product (brand, cost, image, detail) VALUES (:brand,:cost,:image,:detail)");
                $stmt->bindParam(":brand",$brand,PDO::PARAM_STR);
                $stmt->bindParam(":cost",$cost,PDO::PARAM_INT);
                $stmt->bindParam(":image",$image_name,PDO::PARAM_STR);
                $stmt->bindParam(":detail",$detail,PDO::PARAM_STR);


                $stmt->execute();

                echo "<script>alert('Product added successfully');window.location.href='productAdmin.php';</script>";
            }catch(PDOException $e){
                echo("Insert failed". $e->getMessage());
            }
        }else{
            echo "Error uploading image.";
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
</head>
<body>

    <h2>Product Form</h2>
    <form action="createproduct.php" method="POST" enctype="multipart/form-data">
        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand" required><br><br>

        <label for="cost">Cost:</label>
        <input type="number" id="cost" name="cost" required><br><br>

        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <label for="detail">Detail:</label><br>
        <textarea id="detail" name="detail" rows="4" cols="30" required style="resize:none;"></textarea><br><br>

        <button type="submit" name="submit">Submit</button>
    </form>

</body>
</html>


