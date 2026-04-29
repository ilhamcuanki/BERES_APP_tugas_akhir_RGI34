<?php
// client/dashboard.php
$page_title = 'Riwayat Tugas - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

// Ambil tugas milik client yang sedang login
$stmt = $pdo->prepare("
    SELECT g.id_gig, g.judul, g.budget, g.status, g.created_at, c.nama_kategori 
    FROM gigs g 
    JOIN categories c ON g.id_category = c.id_category 
    WHERE g.id_client = ? 
    ORDER BY g.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$gigs = $stmt->fetchAll();
?>
<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <h2 style="color: var(--primary); display: flex; align-items: center; gap: 0.6rem;">
      <i class="fa-solid fa-list-check"></i> Riwayat Tugas Anda
    </h2>
    <a href="<?= BASE_URL ?>client/post_gig.php" class="btn btn-primary">
      <i class="fa-solid fa-plus"></i> Tambah Tugas Baru
    </a>
  </div>

  <?php if (empty($gigs)): ?>
    <div class="alert alert-info" style="margin-top: 1rem;">
      <i class="fa-solid fa-circle-info"></i> Belum ada tugas yang diposting. Mulai dengan membuat tugas pertama Anda.
    </div>
  <?php else: ?>
    <div class="grid" style="margin-top: 1rem;">
      <?php foreach ($gigs as $gig): ?>
        <div class="card">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <h3 style="font-size:1.1rem; margin:0; color: var(--text-main);"><?= htmlspecialchars($gig['judul']) ?></h3>
            <span class="status-badge status-<?= $gig['status'] ?>"><?= htmlspecialchars($gig['status']) ?></span>
          </div>
          <div class="text-muted" style="font-size:0.85rem; margin-bottom:0.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($gig['nama_kategori']) ?></span>
            <span><i class="fa-solid fa-money-bill-wave"></i> Rp <?= number_format($gig['budget'], 0, ',', '.') ?></span>
          </div>
          <div class="text-muted" style="font-size:0.8rem;">
            <i class="fa-regular fa-clock"></i> Diposting: <?= date('d M Y, H:i', strtotime($gig['created_at'])) ?> WIB
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>