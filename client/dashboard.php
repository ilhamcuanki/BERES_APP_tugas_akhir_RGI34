<?php
// client/dashboard.php
$page_title = 'Dashboard Client';
require_once __DIR__ . '/../utils/flash.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><i class="fa-solid fa-list-check" style="color:var(--primary)"></i> Dashboard Client</h1>
  <p class="text-muted">Kelola dan pantau tugas yang Anda posting.</p>
  <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Fitur listing & status tugas akan tersedia di Fase 4.</div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>