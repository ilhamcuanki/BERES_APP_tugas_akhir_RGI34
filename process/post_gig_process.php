<?php
// process/post_gig_process.php
// ATURAN: BASE_URL untuk redirect, ROOT_PATH untuk require
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once ROOT_PATH . 'utils/sanitize.php';
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Validasi Method & CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token()) {
    set_flash('error', 'Permintaan tidak valid.');
    header('Location: ' . BASE_URL . 'client/post_gig.php');
    exit;
}

// 2. Validasi Role: Hanya Client yang boleh posting
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    set_flash('error', 'Akses ditolak. Hanya akun Client yang dapat memposting tugas.');
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

// 3. Sanitasi & Validasi Input
$judul       = sanitize_input($_POST['judul'] ?? '');
$deskripsi   = sanitize_input($_POST['deskripsi'] ?? '');
$kategori_id = filter_var($_POST['id_category'], FILTER_VALIDATE_INT);
$budget      = filter_var($_POST['budget'], FILTER_VALIDATE_FLOAT);
$lokasi      = sanitize_input($_POST['lokasi'] ?? '');

// Cek kelengkapan & batas logis
if (!$judul || strlen($judul) < 5 || !$deskripsi || !$kategori_id || $budget === false || $budget <= 0 || !$lokasi) {
    set_flash('error', 'Data tidak lengkap atau tidak valid. Pastikan budget > 0 dan judul minimal 5 karakter.');
    header('Location: ' . BASE_URL . 'client/post_gig.php');
    exit;
}

// 4. Insert ke Database (Prepared Statement)
try {
    $stmt = $pdo->prepare("INSERT INTO gigs (id_client, id_category, judul, deskripsi, budget, lokasi, status) VALUES (?, ?, ?, ?, ?, ?, 'open')");
    $stmt->execute([
        $_SESSION['user_id'],
        $kategori_id,
        $judul,
        $deskripsi,
        $budget,
        $lokasi
    ]);

    set_flash('success', 'Tugas berhasil diposting! Helper terdekat akan segera melihatnya.');
    header('Location: ' . BASE_URL . 'client/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Post Gig Error: " . $e->getMessage());
    set_flash('error', 'Gagal memposting tugas. Silakan coba beberapa saat lagi.');
    header('Location: ' . BASE_URL . 'client/post_gig.php');
    exit;
}