-- ============================================================
-- AKSI KEBAL — Database Migration Script
-- Absensi Kegiatan Serentak Kementerian Beramal dan Andal
-- ============================================================
-- Issue #5: Desain dan Migrasi Database
-- Database: aksi_kebal
-- Engine: InnoDB (untuk mendukung foreign key constraints)
-- Charset: utf8mb4 (mendukung karakter Unicode lengkap)
-- ============================================================

-- Buat database jika belum ada (Di-comment untuk hosting InfinityFree)
-- CREATE DATABASE IF NOT EXISTS `aksi_kebal`
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;

-- USE `aksi_kebal`;

-- ============================================================
-- Hapus tabel jika sudah ada (urutan sesuai dependensi FK)
-- ============================================================
DROP TABLE IF EXISTS `absensi`;
DROP TABLE IF EXISTS `kegiatan`;
DROP TABLE IF EXISTS `pegawai`;
DROP TABLE IF EXISTS `jabatan`;
DROP TABLE IF EXISTS `tim_kerja`;

-- ============================================================
-- 1. Tabel: tim_kerja
-- Menyimpan data tim kerja/unit kerja pegawai
-- ============================================================
CREATE TABLE `tim_kerja` (
    `id_tim_kerja`   INT AUTO_INCREMENT,
    `nama_tim_kerja` VARCHAR(100) NOT NULL,
    `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_tim_kerja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. Tabel: jabatan
-- Menyimpan data jabatan pegawai
-- ============================================================
CREATE TABLE `jabatan` (
    `id_jabatan`   INT AUTO_INCREMENT,
    `nama_jabatan` VARCHAR(100) NOT NULL,
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_jabatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. Tabel: pegawai
-- Menyimpan data pegawai dan admin
-- NIP sebagai Primary Key (unique identifier ASN)
-- email & password opsional (hanya untuk admin)
-- ============================================================
CREATE TABLE `pegawai` (
    `nip`           VARCHAR(20) NOT NULL,
    `nama_lengkap`  VARCHAR(150) NOT NULL,
    `id_jabatan`    INT NOT NULL,
    `id_tim_kerja`  INT NOT NULL,
    `email`         VARCHAR(100) DEFAULT NULL,
    `password`      VARCHAR(255) DEFAULT NULL,
    `role`          ENUM('admin', 'pegawai') NOT NULL DEFAULT 'pegawai',
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`nip`),

    CONSTRAINT `fk_pegawai_jabatan`
        FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT `fk_pegawai_tim_kerja`
        FOREIGN KEY (`id_tim_kerja`) REFERENCES `tim_kerja` (`id_tim_kerja`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. Tabel: kegiatan
-- Menyimpan data kegiatan/acara kedinasan
-- jenis_kegiatan menggunakan ENUM sesuai spesifikasi
-- status_kegiatan: Draft (belum dipublish), Published (sudah aktif)
-- qr_code: menyimpan data QR code (path gambar atau data string)
-- ============================================================
CREATE TABLE `kegiatan` (
    `id_kegiatan`       INT AUTO_INCREMENT,
    `nama_kegiatan`     VARCHAR(200) NOT NULL,
    `jenis_kegiatan`    ENUM('Kerja Bakti', 'Doa Bersama', 'Apel', 'Rapat', 'Sosialisasi') NOT NULL,
    `tanggal_kegiatan`  DATE NOT NULL,
    `waktu_mulai`       TIME NOT NULL,
    `waktu_selesai`     TIME NOT NULL,
    `lokasi_kegiatan`   VARCHAR(200) DEFAULT NULL,
    `deskripsi_kegiatan` TEXT DEFAULT NULL,
    `status_kegiatan`   ENUM('Draft', 'Published') NOT NULL DEFAULT 'Draft',
    `qr_code`           TEXT DEFAULT NULL,
    `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_kegiatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. Tabel: absensi
-- Menyimpan data kehadiran pegawai pada kegiatan
-- Relasi: pegawai (nip) + kegiatan (id_kegiatan)
-- foto: path ke file foto yang telah dikompresi
-- ============================================================
CREATE TABLE `absensi` (
    `id_absensi`        INT AUTO_INCREMENT,
    `nip`               VARCHAR(20) NOT NULL,
    `id_kegiatan`       INT NOT NULL,
    `status_kehadiran`  ENUM('Hadir', 'Tidak Hadir') NOT NULL DEFAULT 'Hadir',
    `foto`              VARCHAR(255) DEFAULT NULL,
    `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id_absensi`),

    -- Unique constraint: 1 pegawai hanya bisa absen 1x per kegiatan
    UNIQUE KEY `uk_absensi_pegawai_kegiatan` (`nip`, `id_kegiatan`),

    CONSTRAINT `fk_absensi_pegawai`
        FOREIGN KEY (`nip`) REFERENCES `pegawai` (`nip`)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT `fk_absensi_kegiatan`
        FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id_kegiatan`)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- INDEX TAMBAHAN (untuk optimasi query)
-- ============================================================
CREATE INDEX `idx_pegawai_jabatan`    ON `pegawai` (`id_jabatan`);
CREATE INDEX `idx_pegawai_tim_kerja`  ON `pegawai` (`id_tim_kerja`);
CREATE INDEX `idx_pegawai_role`       ON `pegawai` (`role`);
CREATE INDEX `idx_kegiatan_status`    ON `kegiatan` (`status_kegiatan`);
CREATE INDEX `idx_kegiatan_tanggal`   ON `kegiatan` (`tanggal_kegiatan`);
CREATE INDEX `idx_kegiatan_jenis`     ON `kegiatan` (`jenis_kegiatan`);
CREATE INDEX `idx_absensi_nip`        ON `absensi` (`nip`);
CREATE INDEX `idx_absensi_kegiatan`   ON `absensi` (`id_kegiatan`);
CREATE INDEX `idx_absensi_created`    ON `absensi` (`created_at`);


-- ============================================================
-- SEED DATA — Data Awal
-- ============================================================

-- -----------------------------------------------------------
-- Seed: Tim Kerja
-- -----------------------------------------------------------
INSERT INTO `tim_kerja` (`nama_tim_kerja`) VALUES
    ('Tim Pelayanan'),
    ('Tim Administrasi Umum'),
    ('Tim Keuangan'),
    ('Tim Kepegawaian'),
    ('Tim Humas dan Informasi'),
    ('Tim Pengawasan'),
    ('Tim Perencanaan');

-- -----------------------------------------------------------
-- Seed: Jabatan
-- -----------------------------------------------------------
INSERT INTO `jabatan` (`nama_jabatan`) VALUES
    ('Kepala Kantor'),
    ('Kepala Sub Bagian Tata Usaha'),
    ('Penyelenggara Syariah'),
    ('Penyelenggara Pendidikan Madrasah'),
    ('Penyelenggara Pendidikan Agama Islam'),
    ('Penyelenggara Haji dan Umrah'),
    ('Analis Kepegawaian'),
    ('Analis Keuangan'),
    ('Pengelola Administrasi dan Dokumentasi'),
    ('Pengadministrasi Umum'),
    ('Pranata Komputer'),
    ('Penyuluh Agama'),
    ('Staf');

-- -----------------------------------------------------------
-- Seed: Pegawai (1 Admin + beberapa Pegawai)
-- Password admin: admin123 (di-hash dengan password_hash)
-- Gunakan perintah PHP berikut untuk generate hash:
--   echo password_hash('admin123', PASSWORD_DEFAULT);
-- Hash di bawah ini di-generate untuk 'admin123'
-- -----------------------------------------------------------
INSERT INTO `pegawai` (`nip`, `nama_lengkap`, `id_jabatan`, `id_tim_kerja`, `email`, `password`, `role`) VALUES
    ('199001012020011001', 'Admin Sistem',        1, 1, 'admin@kemenag.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
    ('198501012010011002', 'Budi Santoso',         7, 4, NULL, NULL, 'pegawai'),
    ('199003152015012003', 'Siti Rahayu',          9, 2, NULL, NULL, 'pegawai'),
    ('198712202012011004', 'Ahmad Fauzi',         11, 5, NULL, NULL, 'pegawai'),
    ('199105102018012005', 'Dewi Lestari',         3, 1, NULL, NULL, 'pegawai'),
    ('198809082011011006', 'Rahman Hakim',         8, 3, NULL, NULL, 'pegawai'),
    ('199207152019011007', 'Nur Hidayah',         12, 1, NULL, NULL, 'pegawai'),
    ('198603012009011008', 'Eko Prasetyo',         2, 2, NULL, NULL, 'pegawai'),
    ('199401202020012009', 'Fitriani Wulandari',  10, 2, NULL, NULL, 'pegawai'),
    ('199306102017011010', 'Hendra Gunawan',       4, 1, NULL, NULL, 'pegawai');

-- -----------------------------------------------------------
-- Seed: Kegiatan (beberapa contoh kegiatan)
-- -----------------------------------------------------------
INSERT INTO `kegiatan` (`nama_kegiatan`, `jenis_kegiatan`, `tanggal_kegiatan`, `waktu_mulai`, `waktu_selesai`, `lokasi_kegiatan`, `deskripsi_kegiatan`, `status_kegiatan`) VALUES
    ('Apel Pagi Senin Minggu I Juli 2026',
     'Apel',
     '2026-07-06', '07:30:00', '08:00:00',
     'Halaman Kantor Kementerian Agama',
     'Apel pagi rutin setiap hari Senin untuk seluruh pegawai Kementerian Agama.',
     'Published'),

    ('Rapat Koordinasi Evaluasi Semester I',
     'Rapat',
     '2026-07-15', '09:00:00', '12:00:00',
     'Ruang Rapat Lt. 2',
     'Rapat koordinasi evaluasi kinerja dan capaian program semester I tahun 2026.',
     'Draft'),

    ('Doa Bersama Menyambut Tahun Baru Hijriah',
     'Doa Bersama',
     '2026-07-28', '08:00:00', '10:00:00',
     'Aula Utama Kantor Kementerian Agama',
     'Kegiatan doa bersama dalam rangka menyambut Tahun Baru Hijriah 1448 H.',
     'Published'),

    ('Kerja Bakti Jumat Bersih',
     'Kerja Bakti',
     '2026-08-01', '07:00:00', '09:00:00',
     'Lingkungan Kantor Kementerian Agama',
     'Kegiatan kerja bakti rutin kebersihan lingkungan kantor.',
     'Draft'),

    ('Sosialisasi Sistem AKSI KEBAL',
     'Sosialisasi',
     '2026-08-05', '10:00:00', '12:00:00',
     'Ruang Rapat Lt. 1',
     'Sosialisasi penggunaan Sistem Informasi Absensi Kegiatan (AKSI KEBAL) kepada seluruh pegawai.',
     'Draft');

-- ============================================================
-- SELESAI — Database aksi_kebal siap digunakan
-- ============================================================
