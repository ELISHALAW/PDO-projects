<?php 
require __DIR__ . '/_base.php';
$errors = [];

$name = "";
$username = "";
$email = "";
$password = "";
$confirmPassword = "";
$phone_number = 0;
$address = "";

if(is_post()){
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmpassword'] ?? '';
    $phone_number = trim( (string) $_POST['phone_number']);
    $address = trim($_POST['address'] ?? '');

    if(empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword) || empty($phone_number) || empty($address)){
        $errors[] = "All fields are required.";
    }

    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Name can only contain alphabets and spaces.";
    }

    if (!preg_match("/^[a-zA-Z ]+$/", $username)) {
        $errors[] = "Username can only contain alphabets without spaces.";
    }

    if(strlen($username) < 3 || strlen($username) > 20){
        $errors[] = "Username must be between 3 and 20 characters.";
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format.";
    }

    if(preg_match("/^01[0-9]{1}-?[0-9]{7,8}$/", $phone_number)) {
        $errors[] = "The format of the phone number is invalid";
    }
    
    if (strlen($address) < 8) {
        $errors[] = "Address must be at least 8 characters long.";
    }

    if (!preg_match("/^[a-zA-Z0-9\s,.\-]+$/", $address)) {
        $errors[] = "Address contains invalid characters.";
    }

    if($password !== $confirmPassword){
        $errors[] = "Passwords do not match.";
    }

    if(strlen($password) < 8){
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check for existing username, email, or phone number
    if(empty($errors)){
        $stmt = $_db->prepare("SELECT * FROM user WHERE username=:username OR email=:email OR phone_number=:phone_number");
        $stmt->bindParam(':username', $username,PDO::PARAM_STR);
        $stmt->bindParam(':email', $email,PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $phone_number,PDO::PARAM_STR);
        $stmt->execute();
        $query = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($query)){
            $errors[] = "Username, Email, or Phone Number is already taken.";
        }
    }

    // If no errors, insert user
    if(empty($errors)){ 
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $_db->prepare("INSERT INTO user (`name`,`username`,`email`,`password`, `phone_number`, `address`) VALUES (:name,:username,:email,:password, :phone_number, :address)");
        $stmt->bindParam(":name", $name,PDO::PARAM_STR);
        $stmt->bindParam(":username", $username,PDO::PARAM_STR);
        $stmt->bindParam(":email", $email,PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashPassword,PDO::PARAM_STR);
        $stmt->bindParam(":phone_number", $phone_number,PDO::PARAM_STR);
        $stmt->bindParam(":address", $address,PDO::PARAM_STR);

        if($stmt->execute()){
            echo "<script>alert('Registration successful! Please log in'); window.location.href='login.php';</script>";
            exit;
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="form-container">
        <form action="registration.php" method="POST" autocomplete="off">
            <h2>Registration</h2>

            <?php displayError($errors); ?>    

            <?= inputField('text','name','Enter your name', $name) ?>

            <?= inputField('text','username','Enter your username', $username) ?>

            <?= inputField('email','email' ,'Enter your email', $email) ?>

            <?= inputField('text','phone_number' ,'Enter your phone number(eg:011-3390-3509)', $phone_number) ?>

            <?= inputField('text','address' ,'Enter your address', $address) ?>

            <?= inputField('password','password','Enter your password') ?>

            <?= inputField('password','confirmpassword','Confirm your password') ?>
            
            <?= html_submit('submit','submit','form-btn', 'Register') ?>
            
            <p>Already have an account? <a href="login.php">Login now</a></p>
        </form>
    </div>
</body>
</html>
