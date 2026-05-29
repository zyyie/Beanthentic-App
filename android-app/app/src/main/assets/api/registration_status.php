<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();
$userId = (int)($body['user_id'] ?? 0);
$loginKey = trim((string)($body['email'] ?? $body['phone_number'] ?? $body['login'] ?? ''));

if ($loginKey === '') {
    json_fail('Missing login identifier.', 400);
}

$parsed = beanthentic_parse_login_identifier($loginKey);
if ($parsed['type'] === 'empty') {
    json_fail('Invalid login identifier.', 400);
}

try {
    $pdo = db_conn();
    if ($userId > 0) {
        if ($parsed['type'] === 'email') {
            $stmt = $pdo->prepare(
                'SELECT u.user_id, f.farmer_id, f.status AS farmer_status
                 FROM users u
                 LEFT JOIN farmers f ON f.user_id = u.user_id
                 WHERE u.user_id = ? AND LOWER(TRIM(COALESCE(u.email, \'\'))) = ? AND u.is_active = 1
                 LIMIT 1'
            );
            $stmt->execute([$userId, strtolower($parsed['email'])]);
        } else {
            $stmt = $pdo->prepare(
                'SELECT u.user_id, f.farmer_id, f.status AS farmer_status
                 FROM users u
                 LEFT JOIN farmers f ON f.user_id = u.user_id
                 WHERE u.user_id = ? AND u.phone_number = ? AND u.is_active = 1
                 LIMIT 1'
            );
            $stmt->execute([$userId, $parsed['phone']]);
        }
    } elseif ($parsed['type'] === 'email') {
        $stmt = $pdo->prepare(
            'SELECT u.user_id, f.farmer_id, f.status AS farmer_status
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             WHERE LOWER(TRIM(COALESCE(u.email, \'\'))) = ? AND u.is_active = 1
             LIMIT 1'
        );
        $stmt->execute([strtolower($parsed['email'])]);
    } else {
        $stmt = $pdo->prepare(
            'SELECT u.user_id, f.farmer_id, f.status AS farmer_status
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             WHERE u.phone_number = ? AND u.is_active = 1
             LIMIT 1'
        );
        $stmt->execute([$parsed['phone']]);
    }

    $row = $stmt->fetch();
    if (!$row) {
        json_fail('Account not found.', 404);
    }
    $farmerId = isset($row['farmer_id']) ? (int)$row['farmer_id'] : 0;
    $statusRaw = strtolower(trim((string)($row['farmer_status'] ?? 'pending')));
    // Signup creates farmers row as pending; full farm registration sets active (see farmer_mysql_save).
    $registered = $farmerId > 0 && $statusRaw === 'active';
    $profileUrl = beanthentic_farmer_profile_url($farmerId);

    $resolvedUserId = isset($row['user_id']) ? (int)$row['user_id'] : 0;

    json_ok([
        'registered' => $registered,
        'user_id' => $resolvedUserId > 0 ? $resolvedUserId : null,
        // Always return farmer_id when row exists (pending or active) for per-account Client Web QR.
        'farmer_id' => $farmerId > 0 ? $farmerId : null,
        'farmer_status' => $row['farmer_status'] ?? 'pending',
        'profile_url' => $profileUrl,
    ]);
} catch (Throwable $e) {
    json_fail('Registration status check failed: ' . $e->getMessage(), 503);
}

