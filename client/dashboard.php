<?php
// client/dashboard.php
$page_title = 'Riwayat Tugas - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header('Location: ' . BASE_URL . 'auth/login.php'); exit;
}

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
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-list-check"></i> Riwayat Tugas Anda</h2>
    <a href="<?= BASE_URL ?>client/post_gig.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Tugas Baru</a>
  </div>

  <?php if (empty($gigs)): ?>
    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Belum ada tugas yang diposting. Mulai dengan membuat tugas pertama Anda.</div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($gigs as $gig): ?>
        <a href="<?= BASE_URL ?>client/detail_gig.php?id=<?= $gig['id_gig'] ?>" class="card gig-link-card">
          <div class="card-header">
            <h3 class="card-title"><?= htmlspecialchars($gig['judul']) ?></h3>
            <span class="status-badge status-<?= $gig['status'] ?>"><?= strtoupper($gig['status']) ?></span>
          </div>
          <div class="card-meta">
            <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($gig['nama_kategori']) ?></span>
            <span><i class="fa-solid fa-money-bill-wave"></i> Rp <?= number_format($gig['budget'], 0, ',', '.') ?></span>
          </div>
          <div class="card-footer">
            <i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($gig['created_at'])) ?> WIB
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>