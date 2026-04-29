<?php
// helper/dashboard.php
$page_title = 'Dashboard Helper';
require_once __DIR__ . '/../utils/flash.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <h1><i class="fa-solid fa-magnifying-glass" style="color:var(--primary)"></i> Dashboard Helper</h1>
  <p class="text-muted">Temukan pekerjaan terbaru & kelola progres Anda.</p>
  <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i> Feed gig & manajemen status akan tersedia di Fase 5-7.</div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>