<?php
// helper/detail_gig.php
$page_title = 'Detail Tugas - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once __DIR__ . '/../includes/header.php';

// Guard Role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'helper') {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

$gig_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$gig_id) {
    header('Location: ' . BASE_URL . 'helper/dashboard.php');
    exit;
}

// Fetch Data Gig
$stmt = $pdo->prepare("
    SELECT g.*, c.nama_kategori, u.nama AS client_nama
    FROM gigs g
    JOIN categories c ON g.id_category = c.id_category
    JOIN users u ON g.id_client = u.id_user
    WHERE g.id_gig = ?
");
$stmt->execute([$gig_id]);
$gig = $stmt->fetch();

if (!$gig) {
    set_flash('error', 'Tugas tidak ditemukan.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php');
    exit;
}
?>
<div class="container">
  <div class="detail-wrapper">
    <a href="<?= BASE_URL ?>helper/dashboard.php" class="btn btn-ghost" style="margin-bottom:1rem; padding:0.5rem 0;">
      <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tugas
    </a>
    
    <div class="card detail-card">
      <div class="detail-header">
        <h2 class="detail-title"><?= htmlspecialchars($gig['judul']) ?></h2>
        <span class="status-badge status-<?= $gig['status'] ?>"><?= strtoupper($gig['status']) ?></span>
      </div>
      
      <div class="detail-grid">
        <div class="detail-info">
          <p><i class="fa-solid fa-user"></i> <strong>Client:</strong> <?= htmlspecialchars($gig['client_nama']) ?></p>
          <p><i class="fa-solid fa-tag"></i> <strong>Kategori:</strong> <?= htmlspecialchars($gig['nama_kategori']) ?></p>
          <p><i class="fa-solid fa-location-dot"></i> <strong>Lokasi:</strong> <?= htmlspecialchars($gig['lokasi']) ?></p>
          <p><i class="fa-solid fa-money-bill-wave"></i> <strong>Budget:</strong> <span class="price-tag">Rp <?= number_format($gig['budget'], 0, ',', '.') ?></span></p>
          <p><i class="fa-regular fa-clock"></i> <strong>Diposting:</strong> <?= date('d M Y, H:i', strtotime($gig['created_at'])) ?> WIB</p>
        </div>
        
        <div class="detail-desc">
          <h4>Deskripsi Pekerjaan</h4>
          <p><?= nl2br(htmlspecialchars($gig['deskripsi'])) ?></p>
        </div>
      </div>

      <?php if ($gig['status'] === 'open'): ?>
        <form action="<?= BASE_URL ?>process/claim_gig_process.php" method="POST" style="margin-top: 1.5rem; text-align: right;">
          <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
          <input type="hidden" name="id_gig" value="<?= $gig['id_gig'] ?>">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-hand-pointer"></i> Ambil Tugas Sekarang
          </button>
        </form>
      <?php elseif ($gig['status'] === 'taken' && $gig['id_helper'] == $_SESSION['user_id']): ?>
        <div class="alert alert-info" style="margin-top:1rem;">
          <i class="fa-solid fa-check-circle"></i> Anda sudah mengambil tugas ini. Tombol update status akan aktif setelah Anda tiba di lokasi (Fase 7).
        </div>
      <?php else: ?>
        <div class="alert alert-error" style="margin-top:1rem;">
          <i class="fa-solid fa-ban"></i> Tugas ini tidak tersedia atau sudah diambil Helper lain.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>