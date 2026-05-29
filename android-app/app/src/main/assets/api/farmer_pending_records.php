<?php
declare(strict_types=1);
/**
 * GET — pending customer transactions for app Records page (farmer approve/dismiss).
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = read_json_body();
    $userId = (int)($body['user_id'] ?? 0);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = (int)($_GET['user_id'] ?? 0);
} else {
    json_fail('Method not allowed', 405);
}

if ($userId <= 0) {
    json_fail('user_id is required.', 400);
}

try {
    $pdo = db_conn();
    $chk = $pdo->prepare('SELECT farmer_id FROM farmers WHERE user_id = ? LIMIT 1');
    $chk->execute([$userId]);
    $farmerRow = $chk->fetch();
    if (!$farmerRow) {
        json_fail('Farmer profile not found. Complete registration first.', 404);
    }
    $farmerId = (int)$farmerRow['farmer_id'];

    $sql = "
        SELECT
          ct.*
        FROM customer_transaction ct
        WHERE ct.farmer_id = ?
          AND (
          SELECT th.status
          FROM transaction_history th
          WHERE th.customer_transaction_id = ct.customer_transaction_id
          ORDER BY th.transaction_history_id DESC
          LIMIT 1
        ) = 'pending'
        ORDER BY ct.customer_transaction_id DESC
        LIMIT 200
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$farmerId]);
    $rows = $stmt->fetchAll() ?: [];

    $records = [];
    foreach ($rows as $r) {
        $qty = (float)($r['quantity'] ?? 0);
        $unit = trim((string)($r['quantity_unit'] ?? 'KG')) ?: 'KG';
        $records[] = [
            'id' => 'tx-' . (int)$r['customer_transaction_id'],
            'customer_transaction_id' => (int)$r['customer_transaction_id'],
            'buyer' => trim((string)($r['buyer_name'] ?? '')),
            'product' => trim((string)($r['product'] ?? '')),
            'qty' => $qty == floor($qty) ? (string)(int)$qty : (string)$qty,
            'unit' => $unit,
            'amount' => (float)($r['amount'] ?? 0),
            'payment' => trim((string)($r['payment_method'] ?? 'Cash')) ?: 'Cash',
            'paymentAmount' => (float)($r['payment_amount'] ?? 0),
            'ref' => trim((string)($r['reference_no'] ?? '')),
            'status' => 'pending',
            'at' => (string)($r['transaction_date'] ?? ''),
            'transaction_type' => trim((string)($r['transaction_type'] ?? 'pickup')),
            'pickup_date' => trim((string)($r['pickup_date_display'] ?? $r['pickup_date'] ?? '')),
            'valid_id_path' => trim((string)($r['valid_id_path'] ?? '')),
            'valid_id_filename' => trim((string)($r['valid_id_filename'] ?? '')),
            'submitted_from' => trim((string)($r['submitted_from'] ?? '')),
            'client_form_json' => $r['client_form_json'] ?? null,
        ];
    }

    header('Access-Control-Allow-Origin: *');
    json_ok(['records' => $records, 'count' => count($records)]);
} catch (Throwable $e) {
    json_fail('farmer_pending_records failed: ' . $e->getMessage(), 500);
}
