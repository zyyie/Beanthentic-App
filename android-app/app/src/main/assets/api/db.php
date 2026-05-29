<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json; charset=utf-8');

function db_conn(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    // Default: XAMPP on same PC (127.0.0.1, root, no password). Override via env vars.
    $host = getenv('BEANTHENTIC_DB_HOST') ?: '127.0.0.1';
    $port = getenv('BEANTHENTIC_DB_PORT') ?: '3306';
    $name = getenv('BEANTHENTIC_DB_NAME') ?: 'beanthentic_app';
    $user = getenv('BEANTHENTIC_DB_USER') ?: 'root';
    $pass = getenv('BEANTHENTIC_DB_PASS') !== false ? getenv('BEANTHENTIC_DB_PASS') : '';

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

/** @return list<string> Common PH formats for one number (+63, 09…, 639…). */
function beanthentic_phone_variants(string $raw): array {
    $out = [];
    $seen = [];
    $add = static function (string $v) use (&$out, &$seen): void {
        $v = trim($v);
        if ($v === '' || isset($seen[$v])) {
            return;
        }
        $seen[$v] = true;
        $out[] = $v;
    };
    $add(beanthentic_normalize_phone($raw));
    $digits = preg_replace('/\D+/', '', $raw);
    if ($digits === '') {
        return $out;
    }
    $add($digits);
    if (strlen($digits) > 0 && $digits[0] === '0' && strlen($digits) >= 11) {
        $add('+63' . substr($digits, 1));
        $add(substr($digits, 1));
    }
    if (strlen($digits) >= 2 && substr($digits, 0, 2) === '63' && strlen($digits) >= 12) {
        $add('+63' . substr($digits, 2));
        $add('0' . substr($digits, 2));
        $add(substr($digits, 2));
    }
    if (strlen($digits) === 10 && $digits[0] === '9') {
        $add('+63' . $digits);
        $add('0' . $digits);
        $add('63' . $digits);
    }
    return $out;
}

/** Device-local wall clock from app POST (YYYY-MM-DD HH:mm:ss). */
function beanthentic_parse_client_created_at(?string $raw): ?string {
    $raw = trim((string)$raw);
    if ($raw === '') {
        return null;
    }
    if (preg_match('/^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}(?::\d{2})?)$/', $raw, $m)) {
        $time = strlen($m[2]) === 5 ? $m[2] . ':00' : $m[2];
        return $m[1] . ' ' . substr($time, 0, 8);
    }
    if (strpos($raw, 'T') !== false) {
        try {
            $dt = new DateTime($raw);
            $dt->setTimezone(new DateTimeZone('Asia/Manila'));
            return $dt->format('Y-m-d H:i:s');
        } catch (Throwable $e) {
            return null;
        }
    }
    return null;
}

/** Normalize MySQL datetime for JSON (always space-separated, no Z). */
function beanthentic_format_mysql_datetime($value): string {
    if ($value instanceof DateTimeInterface) {
        return $value->format('Y-m-d H:i:s');
    }
    $s = trim((string)$value);
    if (preg_match('/^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}:\d{2})/', $s, $m)) {
        return $m[1] . ' ' . substr($m[2], 0, 8);
    }
    return $s;
}

/** Full name from personal_information (Register Farm), else username, else Farmer #id. */
function beanthentic_farmer_display_name(array $row): string {
    $first = trim((string)($row['first_name'] ?? ''));
    $last = trim((string)($row['last_name'] ?? ''));
    $full = trim($first . ' ' . $last);
    if ($full !== '') {
        return $full;
    }
    $username = trim((string)($row['username'] ?? ''));
    if ($username !== '') {
        return $username;
    }
    $fid = (int)($row['farmer_id'] ?? 0);
    return $fid > 0 ? ('Farmer #' . $fid) : 'Farmer';
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

/** Admin moderation columns on farmers (warning / suspend). */
function beanthentic_ensure_farmer_account_mod_columns(PDO $pdo): void {
    $cols = [
        'is_suspended' => 'TINYINT(1) NOT NULL DEFAULT 0',
        'suspended_until' => 'DATETIME NULL',
        'suspension_reason' => 'VARCHAR(500) NULL',
        'warning_count' => 'INT NOT NULL DEFAULT 0',
        'last_warning_at' => 'DATETIME NULL',
        'last_warning_reason' => 'VARCHAR(500) NULL',
    ];
    foreach ($cols as $name => $def) {
        $chk = $pdo->prepare(
            'SELECT COUNT(*) AS c FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = \'farmers\' AND COLUMN_NAME = ?'
        );
        $chk->execute([$name]);
        if ((int)($chk->fetchColumn() ?: 0) === 0) {
            $pdo->exec("ALTER TABLE farmers ADD COLUMN {$name} {$def}");
        }
    }
}

function beanthentic_clear_expired_farmer_suspensions(PDO $pdo, ?int $farmerId = null): void {
    beanthentic_ensure_farmer_account_mod_columns($pdo);
    $sql = 'UPDATE farmers SET is_suspended = 0, suspended_until = NULL, suspension_reason = NULL
            WHERE is_suspended = 1 AND suspended_until IS NOT NULL AND suspended_until <= NOW()';
    if ($farmerId !== null && $farmerId > 0) {
        $sql .= ' AND farmer_id = ?';
        $st = $pdo->prepare($sql);
        $st->execute([$farmerId]);
        return;
    }
    $pdo->exec($sql);
}

/**
 * @return array{is_suspended: bool, suspended_until: ?string, suspension_reason: string, warning_count: int, last_warning_reason: string, last_warning_at: ?string}
 */
function beanthentic_farmer_account_status(PDO $pdo, int $farmerId): array {
    beanthentic_clear_expired_farmer_suspensions($pdo, $farmerId);
    $st = $pdo->prepare(
        'SELECT is_suspended, suspended_until, suspension_reason, warning_count, last_warning_at, last_warning_reason
         FROM farmers WHERE farmer_id = ? LIMIT 1'
    );
    $st->execute([$farmerId]);
    $row = $st->fetch() ?: [];
    $isSusp = (int)($row['is_suspended'] ?? 0) === 1;
    $until = $row['suspended_until'] ?? null;
    $activeSusp = false;
    if ($isSusp) {
        if ($until === null || $until === '') {
            $activeSusp = true;
        } else {
            $activeSusp = strtotime((string)$until) > time();
        }
    }
    return [
        'is_suspended' => $activeSusp,
        'suspended_until' => $until ? beanthentic_format_mysql_datetime($until) : null,
        'suspension_reason' => (string)($row['suspension_reason'] ?? ''),
        'warning_count' => (int)($row['warning_count'] ?? 0),
        'last_warning_reason' => (string)($row['last_warning_reason'] ?? ''),
        'last_warning_at' => !empty($row['last_warning_at'])
            ? beanthentic_format_mysql_datetime($row['last_warning_at'])
            : null,
    ];
}

/** Human-readable message when login or session is blocked due to suspension. */
function beanthentic_suspend_block_message(array $acct): string {
    $msg = 'Your account is suspended. You cannot use the app until access is restored.';
    $until = (string)($acct['suspended_until'] ?? '');
    $reason = trim((string)($acct['suspension_reason'] ?? ''));
    if ($until !== '') {
        $msg .= ' Access restores after ' . $until . '.';
    }
    if ($reason !== '') {
        $msg .= ' Reason: ' . $reason;
    }
    return $msg;
}

/** LAN-reachable Client Web base (farmer QR). Avoid 127.0.0.1 when request is on LAN. */
function beanthentic_client_web_base(): string {
    $env = trim((string)(getenv('BEANTHENTIC_CLIENT_WEB_BASE') ?: ''));
    if ($env !== '') {
        return rtrim($env, '/');
    }
    $port = trim((string)(getenv('BEANTHENTIC_CLIENT_WEB_PORT') ?: '5001'));
    if ($port === '') {
        $port = '5001';
    }
    $host = trim((string)($_SERVER['HTTP_HOST'] ?? ''));
    $h = $host !== '' ? strtok($host, ':') : '';
    if ($h !== '' && $h !== 'localhost' && $h !== '127.0.0.1') {
        return 'http://' . $h . ':' . $port;
    }
    return 'http://192.168.0.100:' . $port;
}

/** Short QR profile URL: {base}/farmer/{id} */
function beanthentic_farmer_profile_url(int $farmerId): ?string {
    if ($farmerId <= 0) {
        return null;
    }
    return beanthentic_client_web_base() . '/farmer/' . $farmerId;
}

