<?php
/**
 * AKSI KEBAL - Dashboard Model
 * 
 * Model untuk mengambil data agregat dan statistik
 * yang ditampilkan di halaman dashboard admin.
 */

class DashboardModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Hitung total seluruh pegawai
     */
    public function getTotalPegawai(): int
    {
        $this->db->query("SELECT COUNT(*) as total FROM pegawai");
        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Hitung total seluruh kegiatan
     */
    public function getTotalKegiatan(): int
    {
        $this->db->query("SELECT COUNT(*) as total FROM kegiatan");
        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Hitung total kegiatan dengan status 'published'
     */
    public function getTotalKegiatanPublished(): int
    {
        $this->db->query("SELECT COUNT(*) as total FROM kegiatan WHERE status_kegiatan = 'Published'");
        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Hitung total absensi hari ini
     */
    public function getTotalAbsensiHariIni(): int
    {
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $this->db->query("SELECT COUNT(*) as total FROM absensi WHERE created_at BETWEEN :start AND :end");
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Ambil data kegiatan terbaru
     * 
     * @param int $limit Batas jumlah kegiatan yang diambil
     * @return array
     */
    public function getKegiatanTerbaru(int $limit = 5): array
    {
        $this->db->query(
            "SELECT id_kegiatan, nama_kegiatan, tanggal_kegiatan, waktu_mulai, waktu_selesai, lokasi_kegiatan, status_kegiatan 
             FROM kegiatan 
             ORDER BY created_at DESC 
             LIMIT :limit"
        );
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->fetchAll();
    }
}
