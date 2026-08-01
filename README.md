<div align="center">
  <img src="https://img.icons8.com/?size=150&id=bK2b1KxVbICl&format=png&color=10b981" alt="AKSI KEBAL Logo" width="100"/>
  <h1>AKSI KEBAL</h1>
  <p><strong>A</strong>bsensi <strong>K</strong>egiatan <strong>S</strong>erentak <strong>K</strong>ementerian <strong>B</strong>eramal dan <strong>A</strong>ndal</p>
  <p><i>Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama</i></p>

  <p>
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/Vanilla_JS-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="Vanilla JS" />
    <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5" />
    <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3" />
  </p>
</div>

---

## 📖 Tentang Aplikasi

**AKSI KEBAL** merupakan sistem informasi absensi modern berbasis web yang dibangun dari awal *(from scratch)* menggunakan arsitektur **MVC (Model-View-Controller)** tanpa menggunakan framework pihak ketiga. 

Sistem ini diciptakan khusus untuk mencatat kedisiplinan dan kehadiran pegawai dalam mengikuti kegiatan, terinspirasi dari semboyan khas Kemenag **"Ikhlas Beramal"**. Aplikasi ini menawarkan antarmuka admin yang elegan dan proses absensi pegawai yang efisien berbasis kode QR.

## ✨ Fitur Unggulan

🚀 **Antarmuka Modern & Responsif**  
Desain UI yang estetis, modern, minimalis, dan sepenuhnya responsif untuk berbagai ukuran layar (*mobile-friendly*), menggunakan elemen antarmuka yang dinamis (*glassmorphism*, gradien halus, *micro-animations*).

📸 **Absensi Cepat via QR Code**  
Pegawai tidak perlu login! Cukup pindai (*scan*) QR Code yang dipublikasikan oleh Admin untuk langsung masuk ke formulir absensi kegiatan yang sedang berlangsung.

🖼️ **Unggah & Kompresi Foto Otomatis**  
Mendukung unggahan foto bukti kehadiran secara *real-time*. Sistem secara otomatis melakukan *resize* dan kompresi foto tanpa mengurangi kualitas orientasi EXIF untuk menjaga penyimpanan server tetap ringan (file < 1 MB).

📊 **Pelaporan Dinamis (PDF & CSV)**  
Sistem rekapitulasi data yang andal dan mudah difilter. Admin dapat membuat laporan kehadiran dalam format CSV untuk olah data *(spreadsheet)* maupun PDF siap cetak hanya dengan satu klik.

👥 **Manajemen Data Terpusat**  
Dilengkapi dengan modul CRUD penuh untuk pengelolaan data master seperti:
- **Pegawai** (Manajemen NIP, jabatan, dan kredensial akses)
- **Tim Kerja & Jabatan** (Pengelompokan struktural yang terorganisir)
- **Kegiatan** (Penjadwalan acara, *drafting*, hingga penerbitan QR)

🔒 **Keamanan Optimal**  
Dibekali dengan proteksi bawaan untuk melindungi dari serangan siber seperti SQL Injection *(Prepared Statements)*, proteksi CSRF *(Cross-Site Request Forgery)* terintegrasi, sanitasi input XSS, dan enkripsi password yang aman.

## 🏗️ Struktur Arsitektur

Meskipun murni menggunakan **PHP Native**, proyek ini diatur dengan standar arsitektur profesional:

- `/app` - Jantung utama aplikasi tempat *Controllers*, *Models*, dan *Views* berada.
- `/config` - Konfigurasi basis data dan environment.
- `/core` - Mesin *Routing*, modul *Database*, *Middleware*, dan fungsi *Helper*.
- `/public` - Pintu masuk aplikasi (*Front Controller*), penyimpanan *assets* statis (CSS/JS/Img), dan hasil unggahan.

## ⚙️ Persyaratan Sistem

- **PHP**: Versi 7.4 atau lebih baru (Rekomendasi PHP 8.x)
- **Ekstensi PHP**: `pdo_mysql`, `gd` (untuk kompresi foto)
- **Database**: MySQL / MariaDB
- **Web Server**: Apache dengan modul `mod_rewrite` aktif

## 🚀 Cara Instalasi

1. **Kloning Repositori**  
   Unduh atau lakukan kloning repositori ini ke folder root web server Anda (misal: `htdocs` atau `www`).
   ```bash
   git clone https://github.com/martinserafim93/aksi_kebal_kemenag.git
   ```

2. **Konfigurasi Database**  
   - Buat database baru di MySQL (contoh: `aksi_kebal`).
   - Impor struktur database dari file migrasi `database/aksi_kebal.sql` (jika ada).
   - Buka `config/database.php` dan sesuaikan kredensial koneksi (host, user, password, nama database).

3. **Pengaturan URL**  
   - Buka file `core/helpers.php` (atau file *config* terkait) dan sesuaikan konstanta `BASE_URL` dengan path lokal Anda.
   
4. **Jalankan Aplikasi**  
   Akses aplikasi melalui browser:
   - **Formulir Pegawai**: Akan bisa diakses langsung via link dari hasil pemindaian QR.
   - **Panel Admin**: Akses rute `/admin/login` untuk masuk ke pengelola utama.

---
<div align="center">
  <i>Dibuat dengan ❤️ untuk kedisiplinan dan integrasi birokrasi yang lebih baik.</i>
</div>
