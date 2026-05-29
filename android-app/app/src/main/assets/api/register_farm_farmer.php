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

try {
    $pdo = db_conn();
    if ($userId <= 0) {
        $userId = beanthentic_resolve_user_id_from_payload($pdo, $body);
    }
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['success' => false, 'errors' => ['_error' => 'Database unavailable: ' . $e->getMessage()]]);
    exit;
}

$errors = beanthentic_validate_farmer_payload($body, $userId);
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $farmerId = beanthentic_farmer_mysql_save($pdo, $userId, $body);
    $profilePhoto = '';
    try {
        $st = $pdo->prepare('SELECT profile_photo FROM farmers WHERE farmer_id = ? LIMIT 1');
        $st->execute([$farmerId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['profile_photo'])) {
            $profilePhoto = trim((string)$row['profile_photo']);
        }
    } catch (Throwable $_ph) {
        $profilePhoto = '';
    }
    echo json_encode([
        'success' => true,
        'farmer_id' => $farmerId,
        'user_id' => $userId,
        'profile_photo' => $profilePhoto,
        'message' => 'Farmer registered successfully',
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['_error' => $e->getMessage()]]);
}
