<?php
declare(strict_types=1);
/**
 * GET ?user_id=N — Farmer app: current warning / suspend state for popups.
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
    $farmerIdHint = isset($_GET['farmer_id']) ? (int)$_GET['farmer_id'] : 0;
    $loginKey = trim((string)($_GET['login'] ?? $_GET['phone'] ?? $_GET['email'] ?? ''));

    $pdo = db_conn();
    beanthentic_ensure_farmer_account_mod_columns($pdo);

    $row = null;
    if ($userId > 0) {
        $stmt = $pdo->prepare(
            'SELECT u.user_id, f.farmer_id
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             WHERE u.user_id = ?
             LIMIT 1'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch() ?: null;
    }

    if (!$row && $loginKey !== '') {
        $parsed = beanthentic_parse_login_identifier($loginKey);
        if ($parsed['type'] === 'email') {
            $stmt = $pdo->prepare(
                'SELECT u.user_id, f.farmer_id
                 FROM users u
                 LEFT JOIN farmers f ON f.user_id = u.user_id
                 WHERE LOWER(TRIM(COALESCE(u.email, \'\'))) = ? AND u.is_active = 1
                 LIMIT 1'
            );
            $stmt->execute([strtolower($parsed['email'])]);
        } elseif ($parsed['type'] === 'phone' && $parsed['phone'] !== '') {
            $stmt = $pdo->prepare(
                'SELECT u.user_id, f.farmer_id
                 FROM users u
                 LEFT JOIN farmers f ON f.user_id = u.user_id
                 WHERE u.phone_number = ? AND u.is_active = 1
                 LIMIT 1'
            );
            $stmt->execute([$parsed['phone']]);
        }
        $row = isset($stmt) ? ($stmt->fetch() ?: null) : null;
    }

    if (!$row) {
        json_fail($userId > 0 || $loginKey !== '' ? 'User not found.' : 'user_id or login is required.', 400);
    }

    $userId = (int)($row['user_id'] ?? 0);

    $farmerId = isset($row['farmer_id']) ? (int)$row['farmer_id'] : 0;
    if ($farmerId <= 0 && $farmerIdHint > 0) {
        $chk = $pdo->prepare(
            'SELECT f.farmer_id FROM farmers f
             INNER JOIN users u ON u.user_id = f.user_id
             WHERE f.farmer_id = ? AND u.user_id = ?
             LIMIT 1'
        );
        $chk->execute([$farmerIdHint, $userId]);
        $match = $chk->fetch();
        if ($match) {
            $farmerId = (int)$match['farmer_id'];
        }
    }
    $accountWarning = null;
    $accountSuspended = null;

    if ($farmerId > 0) {
        $acct = beanthentic_farmer_account_status($pdo, $farmerId);
        if (!empty($acct['is_suspended'])) {
            $accountSuspended = [
                'message' => (string)($acct['suspension_reason'] ?: 'Your account has been suspended by the administrator.'),
                'until' => $acct['suspended_until'] ?? '',
                'reason' => (string)($acct['suspension_reason'] ?? ''),
            ];
        }
        if ((int)($acct['warning_count'] ?? 0) > 0 && ($acct['last_warning_reason'] ?? '') !== '') {
            $wc = (int)$acct['warning_count'];
            $at = (string)($acct['last_warning_at'] ?? '');
            $msg = (string)$acct['last_warning_reason'];
            $accountWarning = [
                'message' => $msg,
                'count' => $wc,
                'at' => $at !== '' ? $at : null,
                'token' => $farmerId . ':' . $wc . ':' . $at . ':' . substr(md5($msg), 0, 8),
            ];
        }
    }

    header('Access-Control-Allow-Origin: *');
    json_ok([
        'ok' => true,
        'user_id' => $userId > 0 ? $userId : null,
        'farmer_id' => $farmerId > 0 ? $farmerId : null,
        'account_warning' => $accountWarning,
        'account_suspended' => $accountSuspended,
    ]);
} catch (Throwable $e) {
    json_fail('farmer_account_status failed: ' . $e->getMessage(), 500);
}
