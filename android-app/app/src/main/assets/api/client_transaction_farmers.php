<?php
declare(strict_types=1);
/**
 * GET — Farmers the client has transacted with (by buyer_name on customer_transaction).
 * Query: client_name or buyer_name (required)
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

$clientName = trim((string)($_GET['client_name'] ?? $_GET['buyer_name'] ?? ''));
if ($clientName === '') {
    json_fail('client_name is required.', 400);
}

try {
    $pdo = db_conn();
} catch (Throwable $e) {
    json_fail('Database unavailable: ' . $e->getMessage(), 503);
}

try {
    $stmt = $pdo->prepare(
        'SELECT ct.farmer_id,
                MAX(ct.transaction_date) AS last_transaction_at,
                COUNT(*) AS tx_count,
                f.farm_code,
                u.username,
                pi.first_name,
                pi.last_name
         FROM customer_transaction ct
         INNER JOIN farmers f ON f.farmer_id = ct.farmer_id
         LEFT JOIN users u ON u.user_id = f.user_id
         LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
         WHERE LOWER(TRIM(ct.buyer_name)) = LOWER(TRIM(?))
         GROUP BY ct.farmer_id, f.farm_code, u.username, pi.first_name, pi.last_name
         ORDER BY last_transaction_at DESC, ct.farmer_id DESC'
    );
    $stmt->execute([$clientName]);
    $rows = $stmt->fetchAll() ?: [];

    $farmers = [];
    foreach ($rows as $r) {
        $first = trim((string)($r['first_name'] ?? ''));
        $last = trim((string)($r['last_name'] ?? ''));
        $full = trim($first . ' ' . $last);
        if ($full === '') {
            $full = trim((string)($r['username'] ?? ''));
        }
        if ($full === '') {
            $full = 'Farmer #' . (int)($r['farmer_id'] ?? 0);
        }
        $farmCode = trim((string)($r['farm_code'] ?? ''));
        $farmers[] = [
            'farmer_id' => (int)($r['farmer_id'] ?? 0),
            'farmer_name' => $full,
            'farmer_no' => $farmCode !== '' ? $farmCode : (string)(int)($r['farmer_id'] ?? 0),
            'tx_count' => (int)($r['tx_count'] ?? 0),
            'last_transaction_at' => $r['last_transaction_at'] ?? null,
        ];
    }

    header('Access-Control-Allow-Origin: *');
    json_ok([
        'client_name' => $clientName,
        'farmers' => $farmers,
        'count' => count($farmers),
    ]);
} catch (Throwable $e) {
    json_fail('client_transaction_farmers failed: ' . $e->getMessage(), 500);
}
