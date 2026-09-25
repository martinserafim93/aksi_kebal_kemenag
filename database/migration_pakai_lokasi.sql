-- MIGRASI: Tambah kolom pakai_lokasi pada tabel kegiatan
-- Nilai default 1 = kegiatan luring (pakai lokasi).

ALTER TABLE `kegiatan`
    ADD COLUMN `pakai_lokasi` TINYINT(1) NOT NULL DEFAULT 1
        COMMENT '1 = luring (ada koordinat), 0 = daring (Zoom, dll.)'
        AFTER `deskripsi_kegiatan`;
