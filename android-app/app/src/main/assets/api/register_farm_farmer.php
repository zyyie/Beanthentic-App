<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/farmer_mysql_save.inc.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'errors' => ['_error' => 'Method not allowed']]);
    exit;
}

$body = read_json_body();
$userId = (int)($body['user_id'] ?? 0);

$errors = beanthentic_validate_farmer_payload($body, $userId);
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $pdo = db_conn();
    $farmerId = beanthentic_farmer_mysql_save($pdo, $userId, $body);
    echo json_encode([
        'success' => true,
        'farmer_id' => $farmerId,
        'message' => 'Farmer registered successfully',
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['_error' => $e->getMessage()]]);
}
