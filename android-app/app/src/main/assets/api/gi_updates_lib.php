<?php
declare(strict_types=1);

function gi_ensure_updates_table(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS gi_updates (
          gi_update_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          farmer_id BIGINT UNSIGNED NULL,
          title VARCHAR(150) NOT NULL,
          content TEXT NOT NULL,
          image_url VARCHAR(255) NULL,
          attachments_json TEXT NULL,
          upload_status ENUM('pending','approved','archived','rejected') NOT NULL DEFAULT 'pending',
          is_starred TINYINT(1) NOT NULL DEFAULT 0,
          is_read_admin TINYINT(1) NOT NULL DEFAULT 0,
          category VARCHAR(30) NOT NULL DEFAULT 'general',
          sender_name VARCHAR(255) NULL,
          progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
          current_phase VARCHAR(100) NULL,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX idx_gi_updates_farmer (farmer_id),
          INDEX idx_gi_updates_status (upload_status, is_read_admin)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $migrations = [
        'attachments_json' => 'TEXT NULL',
        "upload_status" => "ENUM('pending','approved','archived','rejected') NOT NULL DEFAULT 'pending'",
        'is_starred' => 'TINYINT(1) NOT NULL DEFAULT 0',
        'is_read_admin' => 'TINYINT(1) NOT NULL DEFAULT 0',
        'category' => "VARCHAR(30) NOT NULL DEFAULT 'general'",
        'sender_name' => 'VARCHAR(255) NULL',
    ];
    foreach ($migrations as $col => $ddl) {
        try {
            $pdo->exec("ALTER TABLE gi_updates ADD COLUMN {$col} {$ddl}");
        } catch (Throwable $e) {
            // duplicate column — skip
        }
    }
}

function gi_farmer_display_name(array $row): string
{
    $fn = trim((string)($row['first_name'] ?? ''));
    $ln = trim((string)($row['last_name'] ?? ''));
    $name = trim($fn . ' ' . $ln);
    if ($name !== '') {
        return $name;
    }
    $u = trim((string)($row['username'] ?? ''));
    if ($u !== '') {
        return $u;
    }
    return trim((string)($row['phone_number'] ?? 'Farmer')) ?: 'Farmer';
}

function gi_resolve_farmer(PDO $pdo, int $userId): ?array
{
    if ($userId <= 0) {
        return null;
    }
    $stmt = $pdo->prepare(
        'SELECT f.farmer_id, u.user_id, u.username, u.phone_number, u.email,
                pi.first_name, pi.last_name
         FROM users u
         LEFT JOIN farmers f ON f.user_id = u.user_id
         LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
         WHERE u.user_id = ?
         LIMIT 1'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if (!$row || empty($row['farmer_id'])) {
        return null;
    }
    return $row;
}

function gi_title_from_content(string $content): string
{
    $text = trim(preg_replace('/\s+/', ' ', $content));
    if ($text === '') {
        return 'GI Update';
    }
    if (strlen($text) > 150) {
        return substr($text, 0, 147) . '...';
    }
    return $text;
}

function gi_collect_uploaded_files(): array
{
    $out = [];
    $keys = ['files', 'gi_support_file'];
    foreach ($keys as $key) {
        if (!empty($_FILES[$key])) {
            $out = array_merge($out, gi_normalize_file_bucket($_FILES[$key]));
        }
    }
    return $out;
}

function gi_normalize_file_bucket(array $bucket): array
{
    $out = [];
    if (is_array($bucket['name'])) {
        $n = count($bucket['name']);
        for ($i = 0; $i < $n; $i++) {
            if ((int)($bucket['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }
            $out[] = [
                'name' => (string)$bucket['name'][$i],
                'tmp_name' => (string)$bucket['tmp_name'][$i],
                'type' => (string)($bucket['type'][$i] ?? ''),
                'size' => (int)($bucket['size'][$i] ?? 0),
            ];
        }
    } elseif ((int)($bucket['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $out[] = [
            'name' => (string)$bucket['name'],
            'tmp_name' => (string)$bucket['tmp_name'],
            'type' => (string)($bucket['type'] ?? ''),
            'size' => (int)($bucket['size'] ?? 0),
        ];
    }
    return $out;
}

function gi_category_from_files(array $files): string
{
    $hasImage = false;
    $hasDoc = false;
    foreach ($files as $f) {
        $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $hasImage = true;
        } elseif (in_array($ext, ['pdf', 'doc', 'docx'], true) || $ext !== '') {
            $hasDoc = true;
        }
    }
    if ($hasImage && !$hasDoc) {
        return 'images';
    }
    if ($hasDoc) {
        return 'documents';
    }
    return 'general';
}

function gi_uploads_dir(): string
{
    return dirname(__DIR__) . '/uploads/gi_contributions';
}

function gi_save_upload_files(int $farmerId, int $updateId, array $files): array
{
    $saved = [];
    if (count($files) === 0) {
        return $saved;
    }
    $dir = gi_uploads_dir();
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    foreach ($files as $f) {
        $orig = trim((string)($f['name'] ?? ''));
        if ($orig === '' || empty($f['tmp_name'])) {
            continue;
        }
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'], true)) {
            continue;
        }
        $safe = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $orig);
        $fname = 'gi_' . $farmerId . '_' . $updateId . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        $dest = $dir . '/' . $fname;
        if (!move_uploaded_file((string)$f['tmp_name'], $dest)) {
            continue;
        }
        $saved[] = [
            'name' => $safe,
            'path' => '/uploads/gi_contributions/' . $fname,
            'mime' => (string)($f['type'] ?? ''),
            'size' => (int)filesize($dest),
        ];
    }
    return $saved;
}

function gi_parse_attachments($raw): array
{
    if (!$raw) {
        return [];
    }
    $data = json_decode((string)$raw, true);
    return is_array($data) ? $data : [];
}

function gi_row_to_item(array $row, string $farmerName, string $email): array
{
    $content = (string)($row['content'] ?? '');
    $preview = str_replace("\n", ' ', $content);
    if (strlen($preview) > 160) {
        $preview = substr($preview, 0, 157) . '...';
    }
    return [
        'gi_update_id' => (int)($row['gi_update_id'] ?? 0),
        'id' => (int)($row['gi_update_id'] ?? 0),
        'farmer_name' => $farmerName,
        'farmer_email' => $email,
        'title' => (string)($row['title'] ?? ''),
        'subject' => (string)($row['title'] ?? ''),
        'content' => $content,
        'preview' => $preview,
        'upload_status' => (string)($row['upload_status'] ?? 'pending'),
        'category' => (string)($row['category'] ?? 'general'),
        'attachments' => gi_parse_attachments($row['attachments_json'] ?? null),
        'created_at' => (string)($row['created_at'] ?? ''),
    ];
}
