<?php 
require __DIR__ . '/_base.php';
$errors = [];
$usernameEmail = isset( $_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : "";
$password = "";
if(is_post()){
    if(empty($_POST['usernameEmail']) || empty($_POST['password'])){
        $errors[]= "Both field are required";
    }else{
        $usernameEmail = trim($_POST['usernameEmail']);
        $password = $_POST['password'];

        if(!filter_var($usernameEmail,FILTER_VALIDATE_EMAIL)){
            $errors[] = "Invalid email format";
        }else{
            $stmt = $_db->prepare("SELECT * FROM user WHERE email=:usernameEmail");
            $stmt->bindParam(":usernameEmail",$usernameEmail,PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row){
                if(password_verify($password,$row['password'])){
                    $_SESSION['login'] = true;
                    $_SESSION['id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['status'] = $row['status'];

                    if(isset($_POST['remember'])){
                        setcookie('remember_email',$usernameEmail, time() + (7 * 24 * 60 * 60 ), "/");
                    }else{
                        setcookie('remember_email', '' , time() - 3600 , "/");
                    }

                    if($row['status'] === 'admin'){
                        header("Location: ./Admin/adminHomepage.php");
                    }else{
                        header("Location: index.php");
                    }
                    exit();
                }else{
                    $errors[] = "Incorrect password";
                }
            }else{
                $errors[] = "User not found";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=">
    <title>Login page</title>
    <link rel="stylesheet" href="./css/login.css">
   
</head>
<body>
   
    <div class="form-container">
        <form action="login.php" method="POST">
        <h2>Login</h2>
        <?php displayError($errors); ?>  
        <div class="form-checkbox">
       <label for="remember" style="display:inline-flex; align-items:center;"> <?= checkbox('remember', isset($_POST['remember']) || isset($_COOKIE['remember_email'])) ?><span class="text">Remember me</span></label>
        </div>
        <?= inputField('email','usernameEmail' ,'example@gmail.com',$usernameEmail ) ?><br>

        <?= inputField('password','password','Enter your password') ?><br>

        <?= html_submit('submit','submit','form-btn' ,'Login') ?>

        <p>Don't have an account? <a href="registration.php">Register now</a></p>
        <a href="confirmEmail.php">Forgot password?</a> 
    </form>
    </div>
</body>
</html>