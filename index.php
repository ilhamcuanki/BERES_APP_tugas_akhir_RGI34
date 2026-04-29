<?php
// index.php
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
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
  <link rel="stylesheet" href="<?= ROOT_PATH ?>assets/css/style.css">
  <style>
    :root {
      --bg: #f8fafc; --surface: #ffffff;
      --primary: #0f4c75; --primary-light: #3282b8;
      --accent: #1b9aaa; --text-main: #1a202c; --text-muted: #6b7280;
      --border: #e2e8f0; --radius: 12px;
      --shadow: 0 10px 15px -3px rgba(15,76,117,0.08);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text-main); line-height:1.6; }
    .container { max-width:1200px; margin:0 auto; padding:0 2rem; }
    
    /* Navbar */
    .navbar { background:var(--surface); border-bottom:1px solid var(--border); padding:1rem 0; position:sticky; top:0; z-index:50; }
    .nav-content { display:flex; justify-content:space-between; align-items:center; }
    .logo { display:flex; align-items:center; gap:0.6rem; font-size:1.4rem; font-weight:800; color:var(--primary); text-decoration:none; }
    .logo-icon { width:34px; height:34px; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:#fff; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:0.95rem; }
    .nav-actions { display:flex; gap:0.75rem; }
    
    /* Buttons */
    .btn { display:inline-flex; align-items:center; gap:0.5rem; padding:0.6rem 1.2rem; border-radius:var(--radius); font-weight:500; font-size:0.9rem; cursor:pointer; transition:all 0.2s; border:1.5px solid transparent; text-decoration:none; }
    .btn-primary { background:var(--primary); color:#fff; }
    .btn-primary:hover { background:var(--primary-light); transform:translateY(-1px); }
    .btn-outline { background:transparent; border-color:var(--primary); color:var(--primary); }
    .btn-outline:hover { background:var(--primary); color:#fff; }
    .btn-ghost { background:transparent; color:var(--text-muted); }
    .btn-ghost:hover { background:var(--bg); color:var(--text-main); }
    
    /* Hero */
    .hero { padding:5rem 0 4rem; background:linear-gradient(180deg, #eff6ff 0%, var(--bg) 100%); }
    .hero-content { text-align:center; max-width:700px; margin:0 auto; }
    .badge { display:inline-flex; align-items:center; gap:0.4rem; background:#dbeafe; color:var(--primary); padding:0.35rem 0.85rem; border-radius:99px; font-size:0.8rem; font-weight:600; margin-bottom:1.5rem; }
    .hero h1 { font-size:clamp(2rem,5vw,3rem); line-height:1.15; color:var(--primary); margin-bottom:1.25rem; font-weight:800; letter-spacing:-0.02em; }
    .hero p { color:var(--text-muted); margin-bottom:2.5rem; font-size:1.1rem; max-width:600px; margin-left:auto; margin-right:auto; }
    .hero-actions { display:flex; justify-content:center; gap:1rem; flex-wrap:wrap; }
    .hero-btn { padding:0.85rem 1.5rem; font-size:1rem; }
    
    /* Features - PERBAIKAN SPACING */
    .features { padding:4rem 0 5rem; }
    .section-header { text-align:center; margin-bottom:3rem; }
    .section-header h2 { font-size:2rem; color:var(--primary); margin-bottom:0.5rem; font-weight:700; }
    .section-header p { color:var(--text-muted); margin-top:0.5rem; }
    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem; margin-top:2.5rem; }
    .card { background:var(--surface); padding:2rem; border-radius:var(--radius); border:1px solid var(--border); transition:all 0.3s ease; position:relative; overflow:hidden; }
    .card:hover { border-color:var(--primary-light); transform:translateY(-4px); box-shadow:var(--shadow); }
    .card-icon { width:52px; height:52px; background:linear-gradient(135deg, #eff6ff, #dbeafe); color:var(--primary); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:1.25rem; }
    .card h3 { font-size:1.25rem; margin-bottom:0.75rem; color:var(--primary); font-weight:600; }
    .card p { color:var(--text-muted); line-height:1.7; font-size:0.95rem; }
    
    /* Footer */
    .footer { background:var(--surface); border-top:1px solid var(--border); padding:2.5rem 0; margin-top:4rem; text-align:center; }
    .footer p { color:var(--text-muted); font-size:0.9rem; margin-top:0.5rem; }
    .footer-links { display:flex; justify-content:center; gap:1.5rem; margin-top:1rem; }
    .footer-links a { color:var(--primary-light); text-decoration:none; font-size:0.9rem; font-weight:500; transition:color 0.2s; }
    .footer-links a:hover { color:var(--primary); }
    
    /* Responsive Optimizations */
    @media(max-width:1024px) {
      .container { padding:0 1.75rem; }
      .hero { padding:4rem 0 3rem; }
      .features { padding:3.5rem 0 4rem; }
    }
    
    @media(max-width:768px) {
      .container { padding:0 1.5rem; }
      .nav-actions .btn span { display:none; }
      .nav-actions .btn { padding:0.6rem 0.8rem; }
      .hero { padding:3.5rem 0 2.5rem; }
      .hero h1 { font-size:1.8rem; }
      .hero p { font-size:1rem; }
      .hero-actions { flex-direction:column; align-items:stretch; }
      .hero-btn { width:100%; justify-content:center; }
      .features { padding:3rem 0 3.5rem; }
      .grid { grid-template-columns:1fr; gap:1.75rem; }
      .section-header h2 { font-size:1.75rem; }
      .card { padding:1.75rem; }
    }
    
    @media(max-width:480px) {
      .container { padding:0 1.25rem; }
      .logo { font-size:1.25rem; }
      .logo-icon { width:30px; height:30px; font-size:0.85rem; }
      .hero { padding:3rem 0 2rem; }
      .hero h1 { font-size:1.65rem; }
      .badge { font-size:0.7rem; padding:0.25rem 0.65rem; }
      .features { padding:2.5rem 0 3rem; }
      .card { padding:1.5rem; }
      .card-icon { width:46px; height:46px; font-size:1.25rem; }
      .footer { padding:2rem 0; }
    }
  </style>
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