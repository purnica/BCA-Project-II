<?php
// ============================================================
// db.php — Database Connection (XAMPP / project6)
// ============================================================
// Place this file inside: C:/xampp/htdocs/course_crud/
// Access your app at:     http://localhost/course_crud/
// ============================================================

// ── XAMPP default credentials ──────────────────────────────
// XAMPP ships with user=root and an empty password by default.
// If you set a MySQL password in phpMyAdmin, update DB_PASS below.
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Mysql@123');          // Leave empty for default XAMPP install
define('DB_NAME', 'project6');  // Your database in phpMyAdmin

function getConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }

    return $pdo;
}
?>