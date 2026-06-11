<?php
require_once '../_base.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Order ID is required. <a href="history.php">Back to orders</a>');
}

$orderId = (int)$_GET['id'];

$stmt = $_db->prepare('SELECT o.order_id, o.total, u.email, u.username FROM orders o JOIN user u ON o.user_id = u.user_id WHERE o.order_id = ? AND o.user_id = ?');
$stmt->execute([$orderId, $_SESSION['id']]);
$order = $stmt->fetch(PDO::FETCH_OBJ);

if (!$order) {
    die('Order not found or not authorized. <a href="history.php">Back to orders</a>');
}

$stripePublishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
if (!$stripePublishableKey) {
    die('Stripe publishable key is not configured.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment - Order #<?= htmlspecialchars($orderId) ?></title>
      <link rel="icon" type="image/png" href="../images/computer.webp">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .StripeElement {
            box-sizing: border-box;
            height: 44px;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            background-color: white;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.35);
        }
        .StripeElement--invalid {
            border-color: #f87171;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4">
    <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl border border-slate-200 p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Stripe Payment</h1>
            <p class="text-slate-500">Order #<?= htmlspecialchars($orderId) ?> · RM <?= number_format((float)$order->total, 2) ?></p>
        </div>

        <div id="payment-message" class="hidden text-center text-sm font-medium mb-4"></div>

        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 mb-6">
            <label class="block text-sm font-semibold text-slate-600 mb-3">Card details</label>
            <div id="card-element" class="StripeElement"></div>
            <div id="card-errors" class="mt-3 text-sm text-red-600 hidden"></div>
        </div>

        <button id="pay-button" class="w-full rounded-3xl bg-blue-600 text-white font-semibold py-4 shadow-lg shadow-blue-500/10 transition hover:bg-blue-700">Pay RM <?= number_format((float)$order->total, 2) ?></button>

        <p class="mt-4 text-sm text-slate-500">After payment, you will be redirected to your order detail page.</p>
    </div>

    <script>
        const stripe = Stripe('<?= htmlspecialchars($stripePublishableKey) ?>');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const payButton = document.getElementById('pay-button');
        const cardErrors = document.getElementById('card-errors');
        const paymentMessage = document.getElementById('payment-message');

        cardElement.on('change', (event) => {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.classList.remove('hidden');
            } else {
                cardErrors.textContent = '';
                cardErrors.classList.add('hidden');
            }
        });

        payButton.addEventListener('click', async () => {
            payButton.disabled = true;
            payButton.textContent = 'Processing...';

            const createResponse = await fetch('stripe-handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'create_intent', order_id: <?= $orderId ?> })
            });

            const createData = await createResponse.json();
            if (!createResponse.ok || createData.error) {
                cardErrors.textContent = createData.error || 'Unable to create payment intent.';
                cardErrors.classList.remove('hidden');
                payButton.disabled = false;
                payButton.textContent = 'Pay RM <?= number_format((float)$order->total, 2) ?>';
                return;
            }

            const result = await stripe.confirmCardPayment(createData.clientSecret, {
                payment_method: {
                    card: cardElement
                }
            });

            if (result.error) {
                cardErrors.textContent = result.error.message;
                cardErrors.classList.remove('hidden');
                payButton.disabled = false;
                payButton.textContent = 'Pay RM <?= number_format((float)$order->total, 2) ?>';
                return;
            }

            if (result.paymentIntent.status === 'succeeded') {
                const confirmResponse = await fetch('stripe-handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'confirm_payment',
                        payment_intent_id: result.paymentIntent.id,
                        order_id: <?= $orderId ?>
                    })
                });
                const confirmData = await confirmResponse.json();
                if (confirmData.success) {
                    paymentMessage.textContent = 'Payment successful. Redirecting...';
                    paymentMessage.className = 'text-center text-sm font-medium text-emerald-600 mb-4';
                    paymentMessage.classList.remove('hidden');
                    setTimeout(() => window.location.href = '../index.php', 1800);
                    return;
                }
                cardErrors.textContent = confirmData.message || 'Payment confirmation failed.';
                cardErrors.classList.remove('hidden');
                payButton.disabled = false;
                payButton.textContent = 'Pay RM <?= number_format((float)$order->total, 2) ?>';
            }
        });
    </script>
</body>
</html>