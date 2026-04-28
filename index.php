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
    .container { max-width:1100px; margin:0 auto; padding:0 1.5rem; }
    
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
    
    /* Hero */
    .hero { padding:4.5rem 0 3.5rem; background:linear-gradient(180deg, #eff6ff 0%, var(--bg) 100%); }
    .hero-content { text-align:center; max-width:650px; margin:0 auto; }
    .badge { display:inline-flex; align-items:center; gap:0.4rem; background:#dbeafe; color:var(--primary); padding:0.3rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:600; margin-bottom:1.2rem; }
    .hero h1 { font-size:clamp(1.8rem,4vw,2.6rem); line-height:1.2; color:var(--primary); margin-bottom:1rem; font-weight:800; }
    .hero p { color:var(--text-muted); margin-bottom:2rem; }
    .hero-actions { display:flex; justify-content:center; gap:1rem; flex-wrap:wrap; }
    
    /* Cards */
    .features { padding:3rem 0 4rem; }
    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1.5rem; margin-top:2rem; }
    .card { background:var(--surface); padding:1.8rem; border-radius:var(--radius); border:1px solid var(--border); transition:0.3s; }
    .card:hover { border-color:var(--primary-light); transform:translateY(-3px); box-shadow:var(--shadow); }
    .card-icon { width:46px; height:46px; background:#e0f2fe; color:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; margin-bottom:1rem; }
    .card h3 { font-size:1.15rem; margin-bottom:0.5rem; color:var(--primary); }
    .card p { color:var(--text-muted); font-size:0.9rem; }
    
    /* Footer */
    .footer { background:var(--surface); border-top:1px solid var(--border); padding:2rem 0; margin-top:3rem; text-align:center; }
    .footer p { color:var(--text-muted); font-size:0.85rem; margin-top:0.5rem; }
    
    @media(max-width:768px) { .nav-actions .btn span{display:none;} .hero{padding:3rem 0;} }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="container nav-content">
      <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div> BERES
      </a>
      <div class="nav-actions">
        <a href="#" class="btn btn-ghost" style="background:transparent; color:var(--text-muted);">
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
          <a href="#" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass-location"></i> Cari Penyedia Jasa</a>
          <a href="#" class="btn btn-outline" style="background:var(--surface);"><i class="fa-solid fa-briefcase"></i> Tawarkan Keahlian</a>
        </div>
      </div>
    </section>

    <section class="features container">
      <div class="grid">
        <div class="card">
          <div class="card-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
          <h3>Respon Instan</h3>
          <p>Sistem First-Responder memastikan tugas Anda diambil oleh helper terdekat. Pantau progres secara real-time.</p>
        </div>
        <div class="card">
          <div class="card-icon"><i class="fa-solid fa-shield-halved"></i></div>
          <h3>Keamanan Berlapis</h3>
          <p>Verifikasi identitas, sistem rating dua arah, dan pencatatan transaksi transparan melindungi kedua belah pihak.</p>
        </div>
        <div class="card">
          <div class="card-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
          <h3>Ekonomi Mikro</h3>
          <p>Tentukan budget Anda sendiri. Tidak ada biaya tersembunyi. Semua alur kerja tercatat rapi untuk audit.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="logo" style="justify-content:center; margin-bottom:0.5rem;">
        <div class="logo-icon"><i class="fa-solid fa-bolt"></i></div> BERES
      </div>
      <p>&copy; <?= date('Y') ?> BERES Project. Basis Ekonomi Rakyat Efisiensi Sistem.</p>
    </div>
  </footer>
</body>
</html>