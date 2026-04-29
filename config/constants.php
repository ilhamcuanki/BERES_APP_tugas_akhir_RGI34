<?php
// config/constants.php

// Path Root Aplikasi (otomatis menyesuaikan struktur folder)
define('ROOT_PATH', dirname(__DIR__) . '/');

define('BASE_URL', '/beres_app/'); 

// Path Aset & Upload
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOAD_PATH', ASSETS_PATH . 'img/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB

// Zona Waktu & Lokal
date_default_timezone_set('Asia/Jakarta');
define('APP_LOCALE', 'id_ID');

// Mode Error (Matikan di Production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);