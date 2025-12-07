<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Touch 'n Go Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .payment-container img {
            max-width: 300px;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .payment-container h2 {
            color: #6064b6;
            margin-bottom: 10px;
        }
        .payment-container p {
            color: #555;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="payment-container">
    <h2>Scan to Pay with Touch 'n Go</h2>
    <h2>Your order had been inserted</h2>
    <img src="/paymentimg/touchngo.jpg" alt="Touch 'n Go QR Code">
    <p>Please scan the QR code above using your eWallet or banking app to make payment.</p>
    <a href="../index.php">Back to Homepage</a>
</div>

</body>
</html>