<?php
// process/review_process.php
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
$rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
$comment = trim($_POST['comment'] ?? '');

if (!$gig_id || !$rating) {
    set_flash('error', 'Rating wajib diisi antara 1 sampai 5 bintang.');
    header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . ($gig_id ?: 0)); exit;
}

// Verifikasi kepemilikan & status done
$stmt = $pdo->prepare("SELECT id_client, id_helper, status FROM gigs WHERE id_gig = ?");
$stmt->execute([$gig_id]);
$gig = $stmt->fetch();

if (!$gig || $gig['id_client'] != $_SESSION['user_id'] || $gig['status'] !== 'done') {
    set_flash('error', 'Hanya tugas selesai milik Anda yang dapat diulas.');
    header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . $gig_id); exit;
}

// Cek duplikasi ulasan
$stmt = $pdo->prepare("SELECT id_review FROM reviews WHERE id_gig = ?");
$stmt->execute([$gig_id]);
if ($stmt->fetch()) {
    set_flash('info', 'Anda sudah memberikan ulasan untuk tugas ini.');
    header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . $gig_id); exit;
}

// Insert review aman
$stmt = $pdo->prepare("INSERT INTO reviews (id_gig, id_helper, rating, komentar) VALUES (?, ?, ?, ?)");
$stmt->execute([$gig_id, $gig['id_helper'], $rating, $comment]);

set_flash('success', 'Ulasan berhasil dikirim. Terima kasih atas masukan Anda.');
header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . $gig_id);
exit;