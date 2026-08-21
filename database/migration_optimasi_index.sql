-- Migration: Optimasi Index Database (Issue #62)
-- Menambahkan index pada kolom yang sering digunakan untuk filtering dan pencarian

-- 1. Tabel absensi
-- Index komposit untuk filter id_kegiatan dan status_kehadiran
ALTER TABLE `absensi` ADD INDEX `idx_absensi_kegiatan_status` (`id_kegiatan`, `status_kehadiran`);
-- Index pada waktu mulai dan selesai untuk query date/time
ALTER TABLE `absensi` ADD INDEX `idx_absensi_created_at` (`created_at`);

-- 2. Tabel pegawai
-- Index untuk mempercepat pencarian nama lengkap pegawai
ALTER TABLE `pegawai` ADD INDEX `idx_pegawai_nama` (`nama_lengkap`);

-- 3. Tabel kegiatan
-- Index pada status kegiatan
ALTER TABLE `kegiatan` ADD INDEX `idx_kegiatan_status` (`status_kegiatan`);
-- Index pada tanggal kegiatan
ALTER TABLE `kegiatan` ADD INDEX `idx_kegiatan_tanggal` (`tanggal_kegiatan`);
