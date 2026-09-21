<?php

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'dailyblog');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'LinearLearn');
define('SITE_TAGLINE', 'Train Your Brain');
define('SITE_SUBTITLE', 'Thoughts · Reflection · A Healthier You');
define('SITE_URL', 'http://localhost:8080');
define('ADMIN_PASSWORD_DEFAULT', 'admin123');
define('API_KEY', getenv('API_KEY') ?: 'linearlearn_secret_key_2026');
define('UPLOADS_DIR', __DIR__ . '/../uploads/');
define('UPLOADS_URL', 'uploads/');

function getDB(): ?PDO {
    static $pdo = null;
    static $hasFailed = false;
    if ($hasFailed) return null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $hasFailed = true;
            error_log('MySQL connection failed: ' . $e->getMessage());
            return null;
        }
    }
    return $pdo;
}
