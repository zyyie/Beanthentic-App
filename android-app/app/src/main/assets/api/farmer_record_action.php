<?php
declare(strict_types=1);
/**
 * POST — approve or dismiss a pending record (app Records page).
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
$userId = (int)($body['user_id'] ?? 0);
$txId = (int)($body['customer_transaction_id'] ?? 0);
$action = strtolower(trim((string)($body['action'] ?? '')));

if ($userId <= 0) {
    json_fail('user_id is required.', 400);
}
if ($txId <= 0) {
    json_fail('customer_transaction_id is required.', 400);
}
if (!in_array($action, ['approve', 'dismiss'], true)) {
    json_fail('action must be approve or dismiss.', 400);
}

$status = $action === 'approve' ? 'approved' : 'dismissed';
$remarks = $action === 'approve' ? 'Approved by farmer in app' : 'Dismissed by farmer in app';

try {
    $pdo = db_conn();
    $chk = $pdo->prepare('SELECT farmer_id FROM farmers WHERE user_id = ? LIMIT 1');
    $chk->execute([$userId]);
    $farmerRow = $chk->fetch();
    if (!$farmerRow) {
        json_fail('Farmer profile not found.', 404);
    }
    $farmerId = (int)$farmerRow['farmer_id'];

    $tx = $pdo->prepare(
        'SELECT customer_transaction_id FROM customer_transaction
         WHERE customer_transaction_id = ? AND farmer_id = ? LIMIT 1'
    );
    $tx->execute([$txId, $farmerId]);
    if (!$tx->fetch()) {
        json_fail('Transaction not found for this farmer account.', 404);
    }

    $pdo->prepare(
        'INSERT INTO transaction_history (customer_transaction_id, status, remarks, changed_by_user_id)
         VALUES (?, ?, ?, ?)'
    )->execute([$txId, $status, $remarks, $userId]);

    header('Access-Control-Allow-Origin: *');
    json_ok([
        'customer_transaction_id' => $txId,
        'status' => $status,
    ]);
} catch (Throwable $e) {
    json_fail('farmer_record_action failed: ' . $e->getMessage(), 500);
}
