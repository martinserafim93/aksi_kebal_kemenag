# 📋 AKSI KEBAL — Project Issues (Planning)

> **AKSI KEBAL** (Absensi Kegiatan Serentak Kementerian Beramal dan Andal)
> Sistem Informasi Absensi Kegiatan Pegawai

**Tech Stack:** PHP (MVC tanpa framework) · MySQL · HTML/CSS/JS (Vanilla)

---

## Issue #1 — Setup Struktur Projek & Konfigurasi MVC

**Label:** `setup`, `priority: high`
**Milestone:** Foundation

### Deskripsi

Menyiapkan struktur folder projek berbasis MVC (Model-View-Controller) tanpa framework, termasuk konfigurasi dasar seperti koneksi database, autoloading, routing, dan file `.htaccess`.

### Acceptance Criteria

- [ ] Struktur folder MVC terbentuk:
  ```
  /
  ├── app/
  │   ├── controllers/
  │   ├── models/
  │   └── views/
  │       ├── admin/
  │       └── pegawai/
  ├── public/
  │   ├── assets/
  │   │   ├── css/
  │   │   ├── js/
  │   │   └── img/
  │   ├── uploads/
  │   │   └── foto_absensi/
  │   └── index.php (entry point)
  ├── config/
  │   └── database.php
  ├── core/
  │   ├── App.php (router)
  │   ├── Controller.php (base controller)
  │   ├── Database.php (PDO wrapper)
  │   └── Middleware.php
  └── .htaccess
  ```
- [ ] Routing sederhana berjalan (URL → Controller → View)
- [ ] Koneksi database MySQL berhasil (PDO)
- [ ] File `.htaccess` untuk URL rewriting berfungsi
- [ ] Base Controller dengan method `view()` dan `model()` tersedia
- [ ] Helper function untuk redirect, session, dll tersedia

---

## Issue #2 — Desain & Migrasi Database

**Label:** `database`, `priority: high`
**Milestone:** Foundation

### Deskripsi

Membuat skema database MySQL sesuai rancangan, termasuk tabel `pegawai`, `tim_kerja`, `jabatan`, `kegiatan`, dan `absensi` beserta relasi foreign key.

### Acceptance Criteria

- [ ] File SQL migrasi (`database/aksi_kebal.sql`) tersedia
- [ ] Tabel `tim_kerja`:
  - `id_tim_kerja` INT AUTO_INCREMENT (PK)
  - `nama_tim_kerja` VARCHAR(100) NOT NULL
- [ ] Tabel `jabatan`:
  - `id_jabatan` INT AUTO_INCREMENT (PK)
  - `nama_jabatan` VARCHAR(100) NOT NULL
- [ ] Tabel `pegawai`:
  - `nip` VARCHAR(20) (PK)
  - `nama_lengkap` VARCHAR(150) NOT NULL
  - `id_jabatan` INT (FK → jabatan)
  - `id_tim_kerja` INT (FK → tim_kerja)
  - `email` VARCHAR(100) NULLABLE
  - `password` VARCHAR(255) NULLABLE
  - `role` ENUM('admin', 'pegawai') DEFAULT 'pegawai'
- [ ] Tabel `kegiatan`:
  - `id_kegiatan` INT AUTO_INCREMENT (PK)
  - `nama_kegiatan` VARCHAR(200) NOT NULL
  - `jenis_kegiatan` ENUM('Kerja Bakti', 'Doa Bersama', 'Apel', 'Rapat', 'Sosialisasi')
  - `tanggal_kegiatan` DATE
  - `waktu_mulai` TIME
  - `waktu_selesai` TIME
  - `lokasi_kegiatan` VARCHAR(200)
  - `deskripsi_kegiatan` TEXT
  - `status_kegiatan` ENUM('Draft', 'Published') DEFAULT 'Draft'
  - `qr_code` TEXT NULLABLE
