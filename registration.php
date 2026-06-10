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
      <link rel="icon" type="image/png" href="images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[url('../images/Laptop.jpg')] bg-cover bg-center bg-no-repeat min-h-screen flex items-center justify-center p-4 lg:p-8 antialiased relative">
    
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-xs pointer-events-none"></div>

    <div class="w-full max-w-2xl z-10 my-6">
        <form action="registration.php" method="POST" autocomplete="off" class="bg-white/90 backdrop-blur-md p-6 sm:p-10 rounded-2xl shadow-2xl border border-white/20 space-y-6">
            
            <div class="text-center space-y-1">
                <h2 class="text-3xl font-bold tracking-tight text-slate-800">Create An Account</h2>
                <p class="text-sm text-slate-500">Please fill out the form fields below to get started</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded text-sm text-red-700">
                    <?php displayError($errors); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 [&_input]:w-full [&_input]:px-4 [&_input]:py-2.5 [&_input]:border [&_input]:border-slate-200 [&_input]:rounded-xl [&_input]:text-slate-800 [&_input]:transition [&_input]:duration-200 [&_input]:focus:outline-none [&_input]:focus:ring-2 [&_input]:focus:ring-blue-500/20 [&_input]:focus:border-blue-500 [&_input]:bg-white/80">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <?= inputField('text','name','Enter your name', $name) ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                    <?= inputField('text','username','Enter your username', $username) ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <?= inputField('email','email' ,'Enter your email', $email) ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                    <?= inputField('text','phone_number' ,'e.g. 011-3390-3509', $phone_number) ?>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Home Address</label>
                    <?= inputField('text','address' ,'Enter your address', $address) ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <?= inputField('password','password','Enter your password') ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                    <?= inputField('password','confirmpassword','Confirm your password') ?>
                </div>
            </div>
            
            <div class="pt-2">
                <?= html_submit('submit','submit','w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-500/20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer text-center block', 'Register Now') ?>
            </div>
            
            <p class="text-center text-sm text-slate-600 pt-2">
                Already have an account? 
                <a href="login.php" class="font-semibold text-blue-600 hover:text-blue-500 hover:underline transition duration-150">Login now</a>
            </p>
        </form>
    </div>
</body>
</html>