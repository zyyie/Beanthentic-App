<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();
$phone = trim((string)($body['phone_number'] ?? $_POST['phone_number'] ?? ''));
$password = (string)($body['password'] ?? $_POST['password'] ?? '');
$name = trim((string)($body['name'] ?? $_POST['name'] ?? ''));

if ($phone === '' || $password === '') {
    json_fail('Phone and password are required.');
}
if (strlen($password) < 6) {
    json_fail('Password must be at least 6 characters.');
}

try {
    $pdo = db_conn();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE phone_number = ? LIMIT 1');
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        json_fail('Phone number already registered.', 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO users (username, phone_number, password_hash, role) VALUES (?, ?, ?, "farmer")');
    $ins->execute([$name !== '' ? $name : null, $phone, $hash]);
    $userId = (int)$pdo->lastInsertId();

    $f = $pdo->prepare('INSERT INTO farmers (user_id, status) VALUES (?, "pending")');
    $f->execute([$userId]);
    $farmerId = (int)$pdo->lastInsertId();

    if ($name !== '') {
        $parts = preg_split('/\s+/', $name);
        $first = trim((string)($parts[0] ?? ''));
        $last = trim((string)($parts[count($parts) - 1] ?? ''));
        $pi = $pdo->prepare('INSERT INTO personal_information (farmer_id, first_name, last_name, contact_number) VALUES (?, ?, ?, ?)');
        $pi->execute([$farmerId, $first !== '' ? $first : null, $last !== '' ? $last : null, $phone]);
    }

    $acc = $pdo->prepare('INSERT INTO account_settings (user_id) VALUES (?)');
    $acc->execute([$userId]);

    $pdo->commit();
    json_ok([
        'user' => [
            'user_id' => $userId,
            'farmer_id' => $farmerId,
            'phone_number' => $phone,
            'name' => $name,
        ],
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
    json_fail('Signup failed: ' . $e->getMessage(), 500);
}