- [ ] Tabel `absensi`:
  - `id_absensi` INT AUTO_INCREMENT (PK)
  - `nip` VARCHAR(20) (FK → pegawai)
  - `id_kegiatan` INT (FK → kegiatan)
  - `status_kehadiran` ENUM('Hadir', 'Tidak Hadir') DEFAULT 'Hadir'
  - `foto` VARCHAR(255) NULLABLE
  - `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- [ ] Semua foreign key constraint terdefinisi
- [ ] Data seed awal (minimal 1 admin, beberapa jabatan & tim kerja)

---

## Issue #3 — Halaman Login Admin

**Label:** `feature`, `admin`, `auth`
**Milestone:** Autentikasi

### Deskripsi

Membuat halaman login untuk Admin menggunakan email/NIP dan password. Termasuk validasi, session management, dan proteksi halaman admin dengan middleware.

### Acceptance Criteria

- [ ] Halaman login (`/admin/login`) dengan desain menarik dan modern
- [ ] Form input: Email/NIP + Password
- [ ] Validasi server-side (cek kredensial di database)
- [ ] Password disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`
- [ ] Session management (login, logout, cek session aktif)
- [ ] Middleware proteksi: halaman admin hanya bisa diakses jika sudah login
- [ ] Redirect ke dashboard setelah login sukses
- [ ] Pesan error jika login gagal
- [ ] Tombol logout berfungsi dan menghancurkan session

---

## Issue #4 — Layout & Template Admin (Sidebar)

**Label:** `frontend`, `admin`, `ui/ux`
**Milestone:** UI Admin

### Deskripsi

Membuat layout utama halaman admin dengan sidebar navigasi bergaya minimalis (mirip AdminLTE). Layout ini akan menjadi template utama untuk semua halaman admin.

### Acceptance Criteria

- [ ] Layout admin dengan sidebar navigasi di sebelah kiri
- [ ] Sidebar berisi menu:
  - Dashboard
  - Manajemen Pegawai
  - Manajemen Tim Kerja
  - Manajemen Jabatan
  - Manajemen Kegiatan
  - Manajemen Absensi
  - Logout
- [ ] Header/topbar dengan info admin yang login
- [ ] Desain minimalis, modern, dan responsif
- [ ] Sidebar collapsible (toggle buka/tutup) di layar kecil
- [ ] Active state pada menu yang sedang dibuka
- [ ] Warna tema profesional (dark sidebar, light content area)
- [ ] Icon untuk setiap menu (gunakan icon set seperti Font Awesome atau Boxicons)
- [ ] Template reusable (`views/admin/layouts/main.php`)

---

## Issue #5 — Dashboard Admin

**Label:** `feature`, `admin`
**Milestone:** UI Admin

### Deskripsi

Membuat halaman dashboard admin yang menampilkan statistik ringkas mengenai data pegawai dan kegiatan.

### Acceptance Criteria

- [ ] Halaman dashboard (`/admin/dashboard`)
- [ ] Card statistik:
  - Total Pegawai
  - Total Kegiatan
  - Total Kegiatan Published
  - Total Absensi Hari Ini
- [ ] Tabel atau list kegiatan terbaru (5 kegiatan terakhir)
- [ ] Grafik/chart sederhana (opsional, bisa menggunakan Chart.js)
- [ ] Desain informatif dan clean
- [ ] Data diambil secara dinamis dari database

---

## Issue #6 — Manajemen Pegawai (CRUD)

**Label:** `feature`, `admin`, `crud`
**Milestone:** Manajemen Data

### Deskripsi

Membuat fitur CRUD (Create, Read, Update, Delete) untuk data pegawai pada halaman admin.

### Acceptance Criteria

- [ ] Halaman daftar pegawai (`/admin/pegawai`) dengan tabel data
- [ ] Kolom tabel: NIP, Nama Lengkap, Jabatan, Tim Kerja, Email, Aksi
- [ ] Tombol **Tambah Pegawai** → form input:
  - NIP (text)
  - Nama Lengkap (text)
  - Jabatan (select, dari tabel jabatan)
  - Tim Kerja (select, dari tabel tim_kerja)
  - Email (text, opsional)
  - Password (text, opsional — untuk yang dijadikan admin)
