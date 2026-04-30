<?php
// client/detail_gig.php
$page_title = 'Monitoring Tugas - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header('Location: ' . BASE_URL . 'auth/login.php'); exit;
}

$gig_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
if (!$gig_id) { header('Location: ' . BASE_URL . 'client/dashboard.php'); exit; }

// 1. Fetch Data Dasar
$stmt = $pdo->prepare("SELECT g.*, c.nama_kategori FROM gigs g JOIN categories c ON g.id_category = c.id_category WHERE g.id_gig = ? AND g.id_client = ?");
$stmt->execute([$gig_id, $_SESSION['user_id']]);
$gig = $stmt->fetch();
if (!$gig) {
    set_flash('error', 'Tugas tidak ditemukan atau bukan milik Anda.');
    header('Location: ' . BASE_URL . 'client/dashboard.php'); exit;
}

// 2. Fallback Auto-Complete (24 Jam)
if ($gig['status'] === 'pending_confirm') {
    $stmt_time = $pdo->prepare("SELECT TIMESTAMPDIFF(HOUR, updated_at, NOW()) as hours_ago FROM gigs WHERE id_gig = ?");
    $stmt_time->execute([$gig_id]);
    $hours_ago = (int)$stmt_time->fetchColumn();
    
    if ($hours_ago >= 24) {
        $pdo->prepare("UPDATE gigs SET status = 'done' WHERE id_gig = ? AND status = 'pending_confirm'")->execute([$gig_id]);
        set_flash('info', 'Pekerjaan otomatis dikonfirmasi selesai (batas waktu 24 jam).');
        header('Location: ' . BASE_URL . 'client/detail_gig.php?id=' . $gig_id); exit;
    }
}

// 3. Cek Status Ulasan (DIPERBAIKI: hapus alias 'r' yang tidak terdefinisi)
$stmt_rev = $pdo->prepare("SELECT rating, komentar FROM reviews WHERE id_gig = ?");
$stmt_rev->execute([$gig_id]);
$review = $stmt_rev->fetch();
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
        <p><i class="fa-regular fa-clock"></i> Diposting: <?= date('d M Y, H:i', strtotime($gig['created_at'])) ?> WIB</p>
      </div>
      <div class="detail-desc">
        <h4>Deskripsi Tugas</h4>
        <p><?= nl2br(htmlspecialchars($gig['deskripsi'])) ?></p>
      </div>
    </div>

    <!-- HANDSHAKE & REVIEW AREA -->
    <div class="action-area">
      <?php if ($gig['status'] === 'pending_confirm'): ?>
        <form action="<?= BASE_URL ?>process/confirm_gig_process.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
          <input type="hidden" name="id_gig" value="<?= $gig['id_gig'] ?>">
          <button type="submit" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
            <i class="fa-solid fa-check"></i> Konfirmasi Pekerjaan Selesai
          </button>
        </form>

      <?php elseif ($gig['status'] === 'done' && !$review): ?>
        <div class="review-section">
          <h4><i class="fa-solid fa-star"></i> Berikan Ulasan</h4>
          <form action="<?= BASE_URL ?>process/review_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="hidden" name="id_gig" value="<?= $gig['id_gig'] ?>">
            <div class="rating-input">
              <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                <label for="star<?= $i ?>"><i class="fa-solid fa-star"></i></label>
              <?php endfor; ?>
            </div>
            <textarea name="comment" placeholder="Ceritakan pengalaman Anda dengan helper..." rows="3" required style="width:100%; margin-top:0.5rem; padding:0.75rem; border:1.5px solid var(--border); border-radius:var(--radius); resize:vertical;"></textarea>
            <button type="submit" class="btn btn-success" style="margin-top:0.5rem; width:100%; justify-content:center;">
              <i class="fa-solid fa-paper-plane"></i> Kirim Ulasan
            </button>
          </form>
        </div>

      <?php elseif ($gig['status'] === 'done' && $review): ?>
        <div class="review-display">
          <div class="review-header">
            <div class="stars">
              <?php for($j=1; $j<=5; $j++): ?>
                <i class="fa-solid fa-star <?= ($j <= $review['rating']) ? 'filled' : '' ?>"></i>
              <?php endfor; ?>
            </div>
            <span class="review-author">Ulasan Terkirim</span>
          </div>
          <p><?= nl2br(htmlspecialchars($review['komentar'])) ?></p>
          <div class="alert alert-success" style="margin-top:0.75rem;">
            <i class="fa-solid fa-circle-check"></i> Pekerjaan selesai & sudah diulas. Terima kasih!
          </div>
        </div>

      <?php else: ?>
        <div class="alert alert-info">
          <i class="fa-solid fa-spinner fa-spin"></i> Menunggu helper menyelesaikan pekerjaan...
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>