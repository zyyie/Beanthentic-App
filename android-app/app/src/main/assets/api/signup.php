<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Method not allowed', 405);
}

$body = read_json_body();
$rawPhone = trim((string)($body['phone_number'] ?? $_POST['phone_number'] ?? ''));
$password = (string)($body['password'] ?? $_POST['password'] ?? '');
$name = trim((string)($body['name'] ?? $body['full_name'] ?? $_POST['name'] ?? ''));
$emailIn = trim((string)($body['email'] ?? $_POST['email'] ?? ''));
$email = filter_var($emailIn, FILTER_VALIDATE_EMAIL) ? strtolower($emailIn) : null;

$phone = beanthentic_normalize_phone($rawPhone);
if ($phone === '' || strpos($phone, '+63') !== 0) {
    json_fail('Enter a valid Philippine mobile number (9XXXXXXXXX).');
}
if (strlen($password) < 8) {
    json_fail('Password must be at least 8 characters.');
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

    if ($email !== null) {
        $e = $pdo->prepare('SELECT user_id FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1');
        $e->execute([$email]);
        if ($e->fetch()) {
            $pdo->rollBack();
            json_fail('Email already registered.', 409);
        }
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $display = $name !== '' ? $name : null;

    $ins = $pdo->prepare(
        'INSERT INTO users (phone_number, email, username, password_hash, role)
         VALUES (?, ?, ?, ?, "farmer")'
    );
    $ins->execute([$phone, $email, $display, $hash]);
    $userId = (int)$pdo->lastInsertId();

    $f = $pdo->prepare('INSERT INTO farmers (user_id, status) VALUES (?, "pending")');
    $f->execute([$userId]);
    $farmerId = (int)$pdo->lastInsertId();

    $first = null;
    $last = null;
    if ($display !== null && $display !== '') {
        $parts = preg_split('/\s+/', $display);
        $first = trim((string)($parts[0] ?? '')) ?: null;
        $last = trim((string)($parts[count($parts) - 1] ?? '')) ?: null;
        if (count($parts) === 1) {
            $last = null;
        }
        if ($first === $last && $first !== null) {
            $last = null;
        }
    }
    $pi = $pdo->prepare(
        'INSERT INTO personal_information (farmer_id, first_name, last_name, contact_number) VALUES (?, ?, ?, ?)'
    );
    $pi->execute([$farmerId, $first, $last, $phone]);

    $acc = $pdo->prepare('INSERT INTO account_settings (user_id) VALUES (?)');
    $acc->execute([$userId]);

    $pdo->commit();
    json_ok([
        'user' => [
            'user_id' => $userId,
            'farmer_id' => $farmerId,
            'phone_number' => $phone,
            'email' => $email,
            'name' => $display ?? '',
        ],
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_fail('Signup failed: ' . $e->getMessage(), 500);
}
