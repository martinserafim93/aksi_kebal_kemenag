-- ============================================================
-- MIGRASI: Tabel log_aktivitas (audit trail)
-- Mencatat aktivitas admin (login/logout, CRUD master data,
-- publish kegiatan, koreksi/hapus absensi, ekspor laporan) dan
-- pengisian absensi oleh pegawai.
-- ============================================================

-- USE `aksi_kebal`;

-- Catatan desain:
-- - TIDAK ada FOREIGN KEY ke tabel `pegawai` pada `aktor_nip` secara sengaja.
--   Audit trail harus tetap utuh walau pegawai dihapus, dan aktor untuk
--   percobaan login gagal belum tentu NIP yang valid.
-- - `created_at` mengikuti timezone sesi MySQL (+08:00 / WITA) sesuai
--   core/Database.php.
CREATE TABLE IF NOT EXISTS `log_aktivitas` (
    `id_log`      INT NOT NULL AUTO_INCREMENT,
    `aktor_nip`   VARCHAR(20)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `aktor_nama`  VARCHAR(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `aksi`        VARCHAR(30)  COLLATE utf8mb4_unicode_ci NOT NULL,
    `modul`       VARCHAR(50)  COLLATE utf8mb4_unicode_ci NOT NULL,
    `deskripsi`   VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `ip_address`  VARCHAR(45)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `user_agent`  VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_log`),
    KEY `idx_log_aktor` (`aktor_nip`),
    KEY `idx_log_aksi` (`aksi`),
    KEY `idx_log_modul` (`modul`),
    KEY `idx_log_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
