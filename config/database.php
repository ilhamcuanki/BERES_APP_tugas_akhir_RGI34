<?php
// config/database.php
require_once __DIR__ . '/constants.php';

$host = '127.0.0.1';
$db   = 'db_beres';
$user = 'root';
$pass = ''; // Ganti jika menggunakan password MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log error ke file di production, tampilkan pesan aman di dev
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Koneksi database gagal. Periksa konfigurasi dan pastikan MySQL berjalan.");
}