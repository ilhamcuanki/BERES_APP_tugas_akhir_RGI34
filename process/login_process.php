<?php
// process/login_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'helpers/csrf.php';
require_once ROOT_PATH . 'helpers/sanitize.php';
require_once ROOT_PATH . 'helpers/flash.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
    set_flash('error', 'Permintaan tidak valid.');
    header('Location: ../auth/login.php');
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$pass  = $_POST['password'] ?? '';

if (!$email || !$pass) {
    set_flash('error', 'Email dan password wajib diisi.');
    header('Location: ../auth/login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id_user, nama, password, role FROM users WHERE email = ?");
    $stmt->execute([filter_var($email, FILTER_VALIDATE_EMAIL)]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['role']    = $user['role'];

        $redirect = match ($user['role']) {
            'admin'  => BASE_URL . 'admin/dashboard.php',
            'helper' => BASE_URL . 'helper/dashboard.php',
            default  => BASE_URL . 'client/dashboard.php'
        };
        set_flash('success', "Selamat datang, {$user['nama']}!");
        header("Location: $redirect");
        exit;
    }
    set_flash('error', 'Email atau password salah.');
    header('Location: ../auth/login.php');
    exit;
} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    set_flash('error', 'Gagal memproses login. Coba beberapa saat lagi.');
    header('Location: ../auth/login.php');
    exit;
}
