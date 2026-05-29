<?php
declare(strict_types=1);
/**
 * GET — Admin Client Report module: list client_misconduct_report rows.
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
    $limit = (int)($_GET['limit'] ?? 500);
} catch (Throwable $e) {
    $limit = 500;
}
$limit = max(1, min($limit, 1000));
$status = trim((string)($_GET['status'] ?? ''));
$q = trim((string)($_GET['q'] ?? ''));

try {
    $pdo = db_conn();
} catch (Throwable $e) {
    json_fail('Database unavailable: ' . $e->getMessage(), 503);
}

try {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS client_misconduct_report (
          report_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          reporter_name VARCHAR(255) NOT NULL,
          reporter_contact VARCHAR(255) NOT NULL DEFAULT "",
          reason_category VARCHAR(255) NOT NULL,
          reason_detail VARCHAR(255) NOT NULL DEFAULT "",
          allegation TEXT NOT NULL,
          chat_json TEXT NULL,
          farmer_id BIGINT UNSIGNED NULL,
          farmer_no VARCHAR(50) NULL,
          farmer_name VARCHAR(255) NOT NULL DEFAULT "",
          status VARCHAR(40) NOT NULL DEFAULT "under review",
          INDEX idx_cmr_status (status),
          INDEX idx_cmr_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $sql = 'SELECT * FROM client_misconduct_report WHERE 1=1';
    $args = [];
    if ($status !== '') {
        $norm = strtolower(str_replace('_', ' ', $status));
        if ($norm === 'open') {
            $norm = 'under review';
        }
        $sql .= ' AND LOWER(REPLACE(status, "_", " ")) = ?';
        $args[] = $norm;
    }
    if ($q !== '') {
        $like = '%' . $q . '%';
        $sql .= ' AND (reporter_name LIKE ? OR reporter_contact LIKE ? OR farmer_name LIKE ?'
            . ' OR reason_category LIKE ? OR reason_detail LIKE ? OR allegation LIKE ?)';
        $args = array_merge($args, array_fill(0, 6, $like));
    }
    $sql .= ' ORDER BY created_at DESC, report_id DESC LIMIT ?';
    $args[] = $limit;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    $rows = $stmt->fetchAll() ?: [];

    $items = [];
    foreach ($rows as $r) {
        $rid = (int)($r['report_id'] ?? 0);
        $st = strtolower(str_replace('_', ' ', trim((string)($r['status'] ?? 'under review'))));
        if ($st === 'open') {
            $st = 'under review';
        }
        $items[] = [
            'id' => $rid,
            'report_id' => $rid,
            'created_at' => $r['created_at'] ?? null,
            'reporter_name' => trim((string)($r['reporter_name'] ?? '')),
            'reporter_contact' => trim((string)($r['reporter_contact'] ?? '')),
            'reason_category' => trim((string)($r['reason_category'] ?? '')),
            'reason_detail' => trim((string)($r['reason_detail'] ?? '')),
            'allegation' => trim((string)($r['allegation'] ?? '')),
            'farmer_id' => isset($r['farmer_id']) ? (int)$r['farmer_id'] : null,
            'farmer_no' => $r['farmer_no'] ?? null,
            'farmer_name' => trim((string)($r['farmer_name'] ?? '')) ?: '—',
            'status' => $st,
        ];
    }

    header('Access-Control-Allow-Origin: *');
    json_ok(['items' => $items, 'count' => count($items)]);
} catch (Throwable $e) {
    json_fail('admin_client_reports failed: ' . $e->getMessage(), 500);
}