- [ ] Tombol **Edit** → form edit dengan data terisi
- [ ] Tombol **Hapus** → konfirmasi sebelum hapus
- [ ] Validasi input (NIP unik, nama wajib diisi)
- [ ] Fitur pencarian/filter pada tabel
- [ ] Pagination jika data banyak
- [ ] Notifikasi sukses/gagal setelah aksi

---

## Issue #7 — Manajemen Tim Kerja (CRUD)

**Label:** `feature`, `admin`, `crud`
**Milestone:** Manajemen Data

### Deskripsi

Membuat fitur CRUD untuk data Tim Kerja pada halaman admin.

### Acceptance Criteria

- [ ] Halaman daftar tim kerja (`/admin/tim-kerja`)
- [ ] Tabel: ID, Nama Tim Kerja, Jumlah Anggota, Aksi
- [ ] Tombol **Tambah Tim Kerja** → form input Nama Tim Kerja
- [ ] Tombol **Edit** → form edit dengan data terisi
- [ ] Tombol **Hapus** → konfirmasi sebelum hapus (cek relasi dengan pegawai)
- [ ] Validasi: nama tim kerja tidak boleh kosong dan tidak duplikat
- [ ] Notifikasi sukses/gagal setelah aksi

---

## Issue #8 — Manajemen Jabatan (CRUD)

**Label:** `feature`, `admin`, `crud`
**Milestone:** Manajemen Data

### Deskripsi

Membuat fitur CRUD untuk data Jabatan pada halaman admin.

### Acceptance Criteria

- [ ] Halaman daftar jabatan (`/admin/jabatan`)
- [ ] Tabel: ID, Nama Jabatan, Jumlah Pegawai, Aksi
- [ ] Tombol **Tambah Jabatan** → form input Nama Jabatan
- [ ] Tombol **Edit** → form edit dengan data terisi
- [ ] Tombol **Hapus** → konfirmasi sebelum hapus (cek relasi dengan pegawai)
- [ ] Validasi: nama jabatan tidak boleh kosong dan tidak duplikat
- [ ] Notifikasi sukses/gagal setelah aksi

---

## Issue #9 — Manajemen Kegiatan (CRUD + Publish & QR Code)

**Label:** `feature`, `admin`, `crud`, `qr-code`
**Milestone:** Manajemen Data

### Deskripsi

Membuat fitur CRUD untuk data Kegiatan, termasuk fitur **Publish** yang menghasilkan QR Code berisi link formulir absensi pegawai.

### Acceptance Criteria

- [ ] Halaman daftar kegiatan (`/admin/kegiatan`)
- [ ] Tabel: ID, Nama, Jenis, Tanggal, Waktu, Lokasi, Status, Aksi
- [ ] Tombol **Tambah Kegiatan** → form input:
  - Nama Kegiatan (text)
  - Jenis Kegiatan (select: Kerja Bakti, Doa Bersama, Apel, Rapat, Sosialisasi)
  - Tanggal Kegiatan (date)
  - Waktu Mulai (time)
  - Waktu Selesai (time)
  - Lokasi Kegiatan (text)
  - Deskripsi Kegiatan (textarea)
- [ ] Tombol **Edit** → form edit dengan data terisi
- [ ] Tombol **Hapus** → konfirmasi sebelum hapus (cek relasi dengan absensi)
- [ ] Tombol **Publish** → mengubah status dari `Draft` → `Published`
- [ ] Saat di-publish, sistem men-generate QR Code menggunakan library PHP QR Code
  - QR Code berisi URL: `https://domain.com/absensi?kegiatan={id_kegiatan}`
  - QR Code disimpan sebagai gambar atau data string
