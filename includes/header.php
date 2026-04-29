<?php
// includes/header.php
// 1. Muat constants.php menggunakan __DIR__ (menghindari error ROOT_PATH belum terdefinisi)
require_once __DIR__ . '/../config/constants.php';

// 2. Setelah constants.php sukses, ROOT_PATH sudah ada. Muat helper lainnya.
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id'], $_SESSION['role']);
$role         = $is_logged_in ? $_SESSION['role'] : null;
$user_name    = $is_logged_in ? htmlspecialchars($_SESSION['nama'] ?? 'User', ENT_QUOTES, 'UTF-8') : null;

// Definisikan menu berdasarkan role
$nav_links = [];
if (!$is_logged_in) {
    $nav_links = [
        ['label' => 'Masuk', 'url' => BASE_URL . 'auth/login.php', 'icon' => 'fa-right-to-bracket', 'class' => 'btn-outline'],
        ['label' => 'Daftar', 'url' => BASE_URL . 'auth/register.php', 'icon' => 'fa-user-plus', 'class' => 'btn-primary'],
    ];
} elseif ($role === 'admin') {
    $nav_links = [
        ['label' => 'Dashboard Admin', 'url' => BASE_URL . 'admin/dashboard.php', 'icon' => 'fa-gauge-high', 'class' => 'btn-ghost'],
        ['label' => 'Kelola Kategori', 'url' => BASE_URL . 'admin/manage_categories.php', 'icon' => 'fa-tags', 'class' => 'btn-ghost'],
        ['label' => 'Logout', 'url' => BASE_URL . 'process/logout.php', 'icon' => 'fa-arrow-right-from-bracket', 'class' => 'btn-outline'],
    ];
} elseif ($role === 'client') {
    $nav_links = [
        ['label' => 'Posting Tugas', 'url' => BASE_URL . 'client/post_gig.php', 'icon' => 'fa-plus-circle', 'class' => 'btn-primary'],
        ['label' => 'Riwayat Tugas', 'url' => BASE_URL . 'client/dashboard.php', 'icon' => 'fa-list-check', 'class' => 'btn-ghost'],
        ['label' => "Halo, $user_name", 'url' => '#', 'icon' => 'fa-user-circle', 'class' => 'btn-ghost'],
        ['label' => 'Logout', 'url' => BASE_URL . 'process/logout.php', 'icon' => 'fa-arrow-right-from-bracket', 'class' => 'btn-outline'],
    ];
} elseif ($role === 'helper') {
    $nav_links = [
        ['label' => 'Cari Pekerjaan', 'url' => BASE_URL . 'helper/dashboard.php', 'icon' => 'fa-magnifying-glass', 'class' => 'btn-primary'],
        ['label' => 'Profil & Portfolio', 'url' => BASE_URL . 'helper/profil.php', 'icon' => 'fa-id-card', 'class' => 'btn-ghost'],
        ['label' => "Halo, $user_name", 'url' => '#', 'icon' => 'fa-user-circle', 'class' => 'btn-ghost'],
        ['label' => 'Logout', 'url' => BASE_URL . 'process/logout.php', 'icon' => 'fa-arrow-right-from-bracket', 'class' => 'btn-outline'],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?? 'BERES - Solusi Jasa Lokal' ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
  <nav class="navbar">
    <div class="container nav-content">
      <a href="<?= BASE_URL ?>index.php" class="logo">
        <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div> BERES
      </a>
      <div class="nav-actions">
        <?php foreach ($nav_links as $link): ?>
          <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8') ?>" class="btn <?= $link['class'] ?>">
            <i class="fa-solid <?= $link['icon'] ?>"></i> <span><?= $link['label'] ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </nav>

  <div class="container">
    <?= render_flash() ?>
  </div>

  <main class="main-wrapper">