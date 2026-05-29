<?php
declare(strict_types=1);
/**
 * GET — Client Web: poll transaction status (pending / approved / dismissed / sent_to_client).
 * ?reference_no=CW... or ?customer_transaction_id=123
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_fail('Method not allowed', 405);
}

$ref = trim((string)($_GET['reference_no'] ?? ''));
$txId = isset($_GET['customer_transaction_id']) ? (int)$_GET['customer_transaction_id'] : 0;

if ($ref === '' && $txId <= 0) {
    json_fail('reference_no or customer_transaction_id is required.', 400);
}

try {
    $pdo = db_conn();
    if ($txId > 0) {
        $stmt = $pdo->prepare(
            'SELECT customer_transaction_id, reference_no, buyer_name, product, quantity, amount,
                    payment_amount, payment_method, transaction_date, pickup_date, pickup_date_display,
                    quantity_unit
             FROM customer_transaction WHERE customer_transaction_id = ? LIMIT 1'
        );
        $stmt->execute([$txId]);
    } else {
        $stmt = $pdo->prepare(
            'SELECT customer_transaction_id, reference_no, buyer_name, product, quantity, amount,
                    payment_amount, payment_method, transaction_date, pickup_date, pickup_date_display,
                    quantity_unit
             FROM customer_transaction WHERE reference_no = ? LIMIT 1'
        );
        $stmt->execute([$ref]);
    }
    $row = $stmt->fetch();
} catch (Throwable $e) {
    try {
        $pdo = db_conn();
        if ($txId > 0) {
            $stmt = $pdo->prepare(
                'SELECT customer_transaction_id, reference_no, buyer_name, product, quantity, amount,
                        payment_amount, payment_method, transaction_date
                 FROM customer_transaction WHERE customer_transaction_id = ? LIMIT 1'
            );
            $stmt->execute([$txId]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT customer_transaction_id, reference_no, buyer_name, product, quantity, amount,
                        payment_amount, payment_method, transaction_date
                 FROM customer_transaction WHERE reference_no = ? LIMIT 1'
            );
            $stmt->execute([$ref]);
        }
        $row = $stmt->fetch();
    } catch (Throwable $e2) {
        json_fail('client_transaction_status failed: ' . $e2->getMessage(), 500);
    }
}

if (!$row) {
    json_fail('Transaction not found.', 404);
}

$id = (int)$row['customer_transaction_id'];
$hist = $pdo->prepare(
    'SELECT status, remarks, created_at
     FROM transaction_history
     WHERE customer_transaction_id = ?
     ORDER BY transaction_history_id DESC
     LIMIT 1'
);
$hist->execute([$id]);
$h = $hist->fetch() ?: ['status' => 'pending'];

$status = strtolower(trim((string)($h['status'] ?? 'pending')));
$pickupDisplay = trim((string)($row['pickup_date_display'] ?? ''));
$pickupRaw = (string)($row['pickup_date'] ?? '');
$pickupLabel = $pickupDisplay !== '' ? $pickupDisplay : $pickupRaw;
$qty = (float)($row['quantity'] ?? 0);
$payAmt = (float)($row['payment_amount'] ?? 0);
$amt = (float)($row['amount'] ?? 0);
$total = $amt > 0 ? $amt : ($payAmt > 0 ? $payAmt : 0);
$change = max(0, $payAmt - $total);
$unit = trim((string)($row['quantity_unit'] ?? 'KG'));
if ($unit === '') {
    $unit = 'KG';
}
$at = (string)($row['transaction_date'] ?? '');
$paymentMethod = trim((string)($row['payment_method'] ?? ''));
if ($paymentMethod === '') {
    $paymentMethod = 'Cash';
}
$refNo = (string)($row['reference_no'] ?? '');
$buyerName = (string)($row['buyer_name'] ?? '');
$productName = (string)($row['product'] ?? '');

header('Access-Control-Allow-Origin: *');
json_ok([
    'customer_transaction_id' => $id,
    'reference_no' => (string)($row['reference_no'] ?? ''),
    'status' => $status,
    'is_pending' => $status === 'pending',
    'is_approved' => $status === 'approved',
    'is_dismissed' => $status === 'dismissed',
    'is_sent_to_client' => $status === 'sent_to_client',
    'buyer_name' => (string)($row['buyer_name'] ?? ''),
    'product' => (string)($row['product'] ?? ''),
    'quantity' => $qty,
    'quantity_kg' => $qty,
    'amount' => $amt,
    'payment_amount' => $payAmt,
    'payment_method' => (string)($row['payment_method'] ?? ''),
    'pickup_date' => $pickupLabel,
    'pickup_date_display' => $pickupDisplay,
    'quantity_unit' => $unit,
    'total' => $total,
    'change' => $change,
    'transaction_at' => $at,
    'receipt' => [
        'ref' => $refNo,
        'reference_no' => $refNo,
        'buyer' => $buyerName,
        'buyer_name' => $buyerName,
        'pickup_date' => $pickupLabel,
        'product' => $productName,
        'qty' => $qty,
        'quantity_kg' => $qty,
        'unit' => $unit,
        'amount' => $total,
        'payment' => $paymentMethod,
        'payment_method' => $paymentMethod,
        'paymentAmount' => $payAmt,
        'payment_amount' => $payAmt,
        'total' => $total,
        'change' => $change,
        'at' => $at,
    ],
]);
