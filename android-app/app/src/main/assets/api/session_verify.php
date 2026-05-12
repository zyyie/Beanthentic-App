<?php
declare(strict_types=1);
/**
 * Lightweight check: stored user_id + login key (same shape as client "email" field)
 * still matches an active row in MySQL. Used so WebView localStorage alone cannot
 * impersonate a logged-in user when XAMPP is down or data was wiped.
 */
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'reason' => 'method']);
    exit;
}

$body = read_json_body();
$userId = (int)($body['user_id'] ?? 0);
$loginKey = trim((string)($body['email'] ?? ''));

if ($userId <= 0 || $loginKey === '') {
    echo json_encode(['ok' => false, 'reason' => 'bad_request']);
    exit;
}

$parsed = beanthentic_parse_login_identifier($loginKey);

try {
    $pdo = db_conn();
    if ($parsed['type'] === 'email' && $parsed['email'] !== '') {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM users WHERE user_id = ? AND LOWER(TRIM(COALESCE(email, \'\'))) = ? AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([$userId, strtolower($parsed['email'])]);
    } elseif ($parsed['type'] === 'phone' && $parsed['phone'] !== '') {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM users WHERE user_id = ? AND phone_number = ? AND is_active = 1 LIMIT 1'
        );
        $stmt->execute([$userId, $parsed['phone']]);
    } else {
        echo json_encode(['ok' => false, 'reason' => 'identifier']);
        exit;
    }
    $ok = (bool)$stmt->fetchColumn();
    echo json_encode(['ok' => $ok]);
} catch (Throwable $e) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'reason' => 'db', 'error' => $e->getMessage()]);
}
