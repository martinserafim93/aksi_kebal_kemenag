-- ============================================================
-- MIGRASI: Tambah kolom kode unik kegiatan (Issue #58)
-- ============================================================

USE `aksi_kebal`;

-- 1. Tambah kolom kode_kegiatan (unique, pendek)
ALTER TABLE `kegiatan`
    ADD COLUMN `kode_kegiatan` VARCHAR(10) DEFAULT NULL AFTER `id_kegiatan`;

-- 2. Isi kode untuk kegiatan yang sudah ada
--    (Gunakan format: huruf acak + angka, misal 'KGT001')
UPDATE `kegiatan`
SET `kode_kegiatan` = CONCAT('KGT', LPAD(id_kegiatan, 3, '0'))
WHERE `kode_kegiatan` IS NULL;

-- 3. Set NOT NULL dan UNIQUE setelah data terisi
ALTER TABLE `kegiatan`
    MODIFY COLUMN `kode_kegiatan` VARCHAR(10) NOT NULL,
    ADD UNIQUE INDEX `idx_kode_kegiatan` (`kode_kegiatan`);
