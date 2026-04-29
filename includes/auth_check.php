<?php
// includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function require_auth(array $allowed_roles = []): void {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: ' . ROOT_PATH . 'auth/login.php');
        exit;
    }
    
    if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: ' . ROOT_PATH . 'auth/login.php?error=unauthorized');
        exit;
    }
}