<?php
require '_base.php';

$name = "";
$errors = [];
$message = "";
$rating = "";
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (is_post()) {
    $name = $_POST['name'];
    $number = $_POST['rating'];
    $message = $_POST['message'];

    // Validation
    if (empty($name) || empty($number) || empty($message)) {
        $errors[] = "All fields are required"; 
    }

    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Name can only contain alphabets and spaces.";
    }

    $allowedRatings = [1, 2, 3, 4, 5];
    if (empty($number) || !in_array($number, $allowedRatings)) {
        $errors[] = "Please select a valid rating";
    }

    // Validate user_id
    if ($user_id <= 0) {
        $errors[] = "Invalid user ID";
    }

    if (empty($errors)) {
        // Insert into review table, including user_id
        $stmt = $_db->prepare("INSERT INTO review (name, textarea, number_of_star, user_id) VALUES (:name, :message, :number, :user_id)");
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        $stmt->bindParam(":message", $message, PDO::PARAM_STR);
        $stmt->bindParam(":number", $number, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Rating added'); window.location.href='index.php';</script>";
        } else {
            $errors[] = "Failed to add rating. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .review-container {
            max-width: 400px;
            background-color: #ffffff;
            padding: 30px;
            margin: 150px auto;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-review {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
            color: #333;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            resize: none;
        }

        button {
            padding: 12px;
            background-color: crimson;
            color: white;
            border: none;
            width: 100%;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: red;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <form action="review.php?id=<?php echo htmlspecialchars($user_id); ?>" method="post">
        <div class="review-container">
            <h2 style="text-align:center;">Review Section</h2>

            <div style="text-align:center;">
                <?php echo displayError($errors); ?>
            </div>

            <div class="form-review">
                <?= inputField('text', 'name', 'Please enter your name', $name, '') ?><br>
            </div>

            <div class="form-review">
                <?= html_textarea('message', 'message', '4', '30', 'Please enter your message', $message) ?>
            </div>

            <div class="form-review">
                <?= html_select_range('rating', 'rating', '1', '5', 'Star') ?><br>
            </div>

            <button type="submit">Submit</button>
            <a href="index.php" class="back-link">Back to homepage</a>
        </div>
    </form>
</body>
</html>