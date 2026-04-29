<?php
// admin/dashboard.php
$page_title = 'Dashboard Admin';
require_once __DIR__ . '/../utils/flash.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><i class="fa-solid fa-gauge-high" style="color:var(--primary)"></i> Dashboard Admin</h1>
  <p class="text-muted">Monitoring pengguna, kategori, dan audit transaksi.</p>
  <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Panel back-office akan tersedia di Fase 10.</div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>