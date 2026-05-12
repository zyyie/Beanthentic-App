<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function db_conn(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = getenv('BEANTHENTIC_DB_HOST') ?: '127.0.0.1';
    $port = getenv('BEANTHENTIC_DB_PORT') ?: '3306';
    $name = getenv('BEANTHENTIC_DB_NAME') ?: 'beanthentic_app';
    $user = getenv('BEANTHENTIC_DB_USER') ?: 'root';
    $pass = getenv('BEANTHENTIC_DB_PASS') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function read_json_body(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_ok(array $payload = []): void {
    http_response_code(200);
    echo json_encode(['ok' => true] + $payload);
    exit;
}

function json_fail(string $message, int $status = 400): void {
    http_response_code($status);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

/** Normalize PH mobile to E.164 +639XXXXXXXXX (10 digits after country code). */
function beanthentic_normalize_phone(string $raw): string {
    $s = trim($raw);
    if ($s === '') {
        return '';
    }
    if (strpos($s, '@') !== false) {
        return '';
    }
    $digits = preg_replace('/\D+/', '', $s);
    if ($digits === '') {
        return '';
    }
    if (strlen($digits) > 0 && $digits[0] === '0') {
        $digits = substr($digits, 1);
    }
    if (strlen($digits) >= 2 && substr($digits, 0, 2) === '63') {
        $digits = substr($digits, 2);
    }
    if (strlen($digits) === 10 && $digits[0] === '9') {
        return '+63' . $digits;
    }
    return $s;
}

/**
 * @return array{type: 'email'|'phone'|'empty', email: string, phone: string}
 */
function beanthentic_parse_login_identifier(string $raw): array {
    $t = trim($raw);
    if ($t === '') {
        return ['type' => 'empty', 'email' => '', 'phone' => ''];
    }
    if (strpos($t, '@') !== false) {
        return ['type' => 'email', 'email' => strtolower($t), 'phone' => ''];
    }
    $ph = beanthentic_normalize_phone($t);
    if ($ph !== '' && strpos($ph, '+63') === 0) {
        return ['type' => 'phone', 'email' => '', 'phone' => $ph];
    }
    return ['type' => 'phone', 'email' => '', 'phone' => $ph];
}

