<?php
// client/post_gig.php
$page_title = 'Posting Tugas Baru - BERES';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once __DIR__ . '/../includes/header.php';

// Guard: Hanya client yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

// Fetch kategori untuk dropdown
$categories = $pdo->query("SELECT id_category, nama_kategori FROM categories ORDER BY nama_kategori ASC")->fetchAll();
?>
<div class="container">
  <div class="card" style="max-width: 800px; margin: 0 auto; padding: 2rem;">
    <h2 style="color: var(--primary); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.6rem;">
      <i class="fa-solid fa-plus-circle"></i> Posting Tugas Baru
    </h2>
    
    <form action="<?= BASE_URL ?>process/post_gig_process.php" method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
      
      <div class="form-group" style="margin-bottom: 1.2rem;">
        <label class="form-label">Judul Tugas</label>
        <div class="input-wrap">
          <i class="fa-solid fa-heading"></i>
          <input type="text" name="judul" required placeholder="Contoh: Perbaiki Keran Bocor di Dapur" style="padding-left: 2.4rem;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1.2rem;">
        <label class="form-label">Kategori Jasa</label>
        <div class="input-wrap">
          <i class="fa-solid fa-tags"></i>
          <select name="id_category" required style="padding-left: 2.4rem;">
            <option value="" disabled selected>Pilih kategori...</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id_category'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1.2rem;">
        <label class="form-label">Lokasi Pengerjaan</label>
        <div class="input-wrap">
          <i class="fa-solid fa-location-dot"></i>
          <input type="text" name="lokasi" required placeholder="Contoh: Perumahan Griya Indah Blok C No. 12" style="padding-left: 2.4rem;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1.2rem;">
        <label class="form-label">Budget yang Ditawarkan (Rp)</label>
        <div class="input-wrap">
          <i class="fa-solid fa-money-bill-wave"></i>
          <input type="number" name="budget" required min="10000" step="5000" placeholder="50000" style="padding-left: 2.4rem;">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 1.5rem;">
        <label class="form-label">Deskripsi Detail</label>
        <div class="input-wrap">
          <i class="fa-solid fa-align-left" style="top: 1.2rem;"></i>
          <textarea name="deskripsi" required rows="4" placeholder="Jelaskan masalah secara detail, jam kerja yang diinginkan, atau akses khusus ke lokasi..." style="padding: 0.75rem 0.75rem 0.75rem 2.4rem; width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius); font-family: inherit; font-size: 0.95rem; background: #fafbfc;"></textarea>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 1rem;">
        <i class="fa-solid fa-paper-plane"></i> Publikasikan Tugas
      </button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>