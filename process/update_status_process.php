<?php
// process/update_status_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? '') !== 'helper') {
    set_flash('error', 'Akses tidak valid.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php'); exit;
}
if (!verify_csrf_token()) {
    set_flash('error', 'Token keamanan tidak valid.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php'); exit;
}

$gig_id = filter_var($_POST['id_gig'], FILTER_VALIDATE_INT);
$target_status = $_POST['status'] ?? '';
if (!$gig_id || !in_array($target_status, ['ongoing', 'pending_confirm'])) {
    set_flash('error', 'Parameter status tidak valid.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php'); exit;
}

$stmt = $pdo->prepare("SELECT status, id_helper FROM gigs WHERE id_gig = ?");
$stmt->execute([$gig_id]);
$gig = $stmt->fetch();

if (!$gig || $gig['id_helper'] != $_SESSION['user_id']) {
    set_flash('error', 'Anda tidak memiliki hak untuk mengubah tugas ini.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php'); exit;
}

$allowed_transitions = ['taken' => 'ongoing', 'ongoing' => 'pending_confirm'];
$current = $gig['status'];
if (!isset($allowed_transitions[$current]) || $allowed_transitions[$current] !== $target_status) {
    set_flash('error', "Transisi status '$current' ke '$target_status' tidak diizinkan.");
    header('Location: ' . BASE_URL . 'helper/detail_gig.php?id=' . $gig_id); exit;
}

try {
    $stmt = $pdo->prepare("UPDATE gigs SET status = ?, updated_at = NOW() WHERE id_gig = ? AND status = ?");
    $stmt->execute([$target_status, $gig_id, $current]);
    $stmt->rowCount() === 1 
        ? set_flash('success', "Status berhasil diperbarui menjadi '$target_status'.")
        : set_flash('info', 'Status tugas sudah berubah oleh aksi lain.');
} catch (PDOException $e) {
    error_log("Update Status Error: " . $e->getMessage());
    set_flash('error', 'Gagal memperbarui status sistem.');
}

header('Location: ' . BASE_URL . 'helper/detail_gig.php?id=' . $gig_id);
exit;