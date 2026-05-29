<?php
declare(strict_types=1);
/**
 * POST — send transaction receipt to client (buyer).
 * Saves customer_transaction + transaction_history (sent_to_client).
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();

try {
    $pdo = db_conn();
} catch (Throwable $e) {
    json_fail('Database unavailable: ' . $e->getMessage(), 503);
}

$userId = (int)($body['user_id'] ?? 0);
if ($userId <= 0) {
    json_fail('Missing account. Log in again.', 401);
}

$ref = trim((string)($body['ref'] ?? ''));
$buyer = trim((string)($body['buyer'] ?? ''));
$product = trim((string)($body['product'] ?? ''));
if ($ref === '' || $buyer === '') {
    json_fail('Receipt reference and buyer name are required.', 400);
}

$qtyRaw = trim((string)($body['qty'] ?? ''));
$qty = is_numeric($qtyRaw) ? (float)$qtyRaw : 0.0;
$amount = (float)($body['amount'] ?? $body['total'] ?? 0);
$paymentAmount = (float)($body['paymentAmount'] ?? 0);
$paymentMethod = trim((string)($body['payment'] ?? $body['paymentMethod'] ?? 'Cash'));
$atRaw = trim((string)($body['at'] ?? ''));
$txnDate = date('Y-m-d H:i:s');
if ($atRaw !== '') {
    $ts = strtotime($atRaw);
    if ($ts !== false) {
        $txnDate = date('Y-m-d H:i:s', $ts);
    }
}

try {
    $farmerSel = $pdo->prepare('SELECT farmer_id FROM farmers WHERE user_id = ? LIMIT 1');
    $farmerSel->execute([$userId]);
    $farmerRow = $farmerSel->fetch();
    if (!$farmerRow) {
        json_fail('Farmer profile not found. Complete registration first.', 404);
    }
    $farmerId = (int)$farmerRow['farmer_id'];

    $clientId = null;
    $clientName = $buyer;
    $clientSel = $pdo->prepare(
        'SELECT client_id, COALESCE(NULLIF(TRIM(company_name), ""), NULLIF(TRIM(full_name), "")) AS display_name
         FROM client
         WHERE TRIM(full_name) = ? OR TRIM(company_name) = ?
         LIMIT 1'
    );
    $clientSel->execute([$buyer, $buyer]);
    $clientRow = $clientSel->fetch();
    if ($clientRow) {
        $clientId = (int)$clientRow['client_id'];
        $clientName = trim((string)($clientRow['display_name'] ?? $buyer));
    }

    $pdo->beginTransaction();

    $txId = 0;
    $existing = $pdo->prepare(
        'SELECT customer_transaction_id FROM customer_transaction WHERE farmer_id = ? AND reference_no = ? LIMIT 1'
    );
    $existing->execute([$farmerId, $ref]);
    $exRow = $existing->fetch();

    if ($exRow) {
        $txId = (int)$exRow['customer_transaction_id'];
        $pdo->prepare(
            'UPDATE customer_transaction SET client_id = ?, buyer_name = ?, product = ?, quantity = ?,
             amount = ?, payment_amount = ?, payment_method = ?, transaction_date = ? WHERE customer_transaction_id = ?'
        )->execute([
            $clientId,
            $buyer,
            $product !== '' ? $product : 'Coffee',
            $qty,
            $amount,
            $paymentAmount,
            $paymentMethod !== '' ? $paymentMethod : null,
            $txnDate,
            $txId,
        ]);
    } else {
        $pdo->prepare(
            'INSERT INTO customer_transaction
              (farmer_id, client_id, buyer_name, product, quantity, amount, payment_amount, payment_method, reference_no, transaction_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $farmerId,
            $clientId,
            $buyer,
            $product !== '' ? $product : 'Coffee',
            $qty,
            $amount,
            $paymentAmount,
            $paymentMethod !== '' ? $paymentMethod : null,
            $ref,
            $txnDate,
        ]);
        $txId = (int)$pdo->lastInsertId();
    }

    $pdo->prepare(
        'INSERT INTO transaction_history (customer_transaction_id, status, remarks, changed_by_user_id)
         VALUES (?, ?, ?, ?)'
    )->execute([
        $txId,
        'sent_to_client',
        'Receipt sent to client: ' . $clientName,
        $userId,
    ]);

    $pdo->commit();

    header('Access-Control-Allow-Origin: *');
    json_ok([
        'message' => 'You have successfully sent the receipt.',
        'customer_transaction_id' => $txId,
        'client_name' => $clientName,
        'sent_to_client' => true,
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_fail('send_receipt failed: ' . $e->getMessage(), 500);
}
