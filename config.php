<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DATA_DIR', __DIR__ . '/data/');
define('UPLOADS_DIR', __DIR__ . '/uploads/');
define('USERS_FILE', DATA_DIR . 'users.json');
define('CATEGORIES_FILE', DATA_DIR . 'categories.json');
define('ANALYTICS_FILE', DATA_DIR . 'analytics.json');

// Admin Settings - Change these for production
define('ADMIN_RESET_TOKEN', 'u-shadow-admin-reset-2025');

// Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
// Security Headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data: https://cdnjs.cloudflare.com;");

/**
 * Generate a CSRF token if one doesn't exist.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a CSRF token.
 */
function verify_csrf_token($token) {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize output
 */
function s($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Ensure data files exist with proper permissions
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0700, true);
}
if (!is_dir(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0755, true);
}

$data_files = [USERS_FILE, CATEGORIES_FILE, ANALYTICS_FILE];
foreach ($data_files as $file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
        chmod($file, 0600);
    }
}
?>
