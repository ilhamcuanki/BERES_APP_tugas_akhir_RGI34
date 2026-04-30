<?php
// client/detail_gig.php
$page_title = 'Monitoring Tugas - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header('Location: ' . BASE_URL . 'auth/login.php'); exit;
}

$gig_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$gig_id) { header('Location: ' . BASE_URL . 'client/dashboard.php'); exit; }

$stmt = $pdo->prepare("SELECT g.*, c.nama_kategori FROM gigs g JOIN categories c ON g.id_category = c.id_category WHERE g.id_gig = ? AND g.id_client = ?");
$stmt->execute([$gig_id, $_SESSION['user_id']]);
$gig = $stmt->fetch();

if (!$gig) {
    set_flash('error', 'Tugas tidak ditemukan atau bukan milik Anda.');
    header('Location: ' . BASE_URL . 'client/dashboard.php'); exit;
}
?>
<div class="container">
  <a href="<?= BASE_URL ?>client/dashboard.php" class="btn btn-ghost" style="margin-bottom:1rem; padding:0.5rem 0;">
    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
  </a>

  <div class="card client-detail-card">
    <div class="detail-header">
      <h2 class="detail-title"><?= htmlspecialchars($gig['judul']) ?></h2>
      <span class="status-badge status-<?= $gig['status'] ?>"><?= strtoupper(str_replace('_', ' ', $gig['status'])) ?></span>
    </div>

    <div class="progress-track">
      <div class="step <?= in_array($gig['status'], ['taken','ongoing','pending_confirm','done']) ? 'active' : '' ?>">
        <i class="fa-solid fa-hand-pointer"></i><span>Diambil</span>
      </div>
      <div class="step <?= in_array($gig['status'], ['ongoing','pending_confirm','done']) ? 'active' : '' ?>">
        <i class="fa-solid fa-location-dot"></i><span>Dikerjakan</span>
      </div>
      <div class="step <?= in_array($gig['status'], ['pending_confirm','done']) ? 'active' : '' ?>">
        <i class="fa-solid fa-spinner"></i><span>Selesai</span>
      </div>
      <div class="step <?= $gig['status'] === 'done' ? 'active' : '' ?>">
        <i class="fa-solid fa-circle-check"></i><span>Validasi</span>
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-info">
        <p><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($gig['nama_kategori']) ?></p>
        <p><i class="fa-solid fa-money-bill-wave"></i> Rp <?= number_format($gig['budget'], 0, ',', '.') ?></p>
      </div>
      <div class="detail-desc">
        <h4>Deskripsi Tugas</h4>
        <p><?= nl2br(htmlspecialchars($gig['deskripsi'])) ?></p>
      </div>
    </div>

    <?php if ($gig['status'] === 'pending_confirm'): ?>
      <div class="alert alert-warning" style="margin-top:1.5rem;">
        <i class="fa-solid fa-bell"></i> <strong>Helper mengklaim pekerjaan selesai.</strong><br>
        Silakan verifikasi hasil pekerjaan di panel dashboard Anda.
      </div>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>