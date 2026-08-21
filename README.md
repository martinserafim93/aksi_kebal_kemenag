<div align="center">
  <img src="public/assets/img/kemenag-new-2025.png" alt="Logo Kemenag" width="150" />
  <h1>✨ AKSI KEBAL ✨</h1>
  <p><strong>A</strong>bsensi <strong>K</strong>egiatan <strong>S</strong>erentak <strong>K</strong>ementerian <strong>B</strong>eramal dan <strong>A</strong>ndal</p>
  <p><i>Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama yang Modern, Cepat, dan Andal</i></p>

  <p>
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="Vanilla JS" />
    <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
    <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  </p>
</div>

---

## 📖 Tentang Aplikasi

**AKSI KEBAL** adalah sistem informasi absensi modern berbasis web yang dirancang khusus untuk memfasilitasi pencatatan kehadiran pegawai dalam berbagai kegiatan di lingkungan **Kementerian Agama**. Terinspirasi dari semangat **"Ikhlas Beramal"**, aplikasi ini dibangun secara mandiri *(from scratch)* menggunakan arsitektur **MVC (Model-View-Controller)** murni dengan PHP Native, tanpa bergantung pada framework pihak ketiga. Hal ini menjamin performa yang cepat, ringan, dan sangat mudah dikustomisasi.

Sistem ini menghadirkan antarmuka admin yang elegan serta alur absensi pegawai berbasis **QR Code** yang praktis, membawa revolusi digitalisasi birokrasi ke tingkat yang lebih baik.

---

## ✨ Fitur Unggulan

### 🚀 **Antarmuka Modern & Responsif (UI/UX Premium)**
Desain memukau dengan implementasi gaya *glassmorphism*, gradasi warna yang dinamis, serta *micro-animations* yang interaktif. Antarmuka sepenuhnya responsif, memastikan kenyamanan akses yang setara dari PC, tablet, maupun *smartphone*.

### 📸 **Absensi Cepat via QR Code**
Ucapkan selamat tinggal pada proses antre! Pegawai tidak perlu *login*. Cukup *scan* QR Code unik yang ditampilkan oleh Admin pada lokasi kegiatan, dan pegawai akan langsung diarahkan ke laman pengisian absensi secara instan.

### 🖼️ **Unggah & Kompresi Foto Pintar (Auto-Resize)**
Sistem dapat menerima unggahan bukti foto kehadiran dan secara *real-time* melakukan kompresi ukuran gambar (sehingga < 1 MB) tanpa mengacaukan orientasi (EXIF data) dan mempertahankan kualitas visual. Hal ini sangat menghemat kapasitas penyimpanan server!

### 📊 **Pelaporan Dinamis (Ekspor PDF & CSV)**
Data kehadiran direkapitulasi secara terstruktur dan dapat difilter dengan spesifik. Admin dapat mencetak laporan akhir ke dalam format **PDF** yang rapi atau mengekspor ke format **CSV** untuk diolah lebih lanjut melalui aplikasi *spreadsheet* hanya dalam satu klik.

### 👥 **Manajemen Data Induk Terpusat (Master Data)**
Modul **CRUD (Create, Read, Update, Delete)** yang komprehensif memudahkan pengelolaan:
- 🧑‍💼 **Data Pegawai**: Manajemen NIP, jabatan, dan hak akses absensi.
- 🏢 **Tim Kerja & Jabatan**: Pengelompokan struktur organisasi unit kerja yang rapi.
- 📅 **Manajemen Kegiatan**: Mulai dari penjadwalan, pengaturan status (*Draft/Publish*), hingga pembuatan dan regenerasi QR Code kegiatan.

### 🔒 **Sistem Keamanan Berlapis**
Aplikasi ini dibekali dengan pengamanan ekstra untuk melindungi data vital:
- **Anti SQL Injection**: Penggunaan *Prepared Statements* melalui PDO.
- **Proteksi CSRF**: Tokenisasi *Cross-Site Request Forgery* yang ketat pada setiap form.
- **Sanitasi XSS**: Pembersihan input pengguna untuk menghindari injeksi *script* berbahaya.
- **Enkripsi Hash**: Kata sandi dienkripsi secara aman menggunakan *algoritma modern (Bcrypt)*.

---

## 🏗️ Struktur Arsitektur MVC

Walaupun dibangun menggunakan PHP Native, susunan direktori tetap mengadopsi standar pengembangan *software* modern:

- 📂 `/app` — Jantung aplikasi yang berisi **Controllers**, **Models**, dan **Views**.
- 📂 `/config` — File pengaturan konfigurasi basis data dan profil aplikasi.
- 📂 `/core` — Mesin dasar *(Core Engine)* seperti *Router*, *Database Handler*, dan fungsi utilitas pendukung (*Helper*).
- 📂 `/public` — Titik masuk utama aplikasi (*Front Controller*), letak aset statis (CSS/JS/Gambar), dan folder unggahan dokumen.

---

## ⚙️ Persyaratan Sistem

- **PHP**: Versi `7.4` atau lebih baru (Sangat direkomendasikan **PHP 8.x**).
- **PHP Extensions**: `pdo_mysql` (untuk database) dan `gd` (wajib untuk fitur kompresi & rotasi foto).
- **Database**: `MySQL` atau `MariaDB`.
- **Web Server**: `Apache` dengan modul `mod_rewrite` diaktifkan (untuk implementasi *Clean URL*).

---

## 🚀 Panduan Instalasi (Quick Start)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi pada *local environment* Anda:

1. **Kloning Repositori**  
   Lakukan kloning repositori ke dalam folder *document root* dari web server Anda (misal: `htdocs` pada XAMPP).
   ```bash
   git clone https://github.com/martinserafim93/aksi_kebal_kemenag.git
   ```

2. **Konfigurasi Database**  
   - Buat sebuah database baru melalui *phpMyAdmin* atau *MySQL Console* (contoh: `aksi_kebal`).
   - Impor struktur tabel aplikasi dari file `database/aksi_kebal.sql` (bila disediakan).
   - Buka file `config/database.php` dan sesuaikan informasi kredensial (*host, username, password, dan nama database*).

3. **Penyesuaian Base URL**  
   - Buka file `core/helpers.php` atau file utama pengaturan URL.
   - Ubah parameter konstanta `BASE_URL` agar sesuai dengan *path* aplikasi Anda (contoh: `http://localhost/aksi_kebal_kemenag/public/`).

4. **Jalankan Aplikasi!**  
   Aplikasi Anda siap digunakan. Buka peramban (*web browser*) dan akses URL berikut:
   - 🛂 **Panel Admin Utama**: Arahkan ke tautan `/admin/login` untuk mengakses *dashboard* pengelola.
   - 🤳 **Formulir Presensi Pegawai**: Tautan bersifat rahasia dan dinamis yang hanya bisa diakses via pemindaian (*scan*) QR Code di lokasi kegiatan.

---

<div align="center">
  <br>
  <i>Dibuat dengan ❤️ untuk menunjang kedisiplinan dan integrasi teknologi di lingkungan kerja.</i>
  <br>
  <strong>Hak Cipta © 2026 - AKSI KEBAL</strong>
</div>