- [ ] Tombol **Lihat QR Code** → menampilkan QR Code yang bisa di-download/share
- [ ] Badge warna berbeda untuk status Draft (kuning) dan Published (hijau)
- [ ] Filter berdasarkan status dan jenis kegiatan
- [ ] Library QR Code: menggunakan `phpqrcode` atau library JS seperti `qrcode.js`

---

## Issue #10 — Manajemen Absensi (Verifikasi, Koreksi, Hapus)

**Label:** `feature`, `admin`
**Milestone:** Manajemen Data

### Deskripsi

Membuat halaman admin untuk melihat, memverifikasi, mengoreksi, dan menghapus data absensi pegawai.

### Acceptance Criteria

- [ ] Halaman daftar absensi (`/admin/absensi`)
- [ ] Filter berdasarkan:
  - Kegiatan tertentu (select)
  - Jenis kegiatan (select)
  - Tanggal kegiatan
  - Semua data (default)
- [ ] Tabel: No, NIP, Nama Pegawai, Kegiatan, Status Kehadiran, Foto, Waktu Submit, Aksi
- [ ] Kolom Foto menampilkan thumbnail yang bisa diklik untuk melihat foto penuh (modal/lightbox)
- [ ] Tombol **Edit/Koreksi** → ubah status kehadiran
- [ ] Tombol **Hapus** → konfirmasi sebelum hapus
- [ ] Ringkasan statistik di atas tabel (total hadir, total tidak hadir)
- [ ] Pagination jika data banyak

---

## Issue #11 — Laporan Absensi (Export PDF & CSV)

**Label:** `feature`, `admin`, `reporting`
**Milestone:** Pelaporan

### Deskripsi

Membuat fitur untuk mengexport/generate laporan absensi ke format PDF dan CSV.

### Acceptance Criteria

- [ ] Tombol **Export PDF** pada halaman manajemen absensi
- [ ] Tombol **Export CSV** pada halaman manajemen absensi
- [ ] Filter laporan sesuai filter yang aktif (per kegiatan, per jenis, atau semua)
- [ ] Format PDF:
  - Header: Judul laporan, nama kegiatan, tanggal
  - Tabel: No, NIP, Nama, Jabatan, Tim Kerja, Status Kehadiran, Waktu
  - Footer: Total hadir / tidak hadir
  - Library: `TCPDF`, `FPDF`, atau `Dompdf`
- [ ] Format CSV:
  - Header kolom di baris pertama
  - Data sesuai filter
  - Nama file: `Laporan_Absensi_{nama_kegiatan}_{tanggal}.csv`
- [ ] File ter-download otomatis ke browser

---

## Issue #12 — Halaman Absensi Pegawai (Formulir via QR Code)

**Label:** `feature`, `pegawai`, `priority: high`
**Milestone:** Fitur Pegawai

### Deskripsi

Membuat halaman formulir absensi yang diakses oleh pegawai melalui link QR Code. Halaman ini **tidak memerlukan login** — pegawai langsung mengisi formulir setelah scan QR Code.

### Acceptance Criteria

- [ ] URL: `/absensi?kegiatan={id_kegiatan}`
- [ ] Validasi: jika `id_kegiatan` tidak valid atau status bukan `Published`, tampilkan halaman error
- [ ] Header halaman menampilkan:
  - Nama Kegiatan
  - Jenis Kegiatan
  - Tanggal & Waktu Kegiatan
  - Lokasi Kegiatan
