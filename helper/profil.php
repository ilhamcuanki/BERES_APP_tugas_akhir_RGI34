<?php
// helper/profil.php
$page_title = 'Profil & Portfolio - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'helper') {
    header('Location: ' . BASE_URL . 'auth/login.php'); exit;
}

$helper_id = $_SESSION['user_id'];

// 1. Fetch Data Helper Terkini
$stmt = $pdo->prepare("SELECT nama, email, bio, keahlian, foto_profil FROM users WHERE id_user = ?");
$stmt->execute([$helper_id]);
$user = $stmt->fetch();

// 2. Query Trust Score Dinamis (AVG Rating)
$stmt_score = $pdo->prepare("SELECT AVG(r.rating) as avg_rating, COUNT(r.id_review) as total_reviews 
                             FROM reviews r 
                             JOIN gigs g ON r.id_gig = g.id_gig 
                             WHERE g.id_helper = ? AND g.status = 'done'");
$stmt_score->execute([$helper_id]);
$score_data = $stmt_score->fetch();
$trust_score = number_format($score_data['avg_rating'] ?? 0, 1);
$total_reviews = $score_data['total_reviews'] ?? 0;

// 3. Mock Wallet Simulation (SUM Budget Tugas Selesai)
$stmt_wallet = $pdo->prepare("SELECT COALESCE(SUM(budget), 0) as total_earned 
                              FROM gigs 
                              WHERE id_helper = ? AND status = 'done'");
$stmt_wallet->execute([$helper_id]);
$wallet_balance = $stmt_wallet->fetchColumn();

// 4. Fetch Portfolio Files dari Folder (Sederhana untuk MVP)
$portfolio_dir = ROOT_PATH . 'assets/img/uploads/';
$portfolio_files = array_filter(scandir($portfolio_dir), function($file) use ($helper_id) {
    return strpos($file, 'portfolio_' . $helper_id . '_') === 0;
});
?>
<div class="container">
  <div class="profile-header">
    <h2><i class="fa-solid fa-id-card"></i> Profil Profesional</h2>
    <a href="<?= BASE_URL ?>helper/dashboard.php" class="btn btn-ghost"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>

  <div class="profile-grid">
    <!-- KOLOM KIRI: Info & Stats -->
    <div class="profile-sidebar">
      <div class="card profile-card">
        <div class="profile-avatar">
          <img src="<?= BASE_URL ?>assets/img/uploads/<?= htmlspecialchars($user['foto_profil']) ?>" 
               alt="Foto Profil" 
               onerror="this.src='https://via.placeholder.com/150?text=User'">
        </div>
        <h3 class="profile-name"><?= htmlspecialchars($user['nama']) ?></h3>
        <p class="profile-role text-muted">Helper Terverifikasi</p>
        
        <div class="stats-grid">
          <div class="stat-item">
            <span class="stat-value text-primary"><?= $trust_score ?>/5</span>
            <span class="stat-label">Trust Score</span>
          </div>
          <div class="stat-item">
            <span class="stat-value text-success">Rp <?= number_format($wallet_balance, 0, ',', '.') ?></span>
            <span class="stat-label">Saldo Terkumpul*</span>
          </div>
          <div class="stat-item">
            <span class="stat-value"><?= $total_reviews ?></span>
            <span class="stat-label">Ulasan</span>
          </div>
        </div>
        <p class="text-muted" style="font-size:0.75rem; margin-top:0.5rem;">*Simulasi: Total nilai tugas selesai</p>
      </div>
    </div>

    <!-- KOLOM KANAN: Forms & Content -->
    <div class="profile-content">
      <!-- Form Edit Profil -->
      <div class="card">
        <h4><i class="fa-solid fa-pen-to-square"></i> Edit Informasi</h4>
        <form action="<?= BASE_URL ?>process/update_profile_process.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
          
          <div class="form-group">
            <label class="form-label">Foto Profil</label>
            <input type="file" name="foto_profil" accept="image/png, image/jpeg, image/webp" class="form-control">
          </div>
          
          <div class="form-group">
            <label class="form-label">Keahlian Utama</label>
            <input type="text" name="keahlian" value="<?= htmlspecialchars($user['keahlian'] ?? '') ?>" 
                   class="form-control" placeholder="Contoh: Perbaikan Listrik, Cat Rumah">
          </div>
          
          <div class="form-group">
            <label class="form-label">Bio / Tentang Saya</label>
            <textarea name="bio" rows="3" class="form-control"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
          </div>
          
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
      </div>

      <!-- Upload Portfolio -->
      <div class="card" style="margin-top:1.5rem;">
        <h4><i class="fa-solid fa-images"></i> Tambah Portfolio</h4>
        <form action="<?= BASE_URL ?>process/upload_portfolio_process.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
          <div class="form-group">
            <label class="form-label">Upload Foto Hasil Kerja (Maks 3 sekaligus)</label>
            <input type="file" name="portfolio[]" multiple accept="image/png, image/jpeg, image/webp" class="form-control">
          </div>
          <button type="submit" class="btn btn-outline">Upload Foto</button>
        </form>
      </div>

      <!-- Gallery Portfolio -->
      <?php if (!empty($portfolio_files)): ?>
      <div class="card" style="margin-top:1.5rem;">
        <h4>Galeri Pekerjaan</h4>
        <div class="portfolio-grid">
          <?php foreach ($portfolio_files as $file): ?>
            <img src="<?= BASE_URL ?>assets/img/uploads/<?= htmlspecialchars($file) ?>" 
                 class="portfolio-img" 
                 alt="Portfolio"
                 loading="lazy">
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>