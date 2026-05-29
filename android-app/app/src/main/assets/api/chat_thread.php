<?php
declare(strict_types=1);
/**
 * Farmer ↔ Admin chat thread (shared_messages in XAMPP).
 * GET  ?user_id=N  — list thread
 * POST { user_id, text } — send message to admin
 */
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204);
    exit;
}

function ensure_shared_messages_table(PDO $pdo): void {
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS shared_messages (
          message_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          sender_role ENUM(\'admin\',\'farmer\') NOT NULL,
          sender_phone VARCHAR(32) NOT NULL,
          sender_name VARCHAR(255) NULL,
          recipient_role ENUM(\'admin\',\'farmer\') NOT NULL,
          recipient_phone VARCHAR(32) NOT NULL DEFAULT \'\',
          recipient_name VARCHAR(255) NULL,
          subject VARCHAR(300) NOT NULL,
          body TEXT NOT NULL,
          category VARCHAR(30) NOT NULL DEFAULT \'general\',
          farmer_id BIGINT UNSIGNED NULL,
          is_read TINYINT(1) NOT NULL DEFAULT 0,
          is_starred TINYINT(1) NOT NULL DEFAULT 0,
          is_archived TINYINT(1) NOT NULL DEFAULT 0,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          read_at DATETIME NULL,
          INDEX idx_sm_recipient (recipient_role, recipient_phone, is_read, is_archived),
          INDEX idx_sm_sender (sender_role, sender_phone),
          INDEX idx_sm_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

try {
    $pdo = db_conn();
    ensure_shared_messages_table($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        if ($userId <= 0) {
            json_fail('user_id is required.', 400);
        }

        $stmt = $pdo->prepare(
            'SELECT u.phone_number, u.username, f.farmer_id,
                    pi.first_name, pi.last_name
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
             WHERE u.user_id = ?
             LIMIT 1'
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) {
            json_fail('User not found.', 404);
        }

        $phone = beanthentic_normalize_phone((string)($user['phone_number'] ?? ''));
        if ($phone === '') {
            json_fail('User phone not set.', 400);
        }
        $displayName = beanthentic_farmer_display_name($user);

        $variants = beanthentic_phone_variants($phone);
        $ph = implode(', ', array_fill(0, count($variants), '?'));

        $list = $pdo->prepare(
            "SELECT
              message_id AS id,
              sender_role,
              sender_phone,
              sender_name,
              recipient_role,
              recipient_phone,
              recipient_name,
              subject,
              body,
              category,
              farmer_id,
              is_read,
              created_at
            FROM shared_messages
            WHERE (sender_role = 'farmer' AND sender_phone IN ({$ph}))
               OR (recipient_role = 'farmer' AND recipient_phone IN ({$ph}))
            ORDER BY created_at ASC, message_id ASC
            LIMIT 500"
        );
        $list->execute(array_merge($variants, $variants));
        $items = $list->fetchAll() ?: [];
        foreach ($items as &$row) {
            if (isset($row['sender_role'])) {
                $row['sender_role'] = strtolower((string)$row['sender_role']);
            }
            if (($row['sender_role'] ?? '') === 'farmer') {
                $row['sender_name'] = $displayName;
            }
            if (isset($row['created_at'])) {
                $row['created_at'] = beanthentic_format_mysql_datetime($row['created_at']);
            }
        }
        unset($row);

        $mark = $pdo->prepare(
            "UPDATE shared_messages
             SET is_read = 1, read_at = NOW()
             WHERE recipient_role = 'farmer' AND recipient_phone IN ({$ph}) AND is_read = 0"
        );
        $mark->execute($variants);

        header('Access-Control-Allow-Origin: *');
        json_ok(['phone' => $phone, 'items' => $items]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = read_json_body();
        $userId = isset($body['user_id']) ? (int)$body['user_id'] : 0;
        $text = trim((string)($body['text'] ?? ''));
        if ($userId <= 0 || $text === '') {
            json_fail('user_id and text are required.', 400);
        }

        $stmt = $pdo->prepare(
            'SELECT u.phone_number, u.username, f.farmer_id,
                    pi.first_name, pi.last_name
             FROM users u
             LEFT JOIN farmers f ON f.user_id = u.user_id
             LEFT JOIN personal_information pi ON pi.farmer_id = f.farmer_id
             WHERE u.user_id = ?
             LIMIT 1'
        );
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        if (!$user) {
            json_fail('User not found.', 404);
        }

        $phone = beanthentic_normalize_phone((string)($user['phone_number'] ?? ''));
        if ($phone === '') {
            json_fail('User phone not set.', 400);
        }
        $name = beanthentic_farmer_display_name($user);
        $farmerId = isset($user['farmer_id']) ? (int)$user['farmer_id'] : 0;
        $createdAt = beanthentic_parse_client_created_at((string)($body['client_created_at'] ?? ''));
        if ($createdAt === null) {
            json_fail('client_created_at is required (device local time).', 400);
        }

        $ins = $pdo->prepare(
            'INSERT INTO shared_messages
              (sender_role, sender_phone, sender_name, recipient_role, recipient_phone,
               recipient_name, subject, body, category, farmer_id, is_read, is_starred, is_archived, created_at)
             VALUES
              (\'farmer\', ?, ?, \'admin\', \'\', \'Admin\', \'Farmer Message\', ?, \'farmers\', ?, 0, 0, 0, ?)'
        );
        $ins->execute([
            $phone,
            mb_substr($name, 0, 255),
            $text,
            $farmerId > 0 ? $farmerId : null,
            $createdAt,
        ]);
        $mid = (int)$pdo->lastInsertId();

        header('Access-Control-Allow-Origin: *');
        json_ok([
            'message_id' => $mid,
            'phone' => $phone,
            'message' => [
                'id' => $mid,
                'sender_role' => 'farmer',
                'sender_phone' => $phone,
                'sender_name' => mb_substr($name, 0, 255),
                'body' => $text,
                'created_at' => $createdAt,
            ],
        ]);
    }

    json_fail('Method not allowed', 405);
} catch (Throwable $e) {
    json_fail('chat_thread failed: ' . $e->getMessage(), 500);
}
