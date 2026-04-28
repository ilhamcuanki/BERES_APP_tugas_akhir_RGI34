```markdown
# 🛠️ BERES - Basis Ekonomi Rakyat Efisiensi Sistem
> Platform Mikro Gig Economy untuk Koneksi Jasa Lokal Terpercaya  
> *Stack: PHP 8.x (Procedural), MySQL, HTML5, CSS3, Vanilla JS, Git*

## 📌 Konteks & Tujuan
BERES menjembatani **Client** (warga yang butuh bantuan rumah tangga) dengan **Helper** (penyedia jasa lokal) secara cepat, transparan, dan aman. Proyek ini dirancang untuk workshop dengan fokus pada:
- ✅ Logika bisnis yang ketat (State Machine, Atomic Update, Handshake Validation)
- ✅ Keamanan dasar web (CSRF, Session Regeneration, Prepared Statements, Safe Uploads)
- ✅ Arsitektur prosedural terstruktur (PRG Pattern, Separation of Concerns)
- ✅ Skalabilitas MVP ke arah fitur nyata (rating dinamis, auto-complete fallback, audit trail)

## 💻 Tech Stack
| Komponen | Teknologi |
|----------|-----------|
| Backend  | PHP 8.x (Procedural), PDO, Session Management |
| Database | MySQL 8.x / MariaDB (InnoDB, utf8mb4) |
| Frontend | HTML5, CSS3 (Flex/Grid, Variables), Vanilla JS |
| Version Control | Git + GitHub/GitLab |
| Server   | PHP Built-in Server / Apache / Nginx |

---

## 🧱 Fitur Utama
### 👤 Client (Pencari Jasa)
- Posting Gig (Beres-Request) dengan validasi input & PRG pattern
- Dashboard status tugas (`open`, `taken`, `ongoing`, `done`, `cancelled`)
- Melihat profil Helper, portfolio, dan trust score
- Konfirmasi penyelesaian & input rating/review

### 🛠️ Helper (Penyedia Jasa)
- Gig Feed: Daftar tugas `open` dengan filter kategori & pagination
- Professional Profile: Upload foto profil & portfolio (aman)
- Job Management: Update status berjenjang (`Menuju` → `Kerja` → `Selesai`)
- Reputation System: Trust score dinamis berdasarkan review valid

### 🛡️ Keamanan & Efisiensi Sistem
- Multi-Role Authentication dengan `session_regenerate_id()` & CSRF
- Beres-Handshake: Konfirmasi 2 arah + fallback auto-complete 24 jam
- Simulasi Wallet: Pencatatan transaksi untuk audit alur ekonomi

### 👨‍💼 Admin (Back-Office)
- User Management (blokir/suspend)
- Category & Content Management
- Transaction Logs & Monitoring Platform

---

## ⚙️ Keputusan Teknis (Final)
| Area | Keputusan | Alasan |
|------|-----------|--------|
| **Form Handling** | Post/Redirect/Get (PRG) | Mencegah double-submit, memisahkan logika & view |
| **Database Access** | PDO + Prepared Statements | Konsisten, aman dari SQLi, error handling terstruktur |
| **Claim Gig** | `UPDATE ... WHERE status='open'` + `rowCount()` | Mencegah race condition tanpa lock table |
| **Status Workflow** | PHP State Machine Validation | Mencegah loncat status ilegal |
| **Trust Score** | Dihitung Dinamis (`AVG() JOIN`) | Akurasi > optimasi statis, hindari data desinkron |
| **Feedback UI** | Flash Session (`$_SESSION['flash']`) | Terpusat, aman dari XSS, UX bersih |
| **Real-time UI** | Full Page Reload (default) | Stabil untuk workshop. AJAX hanya enhancement Fase 10 |
| **Upload** | `exif_imagetype()` + `uniqid()` + `.htaccess` block PHP | Mencegah RCE, menjaga integritas server |

---

## 📁 Struktur Folder
```
beres_app/
├── config/
│   ├── database.php          # PDO koneksi + error mode
│   └── constants.php         # Path, timezone, upload limit
├── helpers/
│   ├── csrf.php              # Generate & verify token
│   ├── sanitize.php          # Filter input & escape output
│   └── flash_messages.php    # Set & render flash session
├── process/                  # LOGIKA POST SAJA (PRG)
│   ├── login_process.php
│   ├── register_process.php
│   ├── post_gig_process.php
│   ├── claim_gig_process.php
│   ├── update_status_process.php
│   ├── confirm_gig_process.php
│   └── review_process.php
├── includes/
│   ├── header.php            # Navigasi dinamis per role
│   ├── footer.php            # Render flash, script dasar
│   └── auth_check.php        # Session check + role guard
├── client/                   # Dashboard, post_gig, detail_gig
├── helper/                   # Feed, update_status, profil
├── admin/                    # Manage users, categories, logs
├── assets/
│   ├── css/style.css         # CSS Variables, Mobile-First
│   ├── js/main.js            # Validasi form, fetch (opsional)
│   └── img/                  # Upload user (dilindungi .htaccess)
├── .gitignore                # .env, vendor, *.sql, cache
└── index.php                 # Landing page publik
```

---

## 🗄️ Database Schema (`db_beres`)
**Engine:** InnoDB | **Charset:** `utf8mb4_unicode_ci`

| Tabel | Kolom Utama | Constraint & Catatan |
|-------|-------------|----------------------|
| `users` | `id_user` (PK, AI), `nama`, `email` (UNIQUE), `password`, `role` (ENUM: client, helper, admin), `foto_profil`, `created_at` | `trust_score` **tidak disimpan**. Dihitung dinamis. |
| `categories` | `id_category` (PK, AI), `nama_kategori` | Index untuk filter cepat. |
| `gigs` | `id_gig` (PK, AI), `id_client` (FK), `id_helper` (FK, NULL), `id_category` (FK), `judul`, `deskripsi`, `budget`, `lokasi`, `status` (ENUM: open, taken, ongoing, done, cancelled), `created_at`, `updated_at` (ON UPDATE) | `updated_at` wajib untuk auto-complete & sorting. |
| `reviews` | `id_review` (PK, AI), `id_gig` (FK, UNIQUE), `id_helper` (FK), `rating` (1-5), `komentar`, `created_at` | `UNIQUE(id_gig)` cegah double review. `id_helper` disimpan untuk query profil efisien. |

**Query Trust Score Dinamis:**
```sql
SELECT COALESCE(AVG(r.rating), 0) AS trust_score 
FROM reviews r 
JOIN gigs g ON r.id_gig = g.id_gig 
WHERE g.id_helper = ? AND g.status = 'done';
```

---

## 🔄 Core Workflows (Logika Bisnis)
1. **Autentikasi:** Register → `password_hash()` → Login → `password_verify()` → `session_regenerate_id(true)` → Simpan `user_id`, `role` → Redirect sesuai role.
2. **Posting Gig:** Validasi input → PDO `INSERT` → Redirect ke dashboard client.
3. **Claim Gig (Atomic):** `UPDATE gigs SET id_helper=?, status='taken' WHERE id_gig=? AND status='open'` → Cek `affected_rows`. Jika `1` = sukses, `0` = gig sudah diambil.
4. **Status Workflow:** Validasi transisi via PHP array. Contoh: `taken` → `ongoing` → `done`. Tidak boleh skip.
5. **Handshake & Review:** Helper klik selesai → status `pending_confirm`. Client konfirmasi → `done`. Review hanya bisa insert jika `status='done'`. Fallback: jika >24 jam tidak dikonfirmasi, sistem auto-set `done` saat page load berikutnya.
6. **Upload:** Cek MIME & size → rename `uniqid('img_')` → simpan path di DB → blok eksekusi PHP di folder `assets/img/`.

---

## 🛡️ Security Checklist (Wajib)
- [ ] `password_hash()` / `password_verify()`
- [ ] `htmlspecialchars()` & `filter_var()` di semua output/input
- [ ] PDO Prepared Statements di **setiap** query yang menerima input
- [ ] CSRF token di setiap `<form method="POST">`
- [ ] `auth_check.php` memvalidasi `$_SESSION['role']` sebelum render
- [ ] Batasi upload `upload_max_filesize` & `post_max_size`
- [ ] `.htaccess` di `assets/img/`: `RemoveHandler .php .phtml .php3`

---

## 🗺️ Roadmap & Progress Tracker (Workshop)
| Fase | Fokus | Status | Catatan / Kendala |
|------|-------|--------|-------------------|
| 1 | Inisialisasi & Setup | ⬜ Belum | DB, PDO, Git, constants |
| 2 | Autentikasi Sistem | ⬜ Belum | Register, Login, Session, CSRF |
| 3 | Layout & UI Dasar | ⬜ Belum | Header/Footer dinamis, CSS vars |
| 4 | Core Client (Post) | ⬜ Belum | Form, PRG, Validasi, Dashboard |
| 5 | Core Helper (Feed) | ⬜ Belum | Query `open`, Pagination |
| 6 | Logika Transaksi | ⬜ Belum | Atomic Claim, rowCount check |
| 7 | Manajemen Status | ⬜ Belum | State Machine, Detail View |
| 8 | Handshake & Review | ⬜ Belum | Confirm Logic, 24h fallback |
| 9 | Profil & Portfolio | ⬜ Belum | Upload safe, Trust dinamis |
| 10 | Finishing & Deploy | ⬜ Belum | Testing, Polish, README v1.0 |

> 💡 *Update status hanya di akhir setiap fase. Untuk detail perubahan kode, lihat `git log`.*

---

## 🚀 Setup & Run (Local)
```bash
# 1. Clone & masuk folder
git clone <repo-url> && cd beres_app

# 2. Buat database di phpMyAdmin/CLI
CREATE DATABASE db_beres CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 3. Konfigurasi koneksi
# Edit config/database.php → sesuaikan host, user, password, dbname

# 4. Jalankan server lokal
php -S localhost:8000 -t .

# 5. Akses aplikasi
# Buka http://localhost:8000 di browser
```

---

## 📝 Catatan Kelanjutan (Untuk AI/Mentor Baru)
- Proyek berada di **Fase 1: Inisialisasi & Setup Lingkungan**.
- Stack: PHP Prosedural, PDO, MySQL, HTML/CSS/JS vanilla, Git.
- Pola utama: PRG, Atomic UPDATE, State Machine, Flash Session, Dynamic Trust Score.
- Jika melanjutkan, mulai dari:
  1. Setup `config/database.php` (PDO)
  2. Buat `helpers/csrf.php` & `includes/auth_check.php`
  3. Implementasi `register_process.php` & `login_process.php`
- Gunakan checklist keamanan di atas sebagai patokan review kode.
- Dokumentasi lengkap fitur, struktur, DB, dan roadmap sudah tertanam di file ini.

---
*© 2026 BERES Project. Dibangun untuk Workshop Pembelajaran PHP/MySQL.*
```