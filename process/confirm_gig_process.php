<?php
// process/confirm_gig_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? '') !== 'client') {
    set_flash('error', 'Akses tidak valid.');
    header('Location: ' . BASE_URL . 'client/dashboard.php'); exit;
}
if (!verify_csrf_token()) {
    set_flash('error', 'Token keamanan tidak valid.');
    header('Location: ' . BASE_URL . 'client/dashboard.php'); exit;
}

$gig_id = filter_var($_POST['id_gig'], FILTER_VALIDATE_INT);
if (!$gig_id) {
    set_flash('error', 'ID tugas tidak valid.');
    header('Location: ' . BASE_URL . 'client/dashboard.php'); exit;
}

// Verifikasi kepemilikan & status wajib pending_confirm
$stmt = $pdo->prepare("SELECT id_client, status FROM gigs WHERE id_gig = ?");
$stmt->execute([$gig_id]);
$gig = $stmt->fetch();

if (!$gig || $gig['id_client'] != $_SESSION['user_id'] || $gig['status'] !== 'pending_confirm') {
    set_flash('error', 'Tugas tidak valid untuk dikonfirmasi.');
    header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . $gig_id); exit;
}

// Atomic update ke status 'done'
$stmt = $pdo->prepare("UPDATE gigs SET status = 'done', updated_at = NOW() WHERE id_gig = ? AND status = 'pending_confirm'");
$stmt->execute([$gig_id]);

set_flash('success', 'Pekerjaan dikonfirmasi selesai. Silakan berikan ulasan.');
header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . $gig_id);
exit;