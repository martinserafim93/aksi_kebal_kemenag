-- ============================================================
-- MIGRASI: Tambah kolom file bukti untuk status Tidak Hadir
-- ============================================================

-- USE `aksi_kebal`;

-- 1. Tambah kolom 'file_bukti' untuk menyimpan nama file bukti ketidakhadiran
--    (bisa gambar atau PDF). Kolom 'foto' tetap digunakan untuk yang Hadir.
ALTER TABLE `absensi`
    ADD COLUMN `file_bukti` VARCHAR(255) DEFAULT NULL AFTER `foto`;

-- 2. Tambah kolom 'tipe_file_bukti' untuk tracking jenis file yang diupload
--    ('image' atau 'pdf')
ALTER TABLE `absensi`
    ADD COLUMN `tipe_file_bukti` ENUM('image', 'pdf') DEFAULT NULL AFTER `file_bukti`;
