<?php
// index.php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🚦 SMART ROUTER: Cek sesi aktif & redirect sesuai role
if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    $target_dashboard = match($_SESSION['role']) {
        'admin'  => BASE_URL . 'admin/dashboard.php',
        'helper' => BASE_URL . 'helper/dashboard.php',
        default  => BASE_URL . 'client/dashboard.php'
    };
    header("Location: $target_dashboard");
    exit; // WAJIB: Hentikan eksekusi agar HTML landing page tidak ter-render
}

// Jika tidak ada session, lanjut render landing page di bawah ini
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BERES - Solusi Jasa Lokal Terpercaya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  
</head>
<body>
  <nav class="navbar">
    <div class="container nav-content">
      <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div> BERES
      </a>
      <div class="nav-actions">
        <a href="#" class="btn btn-ghost">
          <i class="fa-regular fa-circle-question"></i> <span>Bantuan</span>
        </a>
        <a href="#" class="btn btn-outline"><i class="fa-regular fa-user"></i> <span>Masuk</span></a>
        <a href="#" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> <span>Daftar</span></a>
      </div>
    </div>
  </nav>

  <main>
    <section class="hero">
      <div class="container hero-content">
        <div class="badge"><i class="fa-solid fa-check-circle"></i> Platform Gig Ekonomi Terpercaya</div>
        <h1>Solusi Cepat untuk Kebutuhan Rumah Tangga</h1>
        <p>Hubungkan kebutuhan Anda dengan penyedia jasa lokal terpercaya dalam hitungan menit. Aman, transparan, dan tervalidasi oleh sistem kami.</p>
        <div class="hero-actions">
          <a href="#" class="btn btn-primary hero-btn"><i class="fa-solid fa-magnifying-glass-location"></i> Cari Penyedia Jasa</a>
          <a href="#" class="btn btn-outline hero-btn" style="background:var(--surface);"><i class="fa-solid fa-briefcase"></i> Tawarkan Keahlian</a>
        </div>
      </div>
    </section>

    <section class="features">
      <div class="container">
        <div class="section-header">
          <h2>Mengapa Memilih BERES?</h2>
          <p>Sistem yang dirancang untuk efisiensi dan keamanan bersama</p>
        </div>
        <div class="grid">
          <div class="card">
            <div class="card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <h3>Respon Instan</h3>
            <p>Sistem First-Responder memastikan tugas Anda diambil oleh helper terdekat. Pantau progres secara real-time tanpa perlu bertanya-tanya.</p>
          </div>
          <div class="card">
            <div class="card-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <h3>Keamanan Berlapis</h3>
            <p>Verifikasi identitas, sistem rating dua arah (Handshake), dan pencatatan transaksi transparan melindungi kedua belah pihak.</p>
          </div>
          <div class="card">
            <div class="card-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            <h3>Ekonomi Mikro Terkelola</h3>
            <p>Tentukan budget Anda sendiri. Tidak ada biaya tersembunyi. Semua alur kerja tercatat rapi untuk keperluan audit dan laporan.</p>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="logo" style="justify-content:center; margin-bottom:1rem;">
        <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div> BERES
      </div>
      <p>&copy; <?= date('Y') ?> BERES Project. Basis Ekonomi Rakyat Efisiensi Sistem.</p>
      <div class="footer-links">
        <a href="#">Tentang Kami</a>
        <a href="#">Syarat & Ketentuan</a>
        <a href="#">Kebijakan Privasi</a>
        <a href="#">Kontak</a>
      </div>
    </div>
  </footer>

  <script>
    // Intersection Observer untuk animasi card saat scroll
    document.addEventListener('DOMContentLoaded', () => {
      const cards = document.querySelectorAll('.card');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.style.opacity = '1';
              entry.target.style.transform = 'translateY(0)';
            }, index * 100);
          }
        });
      }, { threshold: 0.1 });

      cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
      });
    });
  </script>
</body>
</html>