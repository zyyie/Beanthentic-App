<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/farmer_profile_photo.inc.php';
require_once __DIR__ . '/farmer_mysql_save.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$body = read_json_body();
try {
    $pdo = db_conn();
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'Database unavailable']);
    exit;
}

$userId = (int)($body['user_id'] ?? 0);
if ($userId <= 0) {
    $userId = beanthentic_resolve_user_id_from_payload($pdo, $body);
}
if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing or invalid user']);
    exit;
}

$st = $pdo->prepare(
    'SELECT f.farmer_id FROM farmers f
     INNER JOIN users u ON u.user_id = f.user_id
     WHERE f.user_id = ? AND u.is_active = 1
     ORDER BY f.farmer_id DESC LIMIT 1'
);
$st->execute([$userId]);
$row = $st->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Farmer profile not found. Complete Register Farm first.']);
    exit;
}

$farmerId = (int)$row['farmer_id'];
$updated = false;
$profilePhoto = '';

$photoData = trim((string)($body['profile_photo_data'] ?? ''));
if ($photoData !== '') {
    $photoPath = fr_mysql_save_profile_photo_file($farmerId, $photoData);
    if ($photoPath) {
        $pdo->prepare('UPDATE farmers SET profile_photo = ?, updated_at = NOW() WHERE farmer_id = ?')
            ->execute([$photoPath, $farmerId]);
        $profilePhoto = $photoPath;
        $updated = true;
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid profile photo. Use JPG, PNG, or WebP.']);
        exit;
    }
}

$first = trim((string)($body['first_name'] ?? ''));
$last = trim((string)($body['last_name'] ?? ''));
if ($first !== '' || $last !== '') {
    $pi = $pdo->prepare('SELECT personal_info_id FROM personal_information WHERE farmer_id = ? LIMIT 1');
    $pi->execute([$farmerId]);
    $piRow = $pi->fetch(PDO::FETCH_ASSOC);
    if ($piRow) {
        $pdo->prepare(
            'UPDATE personal_information SET first_name = COALESCE(NULLIF(?, \'\'), first_name),
             last_name = COALESCE(NULLIF(?, \'\'), last_name) WHERE farmer_id = ?'
        )->execute([$first, $last, $farmerId]);
    } elseif ($first !== '' && $last !== '') {
        $pdo->prepare(
            'INSERT INTO personal_information (farmer_id, first_name, last_name) VALUES (?, ?, ?)'
        )->execute([$farmerId, $first, $last]);
    }
    $updated = true;
}

if (!$updated) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Nothing to update']);
    exit;
}

if ($profilePhoto === '') {
    $ph = $pdo->prepare('SELECT profile_photo FROM farmers WHERE farmer_id = ? LIMIT 1');
    $ph->execute([$farmerId]);
    $phRow = $ph->fetch(PDO::FETCH_ASSOC);
    $profilePhoto = trim((string)($phRow['profile_photo'] ?? ''));
}

echo json_encode([
    'success' => true,
    'farmer_id' => $farmerId,
    'user_id' => $userId,
    'profile_photo' => $profilePhoto,
    'first_name' => $first,
    'last_name' => $last,
]);
