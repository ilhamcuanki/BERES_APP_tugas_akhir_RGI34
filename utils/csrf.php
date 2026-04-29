<?php
// helpers/csrf.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(): bool {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return false;
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        return false;
    }
    // Regenerate token setelah verifikasi untuk mencegah replay attack
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return true;
}