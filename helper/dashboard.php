<?php
// helper/dashboard.php
$page_title = 'Cari Pekerjaan - BERES';

// 1. Muat dependensi wajib (SERVER PATH)
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/flash.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once __DIR__ . '/../includes/header.php';

// 2. Guard Role: Hanya Helper yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'helper') {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

// 3. Handle Filter Kategori (GET)
$selected_cat = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;
$cat_filter_sql = '';
$params = [];

if ($selected_cat) {
    $cat_filter_sql = "AND g.id_category = ?";
    $params[] = $selected_cat;
}

// 4. Query Gig Open (JOIN aman dengan prepared statement)
$sql = "SELECT g.id_gig, g.judul, g.budget, g.lokasi, g.created_at, 
               c.nama_kategori, u.nama AS client_nama
        FROM gigs g
        JOIN categories c ON g.id_category = c.id_category
        JOIN users u ON g.id_client = u.id_user
        WHERE g.status = 'open' $cat_filter_sql
        ORDER BY g.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$gigs = $stmt->fetchAll();

// 5. Fetch kategori untuk dropdown filter
$categories = $pdo->query("SELECT id_category, nama_kategori FROM categories ORDER BY nama_kategori ASC")->fetchAll();
?>
<div class="container">
  <div class="feed-header">
    <h2 class="page-title"><i class="fa-solid fa-magnifying-glass"></i> Pekerjaan Tersedia</h2>
    <form action="<?= BASE_URL ?>helper/dashboard.php" method="GET" class="filter-form">
      <select name="category" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id_category'] ?>" <?= ($selected_cat == $cat['id_category']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nama_kategori']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <?php if (empty($gigs)): ?>
    <div class="alert alert-info" style="margin-top:1.5rem;">
      <i class="fa-solid fa-circle-info"></i> Belum ada tugas tersedia. Coba ubah filter atau tunggu tugas baru diposting.
    </div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($gigs as $gig): ?>
        <div class="card gig-card">
          <div class="gig-header">
            <h3 class="gig-title"><?= htmlspecialchars($gig['judul']) ?></h3>
            <span class="status-badge status-open">OPEN</span>
          </div>
          <div class="gig-meta">
            <span><i class="fa-solid fa-user"></i> <?= htmlspecialchars($gig['client_nama']) ?></span>
            <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($gig['nama_kategori']) ?></span>
            <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($gig['lokasi']) ?></span>
            <span class="gig-budget"><i class="fa-solid fa-money-bill-wave"></i> Rp <?= number_format($gig['budget'], 0, ',', '.') ?></span>
          </div>
          <div class="gig-footer">
            <span class="text-muted"><i class="fa-regular fa-clock"></i> <?= date('d M Y, H:i', strtotime($gig['created_at'])) ?> WIB</span>
            <!-- Link detail aktif di Fase 7 -->
            <a href="<?= BASE_URL ?>helper/detail_gig.php?id=<?= $gig['id_gig'] ?>" class="btn btn-outline btn-sm">
              <i class="fa-solid fa-arrow-right"></i> Lihat Detail
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>