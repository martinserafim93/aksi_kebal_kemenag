-- ============================================================
-- MIGRASI: Tambah kolom kode unik absensi
-- ============================================================

-- USE `aksi_kebal`;

-- 1. Tambah kolom kode_absensi (unique, pendek)
ALTER TABLE `absensi`
    ADD COLUMN `kode_absensi` VARCHAR(10) DEFAULT NULL AFTER `id_absensi`;

-- 2. Isi kode untuk absensi yang sudah ada
--    (Gunakan format: huruf acak + angka, misal 'ABS001')
UPDATE `absensi`
SET `kode_absensi` = CONCAT('ABS', LPAD(id_absensi, 3, '0'))
WHERE `kode_absensi` IS NULL;

-- 3. Set NOT NULL dan UNIQUE setelah data terisi
ALTER TABLE `absensi`
    MODIFY COLUMN `kode_absensi` VARCHAR(10) NOT NULL,
    ADD UNIQUE INDEX `idx_kode_absensi` (`kode_absensi`);
