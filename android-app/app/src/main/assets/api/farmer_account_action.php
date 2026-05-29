<?php
declare(strict_types=1);
/**
 * POST JSON — Admin moderation: warning | suspend | unsuspend
 * Body: { "farmer_id": 2, "action": "warning|suspend|unsuspend", "reason": "...", "days": 3 }
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

try {
    $body = read_json_body();
    $farmerId = isset($body['farmer_id']) ? (int)$body['farmer_id'] : 0;
    $action = strtolower(trim((string)($body['action'] ?? '')));
    $reason = trim((string)($body['reason'] ?? ''));
    $days = isset($body['days']) ? (int)$body['days'] : 3;

    if ($farmerId <= 0) {
        json_fail('farmer_id is required.', 400);
    }
    if ($reason === '') {
        json_fail('reason is required.', 400);
    }
    if (!in_array($action, ['warning', 'suspend', 'unsuspend'], true)) {
        json_fail('action must be warning, suspend, or unsuspend.', 400);
    }

    $pdo = db_conn();
    beanthentic_ensure_farmer_account_mod_columns($pdo);

    $chk = $pdo->prepare('SELECT farmer_id FROM farmers WHERE farmer_id = ? LIMIT 1');
    $chk->execute([$farmerId]);
    if (!$chk->fetch()) {
        json_fail('Farmer not found.', 404);
    }

    $owner = $pdo->prepare('SELECT user_id FROM farmers WHERE farmer_id = ? LIMIT 1');
    $owner->execute([$farmerId]);
    $ownerRow = $owner->fetch();
    $ownerUserId = $ownerRow ? (int)($ownerRow['user_id'] ?? 0) : 0;

    if ($action === 'warning') {
        $upd = $pdo->prepare(
            'UPDATE farmers
             SET warning_count = warning_count + 1,
                 last_warning_at = NOW(),
                 last_warning_reason = ?
             WHERE farmer_id = ?'
        );
        $upd->execute([mb_substr($reason, 0, 500), $farmerId]);
        if ($ownerUserId > 0) {
            try {
                $log = $pdo->prepare(
                    'INSERT INTO farmer_moderation_logs (user_id, farmer_id, type, reason, expires_at)
                     VALUES (?, ?, \'warning\', ?, NULL)'
                );
                $log->execute([$ownerUserId, $farmerId, mb_substr($reason, 0, 500)]);
            } catch (Throwable $_log) {
                /* table optional on old DB */
            }
        }
    } elseif ($action === 'suspend') {
        $days = max(1, min($days, 365));
        $upd = $pdo->prepare(
            'UPDATE farmers
             SET is_suspended = 1,
                 suspended_until = DATE_ADD(NOW(), INTERVAL ? DAY),
                 suspension_reason = ?
             WHERE farmer_id = ?'
        );
        $upd->execute([$days, mb_substr($reason, 0, 500), $farmerId]);
    } else {
        $upd = $pdo->prepare(
            'UPDATE farmers
             SET is_suspended = 0,
                 suspended_until = NULL,
                 suspension_reason = NULL
             WHERE farmer_id = ?'
        );
        $upd->execute([$farmerId]);
    }

    $status = beanthentic_farmer_account_status($pdo, $farmerId);
    header('Access-Control-Allow-Origin: *');
    json_ok([
        'ok' => true,
        'action' => $action,
        'farmer_id' => $farmerId,
        'account_status' => $status,
    ]);
} catch (Throwable $e) {
    json_fail('farmer_account_action failed: ' . $e->getMessage(), 500);
}
