<?php
// process/logout.php
// PASTIKAN: Tidak ada spasi/enter sebelum <?php

require_once __DIR__ . '/../config/constants.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kosongkan semua variabel session
$_SESSION = array();

// 2. Hapus cookie session jika ada (untuk keamanan ekstra)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy session & redirect ke index
session_destroy();
header('Location: ' . BASE_URL . 'index.php');
exit;