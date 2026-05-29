<?php
declare(strict_types=1);
/**
 * Unread admin→farmer messages for badge (does not mark as read).
 * GET ?user_id=N → { ok, unread_count }
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

try {
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
    if ($userId <= 0) {
        json_fail('user_id is required.', 400);
    }

    $pdo = db_conn();
    $stmt = $pdo->prepare(
        'SELECT u.phone_number FROM users u WHERE u.user_id = ? LIMIT 1'
    );
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user) {
        json_fail('User not found.', 404);
    }

    $phone = beanthentic_normalize_phone((string)($user['phone_number'] ?? ''));
    if ($phone === '') {
        json_ok(['unread_count' => 0]);
    }

    $variants = beanthentic_phone_variants($phone);
    $ph = implode(', ', array_fill(0, count($variants), '?'));
    $countStmt = $pdo->prepare(
        "SELECT COUNT(*) AS c
         FROM shared_messages
         WHERE recipient_role = 'farmer'
           AND recipient_phone IN ({$ph})
           AND sender_role = 'admin'
           AND is_read = 0
           AND is_archived = 0"
    );
    $countStmt->execute($variants);
    $row = $countStmt->fetch();
    $count = (int)($row['c'] ?? 0);

    header('Access-Control-Allow-Origin: *');
    json_ok(['unread_count' => $count]);
} catch (Throwable $e) {
    json_fail('chat_unread_count failed: ' . $e->getMessage(), 500);
}
