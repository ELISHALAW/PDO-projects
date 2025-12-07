
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
</head>
<body>
    <?php
        require './_base.php';

        if(is_get()){
            $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);


            if($category_id === 1){
                echo "<h1>Asus</h1>";
            }else if($category_id === 2){
                echo "<h1>Dell</h1>";
            }else if($category_id === 3){
                echo "<h1>Huawei</h1>";
            }else if($category_id === 4){
                echo "<h1>Acer</h1>";
            }else{
                echo "<h1>Asus</h1>";
            }
        }
    ?>
</body>
</html>