-- db_beres.sql
CREATE DATABASE IF NOT EXISTS `db_beres` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_beres`;

-- Tabel Pengguna (Users)
CREATE TABLE `users` (
  `id_user` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('client', 'helper', 'admin') NOT NULL DEFAULT 'client',
  `foto_profil` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email_role` (`email`, `role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Kategori Jasa
CREATE TABLE `categories` (
  `id_category` TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(50) UNIQUE NOT NULL,
  INDEX `idx_kategori` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Pekerjaan/Tugas (Gigs)
CREATE TABLE `gigs` (
  `id_gig` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_client` INT UNSIGNED NOT NULL,
  `id_helper` INT UNSIGNED DEFAULT NULL,
  `id_category` TINYINT UNSIGNED NOT NULL,
  `judul` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `budget` DECIMAL(10,2) NOT NULL,
  `lokasi` VARCHAR(200) NOT NULL,
  `status` ENUM('open', 'taken', 'ongoing', 'done', 'cancelled') NOT NULL DEFAULT 'open',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_client`) REFERENCES `users`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_helper`) REFERENCES `users`(`id_user`) ON DELETE SET NULL,
  FOREIGN KEY (`id_category`) REFERENCES `categories`(`id_category`) ON DELETE RESTRICT,
  INDEX `idx_status_category` (`status`, `id_category`),
  INDEX `idx_updated` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Ulasan & Rating
CREATE TABLE `reviews` (
  `id_review` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_gig` INT UNSIGNED NOT NULL UNIQUE,
  `id_helper` INT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `komentar` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_gig`) REFERENCES `gigs`(`id_gig`) ON DELETE CASCADE,
  FOREIGN KEY (`id_helper`) REFERENCES `users`(`id_user`) ON DELETE CASCADE,
  INDEX `idx_helper_rating` (`id_helper`, `rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data Awal: Admin Default
INSERT INTO `users` (`nama`, `email`, `password`, `role`) VALUES 
('Admin BERES', 'admin@beres.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Data Awal: Kategori Jasa
INSERT INTO `categories` (`nama_kategori`) VALUES 
('Pertukangan'), ('Kebersihan'), ('Elektrikal'), ('Pindahan & Angkut'), ('Asisten Rumah Tangga');