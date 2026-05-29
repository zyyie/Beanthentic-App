<?php
declare(strict_types=1);
/**
 * POST — Client Web pickup transaction (multipart or JSON).
 * Saves every form field into customer_transaction + transaction_history (pending).
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

function ct_parse_pickup_date(string $raw): ?string
{
    $raw = trim($raw);
    if ($raw === '') {
        return null;
    }
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $raw, $m)) {
        return $m[1] . '-' . $m[2] . '-' . $m[3];
    }
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $raw, $m)) {
        return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
    }
    $ts = strtotime($raw);
    return $ts !== false ? date('Y-m-d', $ts) : null;
}

function ct_first_farmer_id(PDO $pdo): int
{
    $stmt = $pdo->query(
        "SELECT farmer_id FROM farmers WHERE status = 'active' ORDER BY farmer_id ASC LIMIT 1"
    );
    $row = $stmt ? $stmt->fetch() : false;
    if ($row && !empty($row['farmer_id'])) {
        return (int)$row['farmer_id'];
    }
    $stmt = $pdo->query('SELECT farmer_id FROM farmers ORDER BY farmer_id ASC LIMIT 1');
    $row = $stmt ? $stmt->fetch() : false;
    return $row ? (int)$row['farmer_id'] : 0;
}

function ct_normalize_person_name(string $name): string
{
    $name = trim(preg_replace('/\s+/u', ' ', $name));
    return function_exists('mb_strtolower') ? mb_strtolower($name, 'UTF-8') : strtolower($name);
}

function ct_resolve_farmer_id(PDO $pdo, array $body): int
{
    $requested = (int)($body['farmer_id'] ?? 0);
    if ($requested > 0) {
        $chk = $pdo->prepare('SELECT farmer_id FROM farmers WHERE farmer_id = ? LIMIT 1');
        $chk->execute([$requested]);
        if ($chk->fetch()) {
            return $requested;
        }
    }

    $name = trim((string)($body['farmer_name'] ?? ''));
    if ($name !== '') {
        $norm = ct_normalize_person_name($name);
        $stmt = $pdo->prepare(
            "SELECT f.farmer_id
             FROM farmers f
             INNER JOIN personal_information pi ON pi.farmer_id = f.farmer_id
             WHERE LOWER(TRIM(CONCAT(COALESCE(pi.first_name, ''), ' ', COALESCE(pi.last_name, '')))) = ?
             ORDER BY f.farmer_id ASC
             LIMIT 1"
        );
        $stmt->execute([$norm]);
        $row = $stmt->fetch();
        if ($row && !empty($row['farmer_id'])) {
            return (int)$row['farmer_id'];
        }
    }

    return 0;
}

function ct_save_valid_id(int $txId, array $file): array
{
    $out = ['path' => null, 'filename' => null];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return $out;
    }
    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return $out;
    }
    $orig = trim((string)($file['name'] ?? ''));
    if ($orig !== '') {
        $out['filename'] = $orig;
    }
    $mime = '';
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        if ($fi) {
            $mime = (string)finfo_file($fi, $tmp);
            finfo_close($fi);
        }
    }
    $ext = 'jpg';
    if (stripos($mime, 'png') !== false) {
        $ext = 'png';
    } elseif (stripos($mime, 'webp') !== false) {
        $ext = 'webp';
    }
    $dir = dirname(__DIR__) . '/uploads/client_ids';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $name = 'tx_' . $txId . '_' . time() . '.' . $ext;
    $dest = $dir . '/' . $name;
    if (!@move_uploaded_file($tmp, $dest)) {
        return $out;
    }
    $out['path'] = '/uploads/client_ids/' . $name;
    return $out;
}

function ct_table_columns(PDO $pdo): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    try {
        $stmt = $pdo->query(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_transaction'"
        );
        while ($row = $stmt->fetch()) {
            $cache[(string)$row['COLUMN_NAME']] = true;
        }
    } catch (Throwable $e) {
        $cache = [];
    }
    return $cache;
}

function ct_upsert_transaction(PDO $pdo, array $data): int
{
    $cols = ct_table_columns($pdo);
    $ref = (string)$data['reference_no'];

    $existing = $pdo->prepare(
        'SELECT customer_transaction_id FROM customer_transaction WHERE reference_no = ? LIMIT 1'
    );
    $existing->execute([$ref]);
    $exRow = $existing->fetch();
    $txId = $exRow ? (int)$exRow['customer_transaction_id'] : 0;

    $row = [
        'farmer_id' => (int)$data['farmer_id'],
        'buyer_name' => (string)$data['buyer_name'],
        'product' => (string)$data['product'],
        'quantity' => (float)$data['quantity'],
        'amount' => (float)$data['amount'],
        'payment_amount' => (float)$data['payment_amount'],
        'payment_method' => (string)$data['payment_method'],
        'reference_no' => $ref,
        'transaction_date' => (string)$data['transaction_date'],
    ];

    $optional = [
        'transaction_type' => (string)$data['transaction_type'],
        'pickup_date' => $data['pickup_date'],
        'pickup_date_display' => (string)$data['pickup_date_display'],
        'valid_id_path' => $data['valid_id_path'],
        'valid_id_filename' => $data['valid_id_filename'],
        'quantity_unit' => (string)$data['quantity_unit'],
        'submitted_from' => (string)$data['submitted_from'],
        'client_form_json' => (string)$data['client_form_json'],
    ];
    foreach ($optional as $k => $v) {
        if (isset($cols[$k])) {
            $row[$k] = $v;
        }
    }

    if ($txId > 0) {
        $sets = [];
        $vals = [];
        foreach ($row as $k => $v) {
            if ($k === 'reference_no') {
                continue;
            }
            $sets[] = $k . ' = ?';
            $vals[] = $v;
        }
        $vals[] = $txId;
        $pdo->prepare(
            'UPDATE customer_transaction SET ' . implode(', ', $sets) . ' WHERE customer_transaction_id = ?'
        )->execute($vals);
        return $txId;
    }

    $fields = array_keys($row);
    $placeholders = array_fill(0, count($fields), '?');
    $pdo->prepare(
        'INSERT INTO customer_transaction (' . implode(', ', $fields) . ')
         VALUES (' . implode(', ', $placeholders) . ')'
    )->execute(array_values($row));
    return (int)$pdo->lastInsertId();
}

$contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
$isMultipart = strpos($contentType, 'multipart/form-data') !== false;
$body = $isMultipart ? $_POST : read_json_body();
if (!is_array($body)) {
    $body = [];
}

$buyer = trim((string)($body['client_name'] ?? $body['buyer_name'] ?? $body['buyer'] ?? ''));
$product = trim((string)($body['product_type'] ?? $body['product'] ?? ''));
$pickupDisplay = trim((string)($body['pickup_date'] ?? ''));
$transactionType = trim((string)($body['transaction_type'] ?? 'pickup')) ?: 'pickup';
$paymentMethod = trim((string)($body['payment_method'] ?? 'Cash')) ?: 'Cash';
$quantityUnit = trim((string)($body['quantity_unit'] ?? 'KG')) ?: 'KG';

if ($buyer === '' || $product === '') {
    json_fail('Name and product are required.', 400);
}

$qtyRaw = trim((string)($body['quantity_kg'] ?? $body['qty'] ?? '0'));
$qty = is_numeric($qtyRaw) ? (float)$qtyRaw : 0.0;
if ($qty <= 0) {
    json_fail('Quantity must be greater than zero.', 400);
}

$amount = (float)($body['payment_amount'] ?? $body['amount'] ?? 0);
if ($amount <= 0) {
    json_fail('Amount to pay is required.', 400);
}

if ($isMultipart && isset($_FILES['valid_id']) && is_array($_FILES['valid_id'])) {
    $idErr = (int)($_FILES['valid_id']['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($idErr !== UPLOAD_ERR_OK && $idErr !== UPLOAD_ERR_NO_FILE) {
        json_fail('Valid ID upload failed. Please try again.', 400);
    }
    if ($idErr === UPLOAD_ERR_NO_FILE) {
        json_fail('Valid ID is required.', 400);
    }
}

$pickupDate = ct_parse_pickup_date($pickupDisplay);
$txnDate = $pickupDate ? ($pickupDate . ' 09:00:00') : date('Y-m-d H:i:s');

$ref = trim((string)($body['reference_no'] ?? ''));
if ($ref === '') {
    $ref = 'CW' . date('YmdHis') . sprintf('%04d', random_int(0, 9999));
}

$formJson = json_encode([
    'transaction_type' => $transactionType,
    'client_name' => $buyer,
    'pickup_date' => $pickupDisplay,
    'pickup_date_iso' => $pickupDate,
    'product_type' => $product,
    'quantity_kg' => $qty,
    'quantity_unit' => $quantityUnit,
    'payment_method' => $paymentMethod,
    'payment_amount' => $amount,
    'reference_no' => $ref,
    'submitted_from' => 'client_web',
], JSON_UNESCAPED_UNICODE);

try {
    $pdo = db_conn();
} catch (Throwable $e) {
    json_fail('Database unavailable: ' . $e->getMessage(), 503);
}

$farmerId = ct_resolve_farmer_id($pdo, $body);
if ($farmerId <= 0) {
    json_fail(
        'farmer_id is required. Open Transaction from the farmer profile (Go to Transaction) so the request goes to the correct farmer.',
        400
    );
}

try {
    $pdo->beginTransaction();

    $txId = ct_upsert_transaction($pdo, [
        'farmer_id' => $farmerId,
        'buyer_name' => $buyer,
        'product' => $product,
        'quantity' => $qty,
        'amount' => $amount,
        'payment_amount' => $amount,
        'payment_method' => $paymentMethod,
        'reference_no' => $ref,
        'transaction_date' => $txnDate,
        'transaction_type' => $transactionType,
        'pickup_date' => $pickupDate,
        'pickup_date_display' => $pickupDisplay,
        'valid_id_path' => null,
        'valid_id_filename' => null,
        'quantity_unit' => $quantityUnit,
        'submitted_from' => 'client_web',
        'client_form_json' => $formJson,
    ]);

    $validIdPath = null;
    $validIdFilename = null;
    if ($isMultipart && isset($_FILES['valid_id']) && is_array($_FILES['valid_id'])) {
        $saved = ct_save_valid_id($txId, $_FILES['valid_id']);
        $validIdPath = $saved['path'];
        $validIdFilename = $saved['filename'];
        if ($validIdPath) {
            $cols = ct_table_columns($pdo);
            $sets = [];
            $vals = [];
            if (isset($cols['valid_id_path'])) {
                $sets[] = 'valid_id_path = ?';
                $vals[] = $validIdPath;
            }
            if (isset($cols['valid_id_filename']) && $validIdFilename) {
                $sets[] = 'valid_id_filename = ?';
                $vals[] = $validIdFilename;
            }
            if (isset($cols['client_form_json'])) {
                $decoded = json_decode($formJson, true) ?: [];
                $decoded['valid_id_path'] = $validIdPath;
                $decoded['valid_id_filename'] = $validIdFilename;
                $sets[] = 'client_form_json = ?';
                $vals[] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            }
            if ($sets) {
                $vals[] = $txId;
                $pdo->prepare(
                    'UPDATE customer_transaction SET ' . implode(', ', $sets) . ' WHERE customer_transaction_id = ?'
                )->execute($vals);
            }
        }
    }

    $remarks = 'Client Web ' . $transactionType;
    if ($pickupDisplay !== '') {
        $remarks .= '; pickup=' . $pickupDisplay;
    }
    if ($validIdPath) {
        $remarks .= '; valid_id=' . $validIdPath;
    }

    $pdo->prepare(
        'INSERT INTO transaction_history (customer_transaction_id, status, remarks, changed_by_user_id)
         VALUES (?, ?, ?, NULL)'
    )->execute([$txId, 'pending', $remarks]);

    $pdo->commit();

    header('Access-Control-Allow-Origin: *');
    json_ok([
        'customer_transaction_id' => $txId,
        'reference_no' => $ref,
        'status' => 'pending',
        'farmer_id' => $farmerId,
        'saved_fields' => [
            'client_name' => $buyer,
            'transaction_type' => $transactionType,
            'pickup_date' => $pickupDisplay,
            'product_type' => $product,
            'quantity_kg' => $qty,
            'quantity_unit' => $quantityUnit,
            'payment_method' => $paymentMethod,
            'payment_amount' => $amount,
            'valid_id_saved' => $validIdPath !== null,
            'valid_id_path' => $validIdPath,
        ],
        'message' => 'Transaction submitted. Waiting for farmer approval in the app.',
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_fail('client_transaction_submit failed: ' . $e->getMessage(), 500);
}
