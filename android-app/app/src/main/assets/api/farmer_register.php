<?php
declare(strict_types=1);
/**
 * Legacy alias: same validation + MySQL save as register_farm_farmer.php,
 * returns { ok: true, farmer_id } for older clients.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/farmer_mysql_save.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();
$userId = (int)($body['user_id'] ?? 0);
if ($userId <= 0) {
    json_fail('user_id is required.');
}

$errors = beanthentic_validate_farmer_payload($body, $userId);
if (!empty($errors)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'success' => false, 'errors' => $errors]);
    exit;
}

try {
    $pdo = db_conn();
    $farmerId = beanthentic_farmer_mysql_save($pdo, $userId, $body);
    json_ok(['farmer_id' => $farmerId]);
} catch (Throwable $e) {
    json_fail('Farmer save failed: ' . $e->getMessage(), 500);
}
