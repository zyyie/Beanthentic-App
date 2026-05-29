<?php
declare(strict_types=1);
/**
 * Farmer GI Updates — submit contributions + read admin progress.
 * GET  ?user_id=N
 * POST multipart: user_id, message, files[]
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/gi_updates_lib.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

try {
    $pdo = db_conn();
    gi_ensure_updates_table($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        if ($userId <= 0) {
            json_fail('user_id is required.', 400);
        }
        $farmer = gi_resolve_farmer($pdo, $userId);
        if (!$farmer) {
            json_fail('Farmer account not found.', 404);
        }
        $farmerId = (int)$farmer['farmer_id'];
        $prog = $pdo->prepare(
            "SELECT COALESCE(MAX(progress_percent), 0) AS progress_percent
             FROM gi_updates
             WHERE farmer_id = ? AND current_phase = 'admin_progress'"
        );
        $prog->execute([$farmerId]);
        $progress = (float)($prog->fetch()['progress_percent'] ?? 0);

        $list = $pdo->prepare(
            "SELECT gi_update_id, title, content, attachments_json, upload_status, category, created_at
             FROM gi_updates
             WHERE farmer_id = ? AND current_phase = 'farmer_submission'
             ORDER BY created_at DESC, gi_update_id DESC
             LIMIT 50"
        );
        $list->execute([$farmerId]);
        $rows = $list->fetchAll() ?: [];
        $name = gi_farmer_display_name($farmer);
        $items = [];
        foreach ($rows as $row) {
            $items[] = gi_row_to_item($row, $name, (string)($farmer['email'] ?? ''));
        }
        json_ok([
            'progress_percent' => $progress,
            'submissions' => $items,
            'farmer_name' => $name,
        ]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($userId <= 0) {
            $body = read_json_body();
            $userId = isset($body['user_id']) ? (int)$body['user_id'] : 0;
        }
        $farmer = gi_resolve_farmer($pdo, $userId);
        if (!$farmer) {
            json_fail('Farmer account not found.', 404);
        }
        $farmerId = (int)$farmer['farmer_id'];
        $content = trim((string)($_POST['message'] ?? $_POST['content'] ?? ''));
        if ($content === '') {
            $body = read_json_body();
            $content = trim((string)($body['message'] ?? $body['content'] ?? ''));
        }
        $files = gi_collect_uploaded_files();
        if ($content === '' && count($files) === 0) {
            json_fail('Type a message or attach at least one file.', 400);
        }
        $sender = gi_farmer_display_name($farmer);
        $title = gi_title_from_content($content);
        $category = gi_category_from_files($files);

        $ins = $pdo->prepare(
            "INSERT INTO gi_updates
               (farmer_id, title, content, upload_status, is_read_admin, category, sender_name, current_phase, progress_percent)
             VALUES (?, ?, ?, 'pending', 0, ?, ?, 'farmer_submission', 0)"
        );
        $bodyText = $content !== '' ? $content : '(attachments only)';
        $ins->execute([$farmerId, $title, $bodyText, $category, $sender]);
        $updateId = (int)$pdo->lastInsertId();
        $attachments = gi_save_upload_files($farmerId, $updateId, $files);
        if (count($attachments) > 0) {
            $upd = $pdo->prepare('UPDATE gi_updates SET attachments_json = ? WHERE gi_update_id = ?');
            $upd->execute([json_encode($attachments), $updateId]);
        }
        json_ok(['gi_update_id' => $updateId, 'message' => 'GI update sent to admin for review.']);
    }

    json_fail('Method not allowed.', 405);
} catch (Throwable $e) {
    json_fail('gi_updates failed: ' . $e->getMessage(), 500);
}
