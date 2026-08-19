-- ============================================================
-- MIGRASI: Tambah kolom koordinat GPS untuk fitur validasi lokasi
-- ============================================================

USE `aksi_kebal`;

-- 1. Tambah kolom koordinat di tabel KEGIATAN
--    Ini adalah titik lokasi kegiatan yang ditentukan admin
ALTER TABLE `kegiatan`
    ADD COLUMN `latitude_kegiatan`  DECIMAL(10, 8) DEFAULT NULL AFTER `lokasi_kegiatan`,
    ADD COLUMN `longitude_kegiatan` DECIMAL(11, 8) DEFAULT NULL AFTER `latitude_kegiatan`,
    ADD COLUMN `radius_meter`       INT DEFAULT 50 AFTER `longitude_kegiatan`;

-- 2. Tambah kolom koordinat di tabel ABSENSI
--    Ini adalah lokasi GPS pegawai saat melakukan absensi
ALTER TABLE `absensi`
    ADD COLUMN `latitude_absensi`   DECIMAL(10, 8) DEFAULT NULL AFTER `foto`,
    ADD COLUMN `longitude_absensi`  DECIMAL(11, 8) DEFAULT NULL AFTER `latitude_absensi`,
    ADD COLUMN `jarak_meter`        DECIMAL(10, 2) DEFAULT NULL AFTER `longitude_absensi`,
    ADD COLUMN `lokasi_valid`       TINYINT(1) DEFAULT NULL AFTER `jarak_meter`;

-- 3. Index untuk query lokasi
CREATE INDEX `idx_kegiatan_lokasi` ON `kegiatan` (`latitude_kegiatan`, `longitude_kegiatan`);
CREATE INDEX `idx_absensi_lokasi_valid` ON `absensi` (`lokasi_valid`);
