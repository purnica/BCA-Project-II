<?php
// ============================================================
// upload_helper.php — Reusable File Upload Utility (XAMPP)
// ============================================================
// Files are saved inside your project folder under /uploads/
// Full disk path example:
//   C:/xampp/htdocs/course_crud/uploads/thumbnails/
//   C:/xampp/htdocs/course_crud/uploads/content/
//
// Browser URL example (how they're stored in the DB):
//   uploads/thumbnails/file_abc123.jpg
//   uploads/content/file_xyz456.mp4
// ============================================================

// Absolute paths on disk  (__DIR__ = folder this file lives in)
define('UPLOAD_THUMBNAIL_DIR', __DIR__ . '/uploads/thumbnails/');
define('UPLOAD_CONTENT_DIR',   __DIR__ . '/uploads/content/');

// Relative URL paths stored in the database
define('UPLOAD_THUMBNAIL_URL', 'uploads/thumbnails/');
define('UPLOAD_CONTENT_URL',   'uploads/content/');

$ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$ALLOWED_CONTENT_TYPES = [
    // images
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    // video
    'video/mp4', 'video/webm', 'video/ogg', 'video/avi', 'video/quicktime',
    // documents
    'application/pdf',
];

/**
 * Upload a single file and return its stored path.
 *
 * @param  array  $file          — One entry from $_FILES (e.g. $_FILES['image'])
 * @param  string $destDir       — Absolute destination directory
 * @param  string $destUrlPrefix — Relative URL prefix stored in the DB
 * @param  array  $allowedMimes  — Allowed MIME types
 * @param  int    $maxBytes      — Max file size in bytes (default 100 MB)
 * @return string                — Relative file path to store in the database
 * @throws RuntimeException      — On validation or move failure
 */
function uploadFile(
    array  $file,
    string $destDir,
    string $destUrlPrefix,
    array  $allowedMimes,
    int    $maxBytes = 104857600   // 100 MB
): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds the upload_max_filesize directive.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds the MAX_FILE_SIZE directive.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        throw new RuntimeException($errorMessages[$file['error']] ?? 'Unknown upload error.');
    }

    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('File is too large. Maximum allowed size is ' . ($maxBytes / 1048576) . ' MB.');
    }

    // Validate MIME type using finfo (safer than trusting the browser)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedMimes, true)) {
        throw new RuntimeException('Invalid file type: ' . $mimeType . '. Allowed types: ' . implode(', ', $allowedMimes));
    }

    // Generate a unique filename to prevent collisions & path traversal
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename  = uniqid('file_', true) . '.' . $extension;
    $destPath  = $destDir . $filename;

    // Ensure destination directory exists
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        throw new RuntimeException('Could not create upload directory.');
    }

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new RuntimeException('Failed to move uploaded file to destination.');
    }

    return $destUrlPrefix . $filename;  // relative path stored in DB
}

/**
 * Delete a file from disk given its relative path (as stored in the DB).
 */
function deleteFile(string $relPath): void {
    $absPath = __DIR__ . '/' . $relPath;
    if ($relPath && file_exists($absPath)) {
        unlink($absPath);
    }
}
?>