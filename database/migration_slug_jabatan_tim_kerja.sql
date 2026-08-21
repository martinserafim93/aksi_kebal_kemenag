-- ============================================================
-- MIGRASI: Tambah kolom slug untuk jabatan & tim_kerja
-- ============================================================

-- ============================================
-- A. Tabel jabatan
-- ============================================

-- 1) Tambah kolom slug_jabatan
ALTER TABLE `jabatan`
    ADD COLUMN `slug_jabatan` VARCHAR(120) DEFAULT NULL AFTER `nama_jabatan`;

-- 2) Isi slug untuk data yang sudah ada
--    Contoh: "Kepala Kantor" → "kepala-kantor"
UPDATE `jabatan`
SET `slug_jabatan` = LOWER(REPLACE(REPLACE(REPLACE(TRIM(nama_jabatan), ' ', '-'), '.', ''), ',', ''))
WHERE `slug_jabatan` IS NULL;

-- 3) Set NOT NULL dan UNIQUE
ALTER TABLE `jabatan`
    MODIFY COLUMN `slug_jabatan` VARCHAR(120) NOT NULL,
    ADD UNIQUE INDEX `idx_slug_jabatan` (`slug_jabatan`);


-- ============================================
-- B. Tabel tim_kerja
-- ============================================

-- 1) Tambah kolom slug_tim_kerja
ALTER TABLE `tim_kerja`
    ADD COLUMN `slug_tim_kerja` VARCHAR(120) DEFAULT NULL AFTER `nama_tim_kerja`;

-- 2) Isi slug untuk data yang sudah ada
UPDATE `tim_kerja`
SET `slug_tim_kerja` = LOWER(REPLACE(REPLACE(REPLACE(TRIM(nama_tim_kerja), ' ', '-'), '.', ''), ',', ''))
WHERE `slug_tim_kerja` IS NULL;

-- 3) Set NOT NULL dan UNIQUE
ALTER TABLE `tim_kerja`
    MODIFY COLUMN `slug_tim_kerja` VARCHAR(120) NOT NULL,
    ADD UNIQUE INDEX `idx_slug_tim_kerja` (`slug_tim_kerja`);
