<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once '../_base.php';

use Stripe\Stripe;
use Stripe\PaymentIntent;

// Load Stripe key from environment
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
if (!$stripeSecretKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Stripe secret key is not configured']);
    exit;
}

Stripe::setApiKey($stripeSecretKey);

function ensureStripeOrderColumns()
{
    global $_db;

    $columns = [
        'status' => "VARCHAR(50) DEFAULT 'pending'",
        'payment_method' => 'VARCHAR(50) DEFAULT NULL',
        'payment_id' => 'VARCHAR(255) DEFAULT NULL',
    ];

    foreach ($columns as $name => $definition) {
        $stmt = $_db->prepare('SHOW COLUMNS FROM orders LIKE ?');
        $stmt->execute([$name]);
        if (!$stmt->fetch()) {
            $_db->exec("ALTER TABLE orders ADD COLUMN $name $definition");
        }
    }
}

ensureStripeOrderColumns();

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? null;

if ($action === 'create_intent') {
    $orderId = isset($input['order_id']) ? (int)$input['order_id'] : 0;
    if ($orderId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid order ID']);
        exit;
    }

    $stmt = $_db->prepare('SELECT o.total, u.email FROM orders o JOIN user u ON o.user_id = u.user_id WHERE o.order_id = ?');
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    $amount = (int)round($order->total * 100);
    $intent = PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'myr',
        'receipt_email' => $order->email,
        'metadata' => ['order_id' => $orderId],
    ]);

    echo json_encode(['clientSecret' => $intent->client_secret]);
    exit;
}

if ($action === 'confirm_payment') {
    $paymentIntentId = $input['payment_intent_id'] ?? null;
    $orderId = isset($input['order_id']) ? (int)$input['order_id'] : 0;

    if (!$paymentIntentId || $orderId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing payment intent ID or order ID']);
        exit;
    }

    $intent = PaymentIntent::retrieve($paymentIntentId);
    if ($intent->status !== 'succeeded') {
        echo json_encode(['success' => false, 'message' => 'Payment not completed', 'status' => $intent->status]);
        exit;
    }

    $stmt = $_db->prepare('UPDATE orders SET status = ?, payment_method = ?, payment_id = ? WHERE order_id = ?');
    $stmt->execute(['paid', 'stripe', $paymentIntentId, $orderId]);

    echo json_encode(['success' => true, 'message' => 'Payment successful']);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
