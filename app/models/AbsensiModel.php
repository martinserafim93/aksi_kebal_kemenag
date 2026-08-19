<?php
/**
 * AKSI KEBAL - Absensi Model
 * 
 * Model untuk mengelola data absensi pegawai.
 * Mendukung filter, pagination, statistik, koreksi, dan hapus.
 */

class AbsensiModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil data absensi dengan filter dan pagination
     * JOIN ke tabel pegawai dan kegiatan untuk mendapatkan nama
     * 
     * @param array $filters Filter: kegiatan, jenis, tanggal
     * @param int $limit Batas per halaman
     * @param int $offset Offset data
     * @return array
     */
    public function getAllPaginated(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        $query = "SELECT a.*, 
                         p.nama_lengkap, 
                         k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan, k.waktu_mulai, k.waktu_selesai, k.lokasi_kegiatan
                  FROM absensi a
                  JOIN pegawai p ON a.nip = p.nip
                  JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan
                  WHERE 1=1";

        $query .= $this->buildFilterClause($filters);
        $query .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";

        $this->db->query($query);
        $this->bindFilters($filters);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->fetchAll();
    }

    /**
     * Ambil semua data absensi dengan filter untuk keperluan export CSV/PDF
     * 
     * @param array $filters Filter: kegiatan, jenis, tanggal
     * @return array
     */
    public function getAllFilteredForExport(array $filters = []): array
    {
        $query = "SELECT a.*, 
                         p.nama_lengkap, p.nip,
                         k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan, k.waktu_mulai, k.waktu_selesai, k.lokasi_kegiatan
                  FROM absensi a
                  JOIN pegawai p ON a.nip = p.nip
                  JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan
                  WHERE 1=1";

        $query .= $this->buildFilterClause($filters);
        $query .= " ORDER BY k.tanggal_kegiatan DESC, p.nama_lengkap ASC";

        $this->db->query($query);
        $this->bindFilters($filters);

        return $this->db->fetchAll();
    }

    /**
     * Hitung total data absensi sesuai filter (untuk pagination)
     * 
     * @param array $filters Filter aktif
     * @return int
     */
    public function countAll(array $filters = []): int
    {
        $query = "SELECT COUNT(*) as total
                  FROM absensi a
                  JOIN pegawai p ON a.nip = p.nip
                  JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan
                  WHERE 1=1";

        $query .= $this->buildFilterClause($filters);

        $this->db->query($query);
        $this->bindFilters($filters);

        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Hitung statistik kehadiran berdasarkan filter aktif
     * 
     * @param array $filters Filter aktif
     * @return array ['total' => int, 'hadir' => int, 'tidak_hadir' => int]
     */
    public function getStatistik(array $filters = []): array
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN a.status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_kehadiran = 'Tidak Hadir' THEN 1 ELSE 0 END) as tidak_hadir
                  FROM absensi a
                  JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan
                  WHERE 1=1";

        $query .= $this->buildFilterClause($filters);

        $this->db->query($query);
        $this->bindFilters($filters);

        $result = $this->db->fetch();
        return [
            'total'       => $result ? (int) $result['total'] : 0,
            'hadir'       => $result ? (int) $result['hadir'] : 0,
            'tidak_hadir' => $result ? (int) $result['tidak_hadir'] : 0
        ];
    }

    /**
     * Cari absensi berdasarkan ID
     * JOIN dengan pegawai dan kegiatan untuk data lengkap
     * 
     * @param int $id ID absensi
     * @return array|false
     */
    public function findById(int $id)
    {
        $this->db->query(
            "SELECT a.*, 
                    p.nama_lengkap, p.id_jabatan, p.id_tim_kerja,
                    j.nama_jabatan, t.nama_tim_kerja,
                    k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan, k.waktu_mulai, k.waktu_selesai
             FROM absensi a
             JOIN pegawai p ON a.nip = p.nip
             JOIN jabatan j ON p.id_jabatan = j.id_jabatan
             JOIN tim_kerja t ON p.id_tim_kerja = t.id_tim_kerja
             JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan
             WHERE a.id_absensi = :id"
        );
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->fetch();
    }

    /**
     * Update status kehadiran (koreksi)
     * 
     * @param int $id ID absensi
     * @param string $status Status baru ('Hadir' atau 'Tidak Hadir')
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $this->db->query("UPDATE absensi SET status_kehadiran = :status WHERE id_absensi = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * Hapus data absensi
     * 
     * @param int $id ID absensi
     * @return bool
     */
    public function delete(int $id): bool
    {
        // Ambil data foto sebelum hapus untuk menghapus file fisik
        $absensi = $this->findById($id);

        $this->db->query("DELETE FROM absensi WHERE id_absensi = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->execute();

        // Hapus file foto fisik jika ada
        if ($result && $absensi && !empty($absensi['foto'])) {
            $fotoPath = __DIR__ . '/../../public/uploads/foto_absensi/' . $absensi['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        return $result;
    }

    /**
     * Ambil daftar kegiatan yang Published untuk dropdown filter
     * 
     * @return array
     */
    public function getKegiatanList(): array
    {
        $this->db->query(
            "SELECT DISTINCT k.id_kegiatan, k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan
             FROM kegiatan k
             INNER JOIN absensi a ON k.id_kegiatan = a.id_kegiatan
             ORDER BY k.tanggal_kegiatan DESC"
        );
        return $this->db->fetchAll();
    }

    /**
     * Cek apakah pegawai sudah absen di kegiatan ini
     */
    public function hasAbsensi(string $nip, int $id_kegiatan): bool
    {
        $this->db->query("SELECT id_absensi FROM absensi WHERE nip = :nip AND id_kegiatan = :id_kegiatan");
        $this->db->bind(':nip', $nip);
        $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
        $result = $this->db->fetch();
        return $result !== false;
    }

    /**
     * Tambah data absensi baru
     * @return int|false
     */
    public function create(array $data)
    {
        $this->db->query("INSERT INTO absensi (nip, id_kegiatan, foto, status_kehadiran, latitude_absensi, longitude_absensi, jarak_meter, lokasi_valid) 
                          VALUES (:nip, :id_kegiatan, :foto, :status_kehadiran, :latitude_absensi, :longitude_absensi, :jarak_meter, :lokasi_valid)");
        
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':id_kegiatan', $data['id_kegiatan'], PDO::PARAM_INT);
        $this->db->bind(':foto', $data['foto']);
        $this->db->bind(':status_kehadiran', $data['status_kehadiran'] ?? 'Hadir');
        $this->db->bind(':latitude_absensi', !empty($data['latitude_absensi']) ? $data['latitude_absensi'] : null);
        $this->db->bind(':longitude_absensi', !empty($data['longitude_absensi']) ? $data['longitude_absensi'] : null);
        $this->db->bind(':jarak_meter', isset($data['jarak_meter']) && $data['jarak_meter'] !== '' ? (float)$data['jarak_meter'] : null);
        $this->db->bind(':lokasi_valid', isset($data['lokasi_valid']) && $data['lokasi_valid'] !== '' ? (int)$data['lokasi_valid'] : null);

        if ($this->db->execute()) {
            return (int) $this->db->lastInsertId();
        }
        
        return false;
    }

    // =========================================================================
    // Private Helper Methods
    // =========================================================================

    /**
     * Bangun klausa WHERE tambahan berdasarkan filter
     */
    private function buildFilterClause(array $filters): string
    {
        $clause = '';

        if (!empty($filters['kegiatan'])) {
            $clause .= " AND a.id_kegiatan = :kegiatan";
        }
        if (!empty($filters['jenis'])) {
            $clause .= " AND k.jenis_kegiatan = :jenis";
        }
        if (!empty($filters['tanggal'])) {
            $clause .= " AND k.tanggal_kegiatan = :tanggal";
        }

        return $clause;
    }

    /**
     * Bind parameter filter ke prepared statement
     */
    private function bindFilters(array $filters): void
    {
        if (!empty($filters['kegiatan'])) {
            $this->db->bind(':kegiatan', (int) $filters['kegiatan'], PDO::PARAM_INT);
        }
        if (!empty($filters['jenis'])) {
            $this->db->bind(':jenis', $filters['jenis']);
        }
        if (!empty($filters['tanggal'])) {
            $this->db->bind(':tanggal', $filters['tanggal']);
        }
    }
}
