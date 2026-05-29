<?php
declare(strict_types=1);
/**
 * GET — Admin dashboard: all approved/sent customer transactions (same as app History).
 * ?limit=500&farmer_id=12 (optional)
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

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 500;
$limit = max(1, min($limit, 800));
$farmerId = isset($_GET['farmer_id']) ? (int)$_GET['farmer_id'] : 0;

try {
    $pdo = db_conn();
} catch (Throwable $e) {
    json_fail('Database unavailable: ' . $e->getMessage(), 503);
}

$sql = "
    SELECT
      ct.customer_transaction_id,
      ct.farmer_id,
      ct.buyer_name,
      ct.product,
      ct.quantity,
      ct.amount,
      ct.payment_amount,
      ct.payment_method,
      ct.reference_no,
      ct.transaction_date,
      f.farm_code,
      u.username,
      u.phone_number,
      pi.first_name,
      pi.last_name,
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
    LEFT JOIN farmers f ON f.farmer_id = ct.farmer_id
    LEFT JOIN users u ON u.user_id = f.user_id
    LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
    WHERE (
      SELECT th.status
      FROM transaction_history th
      WHERE th.customer_transaction_id = ct.customer_transaction_id
      ORDER BY th.transaction_history_id DESC
      LIMIT 1
    ) IN ('approved', 'sent_to_client')
";
$params = [];
if ($farmerId > 0) {
    $sql .= ' AND ct.farmer_id = ?';
    $params[] = $farmerId;
}
$sql .= ' ORDER BY COALESCE(approved_at, ct.transaction_date) DESC, ct.customer_transaction_id DESC LIMIT ' . $limit;

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll() ?: [];
} catch (Throwable $e) {
    json_fail('admin_customer_transactions query failed: ' . $e->getMessage(), 500);
}

$items = [];
foreach ($rows as $r) {
    $qty = (float)($r['quantity'] ?? 0);
    $fid = (int)($r['farmer_id'] ?? 0);
    $farmCode = trim((string)($r['farm_code'] ?? ''));
    $farmerNo = $farmCode !== '' ? $farmCode : ($fid > 0 ? (string)$fid : '');
    $fn = trim((string)($r['first_name'] ?? ''));
    $ln = trim((string)($r['last_name'] ?? ''));
    $name = trim($fn . ' ' . $ln);
    if ($name === '') {
        $name = trim((string)($r['username'] ?? ''));
    }
    if ($name === '') {
        $name = trim((string)($r['phone_number'] ?? ''));
    }
    $product = trim((string)($r['product'] ?? ''));
    $variety = strtolower($product);
    if (!in_array($variety, ['liberica', 'excelsa', 'robusta'], true)) {
        $variety = $product;
    }
    $at = $r['approved_at'] ?? $r['transaction_date'] ?? null;
    $status = strtolower(trim((string)($r['current_status'] ?? 'approved')));
    $amount = (float)($r['amount'] ?? 0);
    $paymentAmount = (float)($r['payment_amount'] ?? 0);
    $ref = trim((string)($r['reference_no'] ?? ''));
    $productLabel = in_array($variety, ['liberica', 'excelsa', 'robusta'], true)
        ? ucfirst($variety)
        : $product;
    $items[] = [
        'id' => (int)($r['customer_transaction_id'] ?? 0),
        'customer_transaction_id' => (int)($r['customer_transaction_id'] ?? 0),
        'farmer_id' => $fid,
        'farmer_no' => $farmerNo,
        'farmer_name' => $name,
        'recorded_at' => $at ? (string)$at : '',
        'variety' => $variety,
        'product' => $productLabel,
        'qty' => abs($qty) == floor(abs($qty)) ? (int)abs($qty) : abs($qty),
        'unit' => 'KG',
        'delta_kg' => abs($qty),
        'amount' => $amount,
        'total' => $amount,
        'payment_amount' => $paymentAmount,
        'payment_method' => trim((string)($r['payment_method'] ?? '')) ?: 'Cash',
        'change' => max(0, $paymentAmount - $amount),
        'reference_no' => $ref,
        'ref' => $ref,
        'buyer_name' => trim((string)($r['buyer_name'] ?? '')),
        'notes' => '',
        'recorded_by_phone' => '',
        'status' => $status,
        'sent_to_client' => $status === 'sent_to_client',
    ];
}

header('Access-Control-Allow-Origin: *');
json_ok(['items' => $items, 'count' => count($items)]);
