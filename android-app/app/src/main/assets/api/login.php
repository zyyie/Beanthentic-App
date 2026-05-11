<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();
$phone = trim((string)($body['phone_number'] ?? $_POST['phone_number'] ?? ''));
$password = (string)($body['password'] ?? $_POST['password'] ?? '');

if ($phone === '' || $password === '') {
    json_fail('Phone and password are required.');
}

try {
    $pdo = db_conn();
    $stmt = $pdo->prepare(
        'SELECT u.user_id, u.phone_number, u.password_hash, u.username, f.farmer_id,
                pi.first_name, pi.last_name
         FROM users u
         LEFT JOIN farmers f ON f.user_id = u.user_id
         LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
         WHERE u.phone_number = ? AND u.is_active = 1
         LIMIT 1'
    );
    $stmt->execute([$phone]);
    $row = $stmt->fetch();
    if (!$row) json_fail('Account not found.', 404);
    if (!password_verify($password, (string)$row['password_hash'])) {
        json_fail('Invalid password.', 401);
    }

    $name = trim((string)($row['username'] ?? ''));
    if ($name === '') {
        $first = trim((string)($row['first_name'] ?? ''));
        $last = trim((string)($row['last_name'] ?? ''));
        $name = trim($first . ' ' . $last);
    }
    if ($name === '') $name = $phone;

    $upd = $pdo->prepare('UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE user_id = ?');
    $upd->execute([(int)$row['user_id']]);

    json_ok([
        'user' => [
            'user_id' => (int)$row['user_id'],
            'farmer_id' => $row['farmer_id'] !== null ? (int)$row['farmer_id'] : null,
            'phone_number' => (string)$row['phone_number'],
            'name' => $name,
        ],
    ]);
} catch (Throwable $e) {
    json_fail('Login failed: ' . $e->getMessage(), 500);
}

