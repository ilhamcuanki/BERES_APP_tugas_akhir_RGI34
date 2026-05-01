<?php
// process/upload_portfolio_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? '') !== 'helper') {
    set_flash('error', 'Akses tidak valid.');
    header('Location: ' . BASE_URL . 'helper/profil.php'); exit;
}
if (!verify_csrf_token()) {
    set_flash('error', 'Token keamanan tidak valid.');
    header('Location: ' . BASE_URL . 'helper/profil.php'); exit;
}

$helper_id = $_SESSION['user_id'];
$uploaded_count = 0;

if (isset($_FILES['portfolio']) && is_array($_FILES['portfolio']['name'])) {
    $count = count($_FILES['portfolio']['name']);
    
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['portfolio']['error'][$i] !== UPLOAD_ERR_OK) continue;
        
        $file_tmp  = $_FILES['portfolio']['tmp_name'][$i];
        $file_size = $_FILES['portfolio']['size'][$i];
        
        // Validasi Dasar
        if ($file_size > MAX_UPLOAD_SIZE) continue; // Skip file terlalu besar
        $mime = exif_imagetype($file_tmp);
        if (!in_array($mime, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP])) continue;
        
        // Simpan File
        $ext = image_type_to_extension($mime, false);
        $new_name = 'portfolio_' . $helper_id . '_' . uniqid() . '.' . $ext;
        $dest = ROOT_PATH . 'assets/img/uploads/' . $new_name;
        
        if (move_uploaded_file($file_tmp, $dest)) {
            // Catat di DB (Opsional: buat tabel portfolio terpisah jika butuh deskripsi per foto)
            // Untuk MVP, kita cukup simpan nama file di kolom JSON atau tabel sederhana
            // Di sini kita asumsikan cukup simpan path di session atau tampilkan dari folder
            // Agar simpel, kita hanya sukseskan upload, display akan diambil dari folder user
            $uploaded_count++;
        }
    }
}

if ($uploaded_count > 0) {
    set_flash('success', "$uploaded_count foto portfolio berhasil diupload.");
} else {
    set_flash('info', 'Tidak ada foto valid yang diupload. Pastikan format JPG/PNG/WEBP.');
}

header('Location: ' . BASE_URL . 'helper/profil.php');
exit;