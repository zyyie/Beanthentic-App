<?php
declare(strict_types=1);
/**
 * Admin Farmer's Contribution inbox (GI updates from mobile app).
 * GET    ?limit=500
 * PATCH  JSON { gi_update_id, is_starred, is_read_admin, upload_status }
 * DELETE ?gi_update_id=
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/gi_updates_lib.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

try {
    $pdo = db_conn();
    gi_ensure_updates_table($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 500;
        $limit = max(1, min($limit, 800));
        $stmt = $pdo->query(
            "SELECT g.*, u.email, u.username, u.phone_number,
                    pi.first_name, pi.last_name
             FROM gi_updates g
             LEFT JOIN farmers f ON f.farmer_id = g.farmer_id
             LEFT JOIN users u ON u.user_id = f.user_id
             LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
             WHERE g.current_phase = 'farmer_submission'
             ORDER BY g.created_at DESC, g.gi_update_id DESC
             LIMIT {$limit}"
        );
        $items = [];
        foreach ($stmt->fetchAll() ?: [] as $row) {
            $fn = trim((string)($row['first_name'] ?? ''));
            $ln = trim((string)($row['last_name'] ?? ''));
            $name = trim($fn . ' ' . $ln);
            if ($name === '') {
                $name = trim((string)($row['sender_name'] ?? 'Farmer')) ?: 'Farmer';
            }
            $item = gi_row_to_item($row, $name, (string)($row['email'] ?? $row['username'] ?? ''));
            $item['is_starred'] = (bool)((int)($row['is_starred'] ?? 0));
            $item['starred'] = $item['is_starred'];
            $item['is_read_admin'] = (bool)((int)($row['is_read_admin'] ?? 0));
            $item['unread'] = !$item['is_read_admin'];
            $item['status'] = (string)($row['upload_status'] ?? 'pending');
            $items[] = $item;
        }
        json_ok(['items' => $items, 'count' => count($items)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        $body = read_json_body();
        $action = strtolower(trim((string)($body['action'] ?? '')));
        if ($action === 'set_progress') {
            $farmerId = (int)($body['farmer_id'] ?? 0);
            $progress = (float)($body['progress_percent'] ?? 0);
            if ($progress < 0) {
                $progress = 0;
            }
            if ($progress > 100) {
                $progress = 100;
            }
            if ($farmerId <= 0) {
                json_fail('farmer_id required.', 400);
            }
            $note = trim((string)($body['note'] ?? $body['content'] ?? ''));
            $ins = $pdo->prepare(
                "INSERT INTO gi_updates
                   (farmer_id, title, content, upload_status, is_read_admin, category, current_phase, progress_percent)
                 VALUES (?, 'GI Progress Update', ?, 'approved', 1, 'general', 'admin_progress', ?)"
            );
            $ins->execute([
                $farmerId,
                $note !== '' ? $note : ('Progress set to ' . (int)$progress . '%'),
                $progress,
            ]);
            json_ok(['progress_percent' => $progress]);
        }

        $giId = (int)($body['gi_update_id'] ?? $body['id'] ?? 0);
        if ($giId <= 0) {
            json_fail('gi_update_id required.', 400);
        }
        $sets = [];
        $args = [];
        if (array_key_exists('is_starred', $body) || array_key_exists('starred', $body)) {
            $sets[] = 'is_starred = ?';
            $args[] = !empty($body['is_starred']) || !empty($body['starred']) ? 1 : 0;
        }
        if (array_key_exists('is_read_admin', $body) || array_key_exists('unread', $body)) {
            $val = $body['is_read_admin'] ?? null;
            if ($val === null && array_key_exists('unread', $body)) {
                $val = empty($body['unread']) ? 1 : 0;
            }
            $sets[] = 'is_read_admin = ?';
            $args[] = $val ? 1 : 0;
        }
        if (isset($body['upload_status']) || isset($body['status'])) {
            $status = strtolower(trim((string)($body['upload_status'] ?? $body['status'] ?? 'pending')));
            if (!in_array($status, ['pending', 'approved', 'archived', 'rejected'], true)) {
                $status = 'pending';
            }
            $sets[] = 'upload_status = ?';
            $args[] = $status;
        }
        if (count($sets) === 0) {
            json_fail('No fields to update.', 400);
        }
        $args[] = $giId;
        $sql = 'UPDATE gi_updates SET ' . implode(', ', $sets)
            . " WHERE gi_update_id = ? AND current_phase = 'farmer_submission'";
        $upd = $pdo->prepare($sql);
        $upd->execute($args);
        json_ok(['updated' => $upd->rowCount()]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $giId = isset($_GET['gi_update_id']) ? (int)$_GET['gi_update_id'] : (int)($_GET['id'] ?? 0);
        if ($giId <= 0) {
            $body = read_json_body();
            $giId = (int)($body['gi_update_id'] ?? $body['id'] ?? 0);
        }
        if ($giId <= 0) {
            json_fail('gi_update_id required.', 400);
        }
        $del = $pdo->prepare(
            "DELETE FROM gi_updates WHERE gi_update_id = ? AND current_phase = 'farmer_submission'"
        );
        $del->execute([$giId]);
        json_ok(['deleted' => $del->rowCount()]);
    }

    json_fail('Method not allowed.', 405);
} catch (Throwable $e) {
    json_fail('admin_gi_contributions failed: ' . $e->getMessage(), 500);
}