- [ ] Formulir berisi:
  - **Nama Lengkap** (select/dropdown) — berisi daftar semua pegawai
  - Saat nama dipilih, **NIP**, **Jabatan**, dan **Tim Kerja** terisi otomatis (AJAX/fetch)
  - Field NIP, Jabatan, Tim Kerja tampil sebagai readonly/disabled
  - **Upload Foto** (input file, accept: image/*)
  - Preview foto sebelum submit
  - Tombol **Submit Absensi**
- [ ] Validasi:
  - Nama wajib dipilih
  - Foto wajib diunggah
  - Cegah duplikasi absensi (1 pegawai = 1 absensi per kegiatan)
- [ ] Desain responsif (mobile-first, karena pegawai scan QR via HP)
- [ ] Tema warna **hijau cerah**, nyaman dilihat
- [ ] Tampilan ramah dan mudah digunakan

---

## Issue #13 — Kompresi Foto & Penyimpanan File

**Label:** `feature`, `backend`, `priority: high`
**Milestone:** Fitur Pegawai

### Deskripsi

Implementasi kompresi foto yang diunggah pegawai agar ukuran file di bawah 1 MB, serta menyimpan file ke folder server dan path-nya ke database.

### Acceptance Criteria

- [ ] Foto diunggah melalui form absensi pegawai
- [ ] Validasi file:
  - Tipe file: JPG, JPEG, PNG
  - Ukuran maksimal upload: 10 MB (sebelum kompresi)
- [ ] Proses kompresi menggunakan GD Library PHP:
  - Resize jika resolusi terlalu besar (max 1920px width)
  - Compress quality hingga ukuran < 1 MB
  - Pertahankan orientasi (EXIF)
- [ ] File disimpan ke folder `public/uploads/foto_absensi/`
- [ ] Nama file di-rename agar unik: `{nip}_{id_kegiatan}_{timestamp}.jpg`
- [ ] Path file disimpan ke kolom `foto` di tabel `absensi`
- [ ] Jika kompresi gagal, tampilkan pesan error yang jelas

---

## Issue #14 — Halaman Sukses Absensi & Auto-Redirect

**Label:** `feature`, `pegawai`
**Milestone:** Fitur Pegawai

### Deskripsi

Setelah pegawai berhasil submit absensi, sistem menampilkan halaman sukses dengan ringkasan data absensi, lalu mengarahkan kembali ke halaman formulir setelah 5 detik.

### Acceptance Criteria

- [ ] Halaman sukses ditampilkan setelah submit berhasil
- [ ] Informasi yang ditampilkan:
  - ✅ Icon/animasi sukses
  - Nama Pegawai
  - NIP
  - Jabatan
  - Tim Kerja
  - Nama Kegiatan
  - Tanggal & Waktu Kegiatan
  - Status: **Hadir**
- [ ] Countdown timer visual: "Halaman akan dialihkan dalam **5** detik..."
- [ ] Auto-redirect ke halaman formulir absensi (kegiatan yang sama) setelah 5 detik
- [ ] Tombol manual "Kembali ke Formulir" jika pegawai tidak ingin menunggu
- [ ] Desain dengan tema hijau, menyenangkan, dan informatif

---

## Issue #15 — API Endpoint: Data Pegawai (AJAX)

**Label:** `feature`, `backend`, `api`
**Milestone:** Fitur Pegawai

### Deskripsi

Membuat API endpoint untuk mengambil data pegawai berdasarkan NIP, digunakan oleh formulir absensi untuk mengisi otomatis field NIP, Jabatan, dan Tim Kerja saat nama pegawai dipilih.

### Acceptance Criteria

- [ ] Endpoint: `GET /api/pegawai?nip={nip}` atau `GET /api/pegawai/{nip}`
- [ ] Response JSON:
  ```json
  {
    "success": true,
    "data": {
      "nip": "198801012010011001",
      "nama_lengkap": "John Doe",
      "jabatan": "Analis Kepegawaian",
      "tim_kerja": "Tim Pelayanan"
    }
  }
  ```
- [ ] Endpoint: `GET /api/pegawai` → mengembalikan daftar semua pegawai (untuk dropdown select)
- [ ] Response di-set dengan header `Content-Type: application/json`
- [ ] Error handling jika NIP tidak ditemukan

---

## Issue #16 — Integrasi Library QR Code

**Label:** `feature`, `library`, `qr-code`
**Milestone:** Manajemen Data

### Deskripsi

Mengintegrasikan library QR Code ke dalam projek untuk men-generate QR Code saat kegiatan di-publish oleh admin.

### Acceptance Criteria

- [ ] Library QR Code terintegrasi (pilihan):
  - **Server-side (PHP):** `phpqrcode` — generate QR Code sebagai gambar PNG
  - **Client-side (JS):** `qrcode.js` atau `qr-code-styling` — generate QR Code di browser
- [ ] QR Code berisi URL formulir absensi: `https://domain.com/absensi?kegiatan={id}`
- [ ] QR Code dapat ditampilkan di halaman admin (modal/popup)
- [ ] QR Code dapat di-download sebagai gambar PNG
- [ ] QR Code dapat di-print langsung dari browser
- [ ] QR Code menyertakan label nama kegiatan di bawahnya

---

## Issue #17 — UI/UX Halaman Pegawai (Tema Hijau)

**Label:** `frontend`, `pegawai`, `ui/ux`
**Milestone:** UI Pegawai

### Deskripsi

Membuat desain dan styling khusus untuk halaman-halaman yang diakses pegawai (formulir absensi, halaman sukses, halaman error) dengan tema hijau yang cerah dan nyaman.

### Acceptance Criteria

- [ ] Palet warna utama: **hijau cerah** (misal: `#2ecc71`, `#27ae60`, `#1abc9c`)
- [ ] Background: gradasi hijau lembut atau putih bersih
- [ ] Font modern dan mudah dibaca (Google Fonts: Inter, Poppins, atau Nunito)
- [ ] Responsif penuh (mobile-first, optimasi untuk layar HP)
- [ ] Animasi halus pada:
  - Transisi halaman
  - Loading state saat submit
  - Countdown timer
  - Preview foto
- [ ] Form styling yang rapi dan user-friendly
- [ ] Error state yang jelas (border merah, pesan error)
- [ ] Success state yang menyenangkan (icon centang, warna hijau)
- [ ] Tidak memerlukan login — langsung tampil formulir

---

## Issue #18 — Keamanan & Validasi

**Label:** `security`, `priority: high`
**Milestone:** Security

### Deskripsi

Implementasi fitur keamanan dasar untuk melindungi sistem dari serangan umum.

### Acceptance Criteria

- [ ] **SQL Injection Prevention:** Semua query menggunakan Prepared Statement (PDO)
- [ ] **XSS Prevention:** Output di-escape dengan `htmlspecialchars()`
- [ ] **CSRF Protection:** Token CSRF di setiap form
- [ ] **Password Hashing:** Menggunakan `password_hash()` dengan `PASSWORD_DEFAULT`
- [ ] **File Upload Validation:**
  - Cek tipe MIME yang diizinkan
  - Cek ekstensi file
  - Limit ukuran file
  - Rename file agar tidak bisa dieksekusi
- [ ] **Session Security:**
  - `session_regenerate_id()` setelah login
  - Session timeout
  - HttpOnly & Secure cookie flags
- [ ] **Input Validation:** Semua input user divalidasi di server-side
- [ ] **Error Handling:** Error tidak mengekspos informasi sensitif di production

---

## Issue #19 — Testing & Bug Fixing

**Label:** `testing`, `qa`
**Milestone:** Quality Assurance

### Deskripsi

Melakukan pengujian menyeluruh terhadap semua fitur dan memperbaiki bug yang ditemukan.

### Acceptance Criteria

- [ ] **Alur Admin:**
  - [ ] Login/Logout berfungsi
  - [ ] CRUD Pegawai berfungsi
  - [ ] CRUD Tim Kerja berfungsi
  - [ ] CRUD Jabatan berfungsi
  - [ ] CRUD Kegiatan berfungsi (termasuk Publish & QR Code)
  - [ ] Manajemen Absensi berfungsi (view, edit, hapus)
  - [ ] Export PDF berfungsi
  - [ ] Export CSV berfungsi
- [ ] **Alur Pegawai:**
  - [ ] Scan QR Code → halaman formulir tampil
  - [ ] Dropdown nama pegawai berfungsi
  - [ ] Auto-fill NIP, Jabatan, Tim Kerja berfungsi
  - [ ] Upload foto berfungsi
  - [ ] Kompresi foto < 1 MB berfungsi
  - [ ] Submit absensi berhasil tersimpan
  - [ ] Halaman sukses tampil dengan data benar
  - [ ] Auto-redirect setelah 5 detik berfungsi
  - [ ] Cegah duplikasi absensi berfungsi
- [ ] **Responsif:** Semua halaman tampil baik di desktop dan mobile
- [ ] **Cross-browser:** Diuji di Chrome, Firefox, Edge
- [ ] Semua bug yang ditemukan diperbaiki dan didokumentasikan

---

## 📊 Ringkasan Issues

| # | Issue | Label | Prioritas |
|---|-------|-------|-----------|
| 1 | Setup Struktur Projek & Konfigurasi MVC | `setup` | 🔴 High |
| 2 | Desain & Migrasi Database | `database` | 🔴 High |
| 3 | Halaman Login Admin | `auth` | 🔴 High |
| 4 | Layout & Template Admin (Sidebar) | `ui/ux` | 🟡 Medium |
| 5 | Dashboard Admin | `feature` | 🟡 Medium |
| 6 | Manajemen Pegawai (CRUD) | `crud` | 🟡 Medium |
| 7 | Manajemen Tim Kerja (CRUD) | `crud` | 🟡 Medium |
| 8 | Manajemen Jabatan (CRUD) | `crud` | 🟡 Medium |
| 9 | Manajemen Kegiatan (CRUD + QR Code) | `crud`, `qr-code` | 🔴 High |
| 10 | Manajemen Absensi | `feature` | 🟡 Medium |
| 11 | Laporan Absensi (PDF & CSV) | `reporting` | 🟢 Low |
| 12 | Halaman Absensi Pegawai (Formulir) | `pegawai` | 🔴 High |
| 13 | Kompresi Foto & Penyimpanan File | `backend` | 🔴 High |
| 14 | Halaman Sukses & Auto-Redirect | `pegawai` | 🟢 Low |
| 15 | API Endpoint Data Pegawai (AJAX) | `api` | 🟡 Medium |
| 16 | Integrasi Library QR Code | `library` | 🟡 Medium |
| 17 | UI/UX Halaman Pegawai (Tema Hijau) | `ui/ux` | 🟡 Medium |
| 18 | Keamanan & Validasi | `security` | 🔴 High |
| 19 | Testing & Bug Fixing | `testing` | 🔴 High |

---

## 🗓️ Urutan Pengerjaan (Recommended)

```
Phase 1 — Foundation
  ├── Issue #1: Setup Struktur Projek
  └── Issue #2: Desain & Migrasi Database

Phase 2 — Admin Core
  ├── Issue #3: Login Admin
  ├── Issue #4: Layout Admin (Sidebar)
  └── Issue #5: Dashboard Admin

Phase 3 — Manajemen Data (Admin)
  ├── Issue #7: CRUD Tim Kerja
  ├── Issue #8: CRUD Jabatan
  ├── Issue #6: CRUD Pegawai
  ├── Issue #16: Integrasi QR Code
  └── Issue #9: CRUD Kegiatan + Publish

Phase 4 — Fitur Pegawai
  ├── Issue #15: API Endpoint Pegawai
  ├── Issue #17: UI/UX Tema Hijau
  ├── Issue #12: Formulir Absensi Pegawai
  ├── Issue #13: Kompresi Foto
  └── Issue #14: Halaman Sukses

Phase 5 — Absensi & Laporan (Admin)
  ├── Issue #10: Manajemen Absensi
  └── Issue #11: Export PDF & CSV

Phase 6 — Finalisasi
  ├── Issue #18: Keamanan & Validasi
  └── Issue #19: Testing & Bug Fixing
```
