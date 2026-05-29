<?php
declare(strict_types=1);
/**
 * Single farmer profile for Client Web QR scan (fast JSON).
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

$farmerId = isset($_GET['farmer_id']) ? (int)$_GET['farmer_id'] : 0;
if ($farmerId <= 0) {
    json_fail('farmer_id is required.', 400);
}

try {
    $pdo = db_conn();
    $stmt = $pdo->prepare(
        'SELECT
          f.farmer_id,
          f.status,
          f.profile_photo,
          u.phone_number,
          u.email,
          u.username,
          pi.first_name,
          pi.last_name,
          pi.birthday,
          pi.contact_number,
          pi.province,
          pi.municipality,
          pi.barangay AS pi_barangay,
          pi.current_address,
          COALESCE(pi.barangay, fi.barangay) AS barangay,
          fi.farm_name,
          fi.ownership_status,
          ai.federation_assoc,
          ai.rsbsa_registered,
          ai.rsbsa_number,
          ai.rsbsa_status
        FROM farmers f
        INNER JOIN users u ON u.user_id = f.user_id
        LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
        LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
        LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
        WHERE f.farmer_id = ?
        LIMIT 1'
    );
    $stmt->execute([$farmerId]);
    $r = $stmt->fetch();
    if (!$r) {
        json_fail('Farmer not found.', 404);
    }

    unset($r['phone_number'], $r['email'], $r['contact_number'], $r['username']);

    $addr = trim((string)($r['current_address'] ?? ''));
    if ($addr === '') {
        $parts = array_filter([
            trim((string)($r['pi_barangay'] ?? '')),
            trim((string)($r['municipality'] ?? '')),
            trim((string)($r['province'] ?? '')),
        ]);
        $addr = implode(', ', $parts);
    }
    $r['current_address'] = $addr !== '' ? $addr : '—';

    if (!empty($r['birthday'])) {
        $ts = strtotime((string)$r['birthday']);
        if ($ts) {
            $r['birthday'] = date('F d, Y', $ts);
        }
    }

    $first = trim((string)($r['first_name'] ?? ''));
    $last = trim((string)($r['last_name'] ?? ''));
    $r['display_name'] = trim($first . ' ' . $last) !== '' ? trim($first . ' ' . $last) : ('Farmer #' . $farmerId);

    $rsbSt = strtolower(trim((string)($r['rsbsa_status'] ?? '')));
    if ($rsbSt === 'not_yet_applied') {
        $r['rsbsa_status_label'] = 'Not Yet Applied';
    } elseif ($rsbSt === 'pending_rsbsa') {
        $r['rsbsa_status_label'] = 'Pending RSBSA';
    } else {
        $r['rsbsa_status_label'] = '';
    }

    header('Access-Control-Allow-Origin: *');
    json_ok(['farmer' => $r]);
} catch (Throwable $e) {
    json_fail('client_farmer_profile failed: ' . $e->getMessage(), 500);
}
