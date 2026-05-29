<?php
declare(strict_types=1);

/**
 * Save register-farm profile photo (data URL) to assets/uploads/farmers/ and return web path.
 */

if (!function_exists('fr_assets_uploads_farmers_dir')) {
    function fr_assets_uploads_farmers_dir(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'farmers';
    }
}

if (!function_exists('fr_mysql_save_profile_photo_file')) {
  /**
   * @return string|null Relative URL path e.g. /uploads/farmers/farmer_12.jpg
   */
    function fr_mysql_save_profile_photo_file(int $farmerId, string $photoData): ?string
    {
        if ($farmerId <= 0) {
            return null;
        }
        $raw = trim($photoData);
        if ($raw === '' || !preg_match('#^data:image/(jpeg|jpg|png|webp);base64,#i', $raw, $m)) {
            return null;
        }
        $comma = strpos($raw, ',');
        if ($comma === false) {
            return null;
        }
        $bin = base64_decode(substr($raw, $comma + 1), true);
        if ($bin === false || strlen($bin) < 64) {
            return null;
        }
        $ext = strtolower($m[1]);
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        if (!in_array($ext, ['jpg', 'png', 'webp'], true)) {
            $ext = 'jpg';
        }
        $dir = fr_assets_uploads_farmers_dir();
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return null;
        }
        $rel = '/uploads/farmers/farmer_' . $farmerId . '.' . $ext;
        $path = dirname(__DIR__) . str_replace('/', DIRECTORY_SEPARATOR, $rel);
        if (file_put_contents($path, $bin) === false) {
            return null;
        }
        return $rel;
    }
}

if (!function_exists('fr_mysql_profile_photo_public_url')) {
    /** Turn DB path into absolute URL for the browser (optional). */
    function fr_mysql_profile_photo_public_url(?string $storedPath): string
    {
        $p = trim((string)$storedPath);
        if ($p === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $p)) {
            return $p;
        }
        if (isset($p[0]) && $p[0] === '/') {
            return $p;
        }
        return '/' . ltrim($p, '/');
    }
}
