<?php
// process/register_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'helpers/csrf.php';
require_once ROOT_PATH . 'helpers/sanitize.php';
require_once ROOT_PATH . 'helpers/flash.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
    set_flash('error', 'Permintaan tidak valid. Silakan gunakan formulir resmi.');
    header('Location: ' . BASE_URL . 'auth/register.php');
    exit;
}

$nama   = sanitize_input($_POST['nama'] ?? '');
$email  = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$pass   = $_POST['password'] ?? '';
$role   = in_array($_POST['role'], ['client', 'helper']) ? $_POST['role'] : 'client';

if (!$nama || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
    set_flash('error', 'Data tidak lengkap atau format email/password salah.');
    header('Location: ' . BASE_URL . 'auth/register.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id_user FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        set_flash('error', 'Email sudah terdaftar. Silakan login atau gunakan email lain.');
        header('Location: ' . BASE_URL . 'auth/register.php');
        exit;
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $email, $hash, $role]);

    set_flash('success', 'Registrasi berhasil. Silakan login.');
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
} catch (PDOException $e) {
    error_log("Register Error: " . $e->getMessage());
    set_flash('error', 'Terjadi kesalahan sistem. Coba beberapa saat lagi.');
    header('Location: ' . BASE_URL . 'auth/register.php');
    exit;
}