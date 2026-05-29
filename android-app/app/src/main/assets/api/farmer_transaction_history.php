<?php
declare(strict_types=1);
/**
 * POST — approved / sent transactions for app Transaction History (from MySQL).
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
        json_fail('Farmer profile not found.', 404);
    }
    $farmerId = (int)$farmerRow['farmer_id'];

    $sql = "
        SELECT
          ct.customer_transaction_id,
          ct.buyer_name,
          ct.product,
          ct.quantity,
          ct.amount,
          ct.payment_amount,
          ct.payment_method,
          ct.reference_no,
          ct.transaction_date,
          (
            SELECT th.status
            FROM transaction_history th
            WHERE th.customer_transaction_id = ct.customer_transaction_id
            ORDER BY th.transaction_history_id DESC
            LIMIT 1
          ) AS current_status,
          (
            SELECT th.created_at
            FROM transaction_history th
            WHERE th.customer_transaction_id = ct.customer_transaction_id
              AND th.status = 'approved'
            ORDER BY th.transaction_history_id ASC
            LIMIT 1
          ) AS approved_at
        FROM customer_transaction ct
        WHERE ct.farmer_id = ?
          AND (
          SELECT th.status
          FROM transaction_history th
          WHERE th.customer_transaction_id = ct.customer_transaction_id
          ORDER BY th.transaction_history_id DESC
          LIMIT 1
        ) IN ('approved', 'sent_to_client')
        ORDER BY COALESCE(approved_at, ct.transaction_date) DESC, ct.customer_transaction_id DESC
        LIMIT 300
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$farmerId]);
    $rows = $stmt->fetchAll() ?: [];

    $records = [];
    foreach ($rows as $r) {
        $qty = (float)($r['quantity'] ?? 0);
        $status = strtolower(trim((string)($r['current_status'] ?? 'approved')));
        $at = $r['approved_at'] ?? $r['transaction_date'] ?? null;
        $records[] = [
            'customer_transaction_id' => (int)$r['customer_transaction_id'],
            'buyer' => trim((string)($r['buyer_name'] ?? '')),
            'product' => trim((string)($r['product'] ?? '')),
            'variety' => strtolower(trim((string)($r['product'] ?? ''))),
            'qty' => $qty == floor($qty) ? (string)(int)$qty : (string)$qty,
            'unit' => 'KG',
            'amount' => (float)($r['amount'] ?? 0),
            'payment' => trim((string)($r['payment_method'] ?? 'Cash')) ?: 'Cash',
            'paymentAmount' => (float)($r['payment_amount'] ?? 0),
            'total' => (float)($r['amount'] ?? 0),
            'change' => max(0, (float)($r['payment_amount'] ?? 0) - (float)($r['amount'] ?? 0)),
            'ref' => trim((string)($r['reference_no'] ?? '')),
            'at' => $at ? (string)$at : date('Y-m-d H:i:s'),
            'sentToClient' => $status === 'sent_to_client',
            'sentAt' => $status === 'sent_to_client' ? (string)$at : null,
            'status' => $status,
        ];
    }

    header('Access-Control-Allow-Origin: *');
    json_ok(['records' => $records, 'count' => count($records)]);
} catch (Throwable $e) {
    json_fail('farmer_transaction_history failed: ' . $e->getMessage(), 500);
}
