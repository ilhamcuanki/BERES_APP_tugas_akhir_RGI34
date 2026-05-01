<?php
// process/update_profile_process.php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once ROOT_PATH . 'utils/csrf.php';
require_once ROOT_PATH . 'utils/flash.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Guard: Hanya Helper yang boleh update profil publik
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_SESSION['role'] ?? '') !== 'helper') {
    set_flash('error', 'Akses tidak valid.');
    header('Location: ' . BASE_URL . 'helper/dashboard.php'); exit;
}
if (!verify_csrf_token()) {
    set_flash('error', 'Token keamanan tidak valid.');
    header('Location: ' . BASE_URL . 'helper/profil.php'); exit;
}

$bio      = trim($_POST['bio'] ?? '');
$keahlian = trim($_POST['keahlian'] ?? '');
$user_id  = $_SESSION['user_id'];

// Handle Upload Foto Profil
$foto_profil = $_SESSION['foto_profil'] ?? 'default-avatar.png'; // Default

if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
    $file_tmp  = $_FILES['foto_profil']['tmp_name'];
    $file_name = $_FILES['foto_profil']['name'];
    $file_size = $_FILES['foto_profil']['size'];
    
    // Validasi 1: Ukuran (Maks 2MB)
    if ($file_size > MAX_UPLOAD_SIZE) {
        set_flash('error', 'Ukuran foto terlalu besar (Maks 2MB).');
        header('Location: ' . BASE_URL . 'helper/profil.php'); exit;
    }
    
    // Validasi 2: Tipe File (Hanya Gambar)
    $mime = exif_imagetype($file_tmp);
    $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];
    if (!in_array($mime, $allowed)) {
        set_flash('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
        header('Location: ' . BASE_URL . 'helper/profil.php'); exit;
    }
    
    // Validasi 3: Rename Unik & Pindah File
    $ext = image_type_to_extension($mime, false);
    $new_name = 'profile_' . $user_id . '_' . uniqid() . '.' . $ext;
    $dest = ROOT_PATH . 'assets/img/uploads/' . $new_name;
    
    if (move_uploaded_file($file_tmp, $dest)) {
        // Hapus foto lama jika bukan default
        $old_photo = $_SESSION['foto_profil'] ?? null;
        if ($old_photo && $old_photo !== 'default-avatar.png' && file_exists(ROOT_PATH . 'assets/img/uploads/' . $old_photo)) {
            unlink(ROOT_PATH . 'assets/img/uploads/' . $old_photo);
        }
        $foto_profil = $new_name;
    } else {
        set_flash('error', 'Gagal mengupload foto. Periksa izin folder.');
        header('Location: ' . BASE_URL . 'helper/profil.php'); exit;
    }
}

// Update Database
try {
    $stmt = $pdo->prepare("UPDATE users SET bio = ?, keahlian = ?, foto_profil = ? WHERE id_user = ?");
    $stmt->execute([$bio, $keahlian, $foto_profil, $user_id]);
    
    // Update Session agar navbar/avatar langsung refresh
    $_SESSION['foto_profil'] = $foto_profil;
    
    set_flash('success', 'Profil berhasil diperbarui.');
} catch (PDOException $e) {
    error_log("Update Profile Error: " . $e->getMessage());
    set_flash('error', 'Gagal menyimpan perubahan.');
}

header('Location: ' . BASE_URL . 'helper/profil.php');
exit;