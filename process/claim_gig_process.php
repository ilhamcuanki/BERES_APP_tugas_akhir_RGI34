<?php
// process/claim_gig_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Guard: Method & Role
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['role']) || $_SESSION['role'] !== 'helper') {
    set_flash('error', 'Akses ditolak. Hanya Helper yang dapat mengambil tugas.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php');
    exit;
}

// 2. CSRF Verification
if (!verify_csrf_token()) {
    set_flash('error', 'Token keamanan tidak valid.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php');
    exit;
}

// 3. Validasi Input
$gig_id = filter_var($_POST['id_gig'], FILTER_VALIDATE_INT);
$helper_id = $_SESSION['user_id'];

if (!$gig_id) {
    set_flash('error', 'ID Tugas tidak valid.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php');
    exit;
}

// 4. Atomic UPDATE (Mencegah Race Condition)
try {
    $stmt = $pdo->prepare("
        UPDATE gigs 
        SET id_helper = ?, status = 'taken', updated_at = NOW() 
        WHERE id_gig = ? AND status = 'open'
    ");
    $stmt->execute([$helper_id, $gig_id]);
    $affected = $stmt->rowCount();

    if ($affected === 1) {
        set_flash('success', 'Tugas berhasil diambil! Silakan segera menuju lokasi.');
        header('Location: ' . BASE_URL . 'helper/detail_gig.php?id=' . $gig_id);
        exit;
    } else {
        set_flash('error', 'Tugas ini sudah diambil oleh Helper lain atau tidak tersedia.');
        header('Location: ' . BASE_URL . 'helper/dashboard.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Claim Gig Error: " . $e->getMessage());
    set_flash('error', 'Terjadi kesalahan sistem. Coba beberapa saat lagi.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php');
    exit;
}