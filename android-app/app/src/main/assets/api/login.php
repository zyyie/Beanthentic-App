<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();
$rawLogin = trim((string)($body['phone_number'] ?? $body['login'] ?? $_POST['phone_number'] ?? $_POST['login'] ?? ''));
$password = (string)($body['password'] ?? $_POST['password'] ?? '');

if ($rawLogin === '' || $password === '') {
    json_fail('Phone (or email) and password are required.');
}

$parsed = beanthentic_parse_login_identifier($rawLogin);
if ($parsed['type'] === 'empty') {
    json_fail('Enter a valid phone number or email.');
}

try {
    $pdo = db_conn();
    if ($parsed['type'] === 'email') {
        $stmt = $pdo->prepare(
            'SELECT u.user_id, u.phone_number, u.password_hash, u.username, u.email, f.farmer_id,
                    f.status AS farmer_status,
                    pi.first_name, pi.last_name
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
             WHERE LOWER(TRIM(u.email)) = LOWER(TRIM(?)) AND u.is_active = 1
             LIMIT 1'
        );
        $stmt->execute([$parsed['email']]);
    } else {
        $phone = $parsed['phone'];
        if ($phone === '') {
            json_fail('Enter a valid Philippine mobile number.');
        }
        $stmt = $pdo->prepare(
            'SELECT u.user_id, u.phone_number, u.password_hash, u.username, u.email, f.farmer_id,
                    f.status AS farmer_status,
                    pi.first_name, pi.last_name
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
             WHERE u.phone_number = ? AND u.is_active = 1
             LIMIT 1'
        );
        $stmt->execute([$phone]);
    }

    $row = $stmt->fetch();
    if (!$row) {
        json_fail('Account not found.', 404);
    }
    if (!password_verify($password, (string)$row['password_hash'])) {
        json_fail('Invalid password.', 401);
    }

    $farmerId = $row['farmer_id'] !== null ? (int)$row['farmer_id'] : 0;
    if ($farmerId > 0) {
        beanthentic_ensure_farmer_account_mod_columns($pdo);
        $acct = beanthentic_farmer_account_status($pdo, $farmerId);
        if (!empty($acct['is_suspended'])) {
            json_fail(beanthentic_suspend_block_message($acct), 403);
        }
    }

    $name = trim((string)($row['username'] ?? ''));
    if ($name === '') {
        $first = trim((string)($row['first_name'] ?? ''));
        $last = trim((string)($row['last_name'] ?? ''));
        $name = trim($first . ' ' . $last);
    }
    if ($name === '') {
        $name = (string)($row['phone_number'] ?? $rawLogin);
    }

    $upd = $pdo->prepare('UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE user_id = ?');
    $upd->execute([(int)$row['user_id']]);

    $farmerStatus = strtolower(trim((string)($row['farmer_status'] ?? 'pending')));
    $registrationComplete = $farmerStatus === 'active';

    json_ok([
        'user' => [
            'user_id' => (int)$row['user_id'],
            'farmer_id' => $row['farmer_id'] !== null ? (int)$row['farmer_id'] : null,
            'farmer_status' => $row['farmer_status'] !== null ? (string)$row['farmer_status'] : 'pending',
            'registration_complete' => $registrationComplete,
            'phone_number' => (string)($row['phone_number'] ?? ''),
            'email' => $row['email'] !== null ? (string)$row['email'] : null,
            'name' => $name,
        ],
    ]);
} catch (Throwable $e) {
    json_fail('Login failed: ' . $e->getMessage(), 500);
}
