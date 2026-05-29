<?php
declare(strict_types=1);
/**
 * Farmer list for Beanthentic-Client-Web (home page circles).
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
    $pdo = db_conn();
    $limit = 500;
    if (isset($_GET['limit'])) {
        $limit = max(1, min(500, (int)$_GET['limit']));
    }

    $sql = 'SELECT
          f.farmer_id,
          f.status,
          f.profile_photo,
          f.created_at,
          f.updated_at,
          u.username,
          u.phone_number,
          u.email,
          pi.first_name,
          pi.last_name,
          COALESCE(pi.barangay, fi.barangay) AS barangay,
          ai.federation_assoc,
          ai.coop_name
        FROM farmers f
        INNER JOIN users u ON u.user_id = f.user_id
        LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
        LEFT JOIN farm_information fi ON fi.farmer_id = f.farmer_id
        LEFT JOIN affiliation_information ai ON ai.farmer_id = f.farmer_id
        ORDER BY COALESCE(f.updated_at, f.created_at) DESC, f.farmer_id DESC
        LIMIT ' . (int)$limit;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch (Throwable $colErr) {
        if (stripos($colErr->getMessage(), 'coop_name') === false) {
            throw $colErr;
        }
        $sql = str_replace('ai.coop_name', "'' AS coop_name", $sql);
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    $rows = $stmt->fetchAll() ?: [];

    $farmers = [];
    foreach ($rows as $r) {
        $first = trim((string)($r['first_name'] ?? ''));
        $last = trim((string)($r['last_name'] ?? ''));
        if ($first === '' && $last === '') {
            $full = trim((string)($r['username'] ?? ''));
            if ($full !== '') {
                $parts = preg_split('/\s+/', $full) ?: [];
                if (count($parts) >= 2) {
                    $last = array_pop($parts);
                    $first = trim(implode(' ', $parts));
                } else {
                    $first = $full;
                }
            }
        }
        $farmers[] = [
            'farmer_id' => (int)($r['farmer_id'] ?? 0),
            'first_name' => $first,
            'last_name' => $last,
            'barangay' => trim((string)($r['barangay'] ?? '')),
            'profile_photo' => trim((string)($r['profile_photo'] ?? '')),
            'status' => trim((string)($r['status'] ?? 'pending')),
            'phone_number' => trim((string)($r['phone_number'] ?? '')),
            'email' => trim((string)($r['email'] ?? '')),
        ];
    }

    header('Access-Control-Allow-Origin: *');
    json_ok(['farmers' => $farmers, 'count' => count($farmers)]);
} catch (Throwable $e) {
    json_fail('client_farmers failed: ' . $e->getMessage(), 500);
}
