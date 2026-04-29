<?php
// auth/register.php
require_once __DIR__ . '/../config/constants.php';
require_once ROOT_PATH . 'helpers/csrf.php';
require_once ROOT_PATH . 'helpers/flash.php';

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - BERES</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --bg:#f8fafc; --surface:#fff; --primary:#0f4c75; --primary-light:#3282b8; --text-main:#1a202c; --text-muted:#6b7280; --border:#e2e8f0; --radius:12px; }
    *{margin:0;padding:0;box-sizing:border-box} body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text-main);line-height:1.6;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
    .auth-card{background:var(--surface);padding:2.5rem;border-radius:var(--radius);box-shadow:0 10px 25px rgba(0,0,0,0.08);width:100%;max-width:420px;border:1px solid var(--border)}
    .auth-header{text-align:center;margin-bottom:2rem} .auth-header h1{color:var(--primary);font-size:1.6rem;font-weight:700} .auth-header p{color:var(--text-muted);font-size:0.9rem;margin-top:0.4rem}
    .form-group{margin-bottom:1.2rem} .form-label{display:block;margin-bottom:0.4rem;font-weight:500;font-size:0.9rem;color:var(--text-main)}
    .input-wrap{position:relative} .input-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.9rem}
    .input-wrap input,.input-wrap select{width:100%;padding:0.75rem 0.75rem 0.75rem 2.4rem;border:1.5px solid var(--border);border-radius:var(--radius);font-size:0.95rem;transition:0.2s;background:#fafbfc}
    .input-wrap input:focus,.input-wrap select:focus{outline:none;border-color:var(--primary-light);background:#fff}
    .btn-submit{width:100%;padding:0.85rem;background:var(--primary);color:#fff;border:none;border-radius:var(--radius);font-weight:600;font-size:1rem;cursor:pointer;transition:0.2s;margin-top:0.5rem}
    .btn-submit:hover{background:var(--primary-light);transform:translateY(-1px)}
    .alert{padding:0.8rem;border-radius:8px;margin-bottom:1rem;font-size:0.9rem} .alert-success{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0} .alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
    .auth-footer{text-align:center;margin-top:1.2rem;font-size:0.9rem;color:var(--text-muted)} .auth-footer a{color:var(--primary);text-decoration:none;font-weight:500}
    .auth-footer a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="auth-header">
      <h1><i class="fa-solid fa-user-plus" style="color:var(--primary);margin-right:0.4rem"></i> Daftar</h1>
      <p>Bergabung sebagai pencari jasa atau penyedia keahlian</p>
    </div>
    <?= render_flash() ?>
    <form action="<?= BASE_URL ?>process/register_process.php" method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
      <div class="form-group">
        <label class="form-label">Nama Lengkap</label>
        <div class="input-wrap">
          <i class="fa-regular fa-user"></i>
          <input type="text" name="nama" required placeholder="Masukkan nama lengkap">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Alamat Email</label>
        <div class="input-wrap">
          <i class="fa-regular fa-envelope"></i>
          <input type="email" name="email" required placeholder="email@contoh.com">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <i class="fa-solid fa-lock"></i>
          <input type="password" name="password" required placeholder="Minimal 6 karakter">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Daftar Sebagai</label>
        <div class="input-wrap">
          <i class="fa-solid fa-briefcase"></i>
          <select name="role" required>
            <option value="client">Client (Pencari Jasa)</option>
            <option value="helper">Helper (Penyedia Jasa)</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn-submit">Buat Akun Sekarang</button>
    </form>
    <div class="auth-footer">
      Sudah punya akun? <a href="login.php">Masuk di sini</a>
    </div>
  </div>
</body>
</html>