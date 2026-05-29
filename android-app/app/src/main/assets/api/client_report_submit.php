<?php
declare(strict_types=1);
/**
 * POST — Client Web: save misconduct report to beanthentic_app.
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

$body = read_json_body();

$reporterName = trim((string)($body['reporter_name'] ?? ''));
if ($reporterName === '') {
    json_fail('reporter_name is required.', 400);
}

$reasonCategory = trim((string)($body['reason_category'] ?? $body['reason'] ?? ''));
if ($reasonCategory === '') {
    json_fail('reason_category is required.', 400);
}

$reasonDetail = trim((string)($body['reason_detail'] ?? ''));
$allegation = trim((string)($body['allegation'] ?? ''));
if ($allegation === '') {
    json_fail('allegation is required.', 400);
}

$reporterContact = trim((string)($body['reporter_contact'] ?? ''));
$chatJson = $body['chat_log'] ?? $body['chat_json'] ?? null;
$chatStr = null;
if ($chatJson !== null) {
    $chatStr = is_string($chatJson) ? $chatJson : json_encode($chatJson, JSON_UNESCAPED_UNICODE);
}

$farmerId = isset($body['farmer_id']) ? (int)$body['farmer_id'] : 0;
$farmerNo = trim((string)($body['farmer_no'] ?? ''));
$farmerName = trim((string)($body['farmer_name'] ?? ''));

try {
    $pdo = db_conn();
} catch (Throwable $e) {
    json_fail('Database unavailable: ' . $e->getMessage(), 503);
}

if ($farmerId > 0 && $farmerName === '') {
    $fs = $pdo->prepare(
        'SELECT f.farmer_id, f.farm_code, u.username, pi.first_name, pi.last_name
         FROM farmers f
         LEFT JOIN users u ON u.user_id = f.user_id
         LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
         WHERE f.farmer_id = ? LIMIT 1'
    );
    $fs->execute([$farmerId]);
    $fr = $fs->fetch();
    if ($fr) {
        $fn = trim((string)($fr['first_name'] ?? '') . ' ' . (string)($fr['last_name'] ?? ''));
        $farmerName = trim($fn) !== '' ? trim($fn) : trim((string)($fr['username'] ?? ''));
        if ($farmerNo === '' && !empty($fr['farm_code'])) {
            $farmerNo = (string)$fr['farm_code'];
        } elseif ($farmerNo === '') {
            $farmerNo = (string)$farmerId;
        }
    }
}

try {
    $pdo->prepare(
        'INSERT INTO client_misconduct_report
          (reporter_name, reporter_contact, reason_category, reason_detail, allegation, chat_json,
           farmer_id, farmer_no, farmer_name, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    )->execute([
        $reporterName,
        $reporterContact,
        $reasonCategory,
        $reasonDetail,
        $allegation,
        $chatStr,
        $farmerId > 0 ? $farmerId : null,
        $farmerNo !== '' ? $farmerNo : null,
        $farmerName,
        'under review',
    ]);
    $id = (int)$pdo->lastInsertId();
    header('Access-Control-Allow-Origin: *');
    json_ok([
        'report_id' => $id,
        'id' => $id,
        'message' => 'Your report was submitted. Our team will review it.',
    ]);
} catch (Throwable $e) {
    json_fail('client_report_submit failed: ' . $e->getMessage(), 500);
}
