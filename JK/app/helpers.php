<?php
declare(strict_types=1);

/** HTML escape (idempotent; accepts null safely) */
if (!function_exists('e')) {
    function e(?string $s): string {
        return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Save an uploaded file to JK_UPLOAD_DIR (or a sensible default).
 * Returns: ['original_name','server_path','mime_type','file_size'] or null if no file.
 */
function save_upload(array $file): ?array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload error');
    }

    // Optional max size guard
    if (\defined('JK_MAX_UPLOAD_BYTES') && (int)($file['size'] ?? 0) > JK_MAX_UPLOAD_BYTES) {
        throw new RuntimeException('File too large');
    }

    // Directories (with safe fallbacks)
    $dirFs = \defined('JK_UPLOAD_DIR')
        ? rtrim((string)\constant('JK_UPLOAD_DIR'), '/\\')
        : __DIR__ . '/../uploads';

    $dirWeb = \defined('JK_UPLOAD_WEB')
        ? rtrim((string)\constant('JK_UPLOAD_WEB'), '/\\')
        : 'uploads';

    if (!is_dir($dirFs)) {
        mkdir($dirFs, 0755, true);
    }

    // MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';

    // Generate a safe name
    $ext      = pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)) . ($ext ? ".{$ext}" : '');
    $targetFs = $dirFs . '/' . $basename;

    if (!move_uploaded_file($file['tmp_name'], $targetFs)) {
        throw new RuntimeException('Failed to move uploaded file');
    }

    return [
        'original_name' => (string)($file['name'] ?? ''),
        'server_path'   => $dirWeb . '/' . $basename, // usable in <a href> / <img src>
        'mime_type'     => $mime,
        'file_size'     => (int)($file['size'] ?? 0),
    ];
}
