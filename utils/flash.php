<?php
// helpers/flash.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function render_flash(): string {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        
        $class = match($flash['type']) {
            'success' => 'alert-success',
            'error'   => 'alert-error',
            default   => 'alert-info'
        };
        return "<div class='alert {$class}'>{$flash['message']}</div>";
    }
    return '';
}