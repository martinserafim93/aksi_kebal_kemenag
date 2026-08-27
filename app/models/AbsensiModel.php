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
        $query = "SELECT a.id_absensi, a.kode_absensi, a.nip, a.id_kegiatan, a.status_kehadiran, a.created_at, a.foto, a.file_bukti, a.tipe_file_bukti,
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
     * Ambil pegawai yang tidak melakukan absensi sama sekali pada kegiatan tertentu
     */
    public function getPegawaiTidakAbsen(int $id_kegiatan): array
    {
        $this->db->query(
            "SELECT p.nip, p.nama_lengkap
             FROM pegawai p
             WHERE p.nip NOT IN (
                 SELECT a.nip FROM absensi a WHERE a.id_kegiatan = :id_kegiatan
             )
             ORDER BY p.nama_lengkap ASC"
        );
        $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
        return $this->db->fetchAll();
    }

    /**
     * Hitung statistik absensi yang mencakup pegawai tidak mengisi absensi
     */
    public function getStatistikLengkap(int $id_kegiatan): array
    {
        // Hitung pegawai yang sudah absen
        $this->db->query(
            "SELECT 
                SUM(CASE WHEN status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status_kehadiran = 'Tidak Hadir' THEN 1 ELSE 0 END) as tidak_hadir_absen
             FROM absensi WHERE id_kegiatan = :id_kegiatan"
        );
        $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
        $result = $this->db->fetch();

        // Hitung total pegawai di sistem
        $this->db->query("SELECT COUNT(*) as total FROM pegawai");
        $totalPegawai = (int) $this->db->fetch()['total'];

        // Hitung pegawai yang tidak melakukan absensi sama sekali
        $this->db->query(
            "SELECT COUNT(*) as jumlah FROM pegawai
             WHERE nip NOT IN (SELECT nip FROM absensi WHERE id_kegiatan = :id_kegiatan)"
        );
        $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
        $tidakAbsen = (int) $this->db->fetch()['jumlah'];

        $hadir = $result && $result['hadir'] ? (int) $result['hadir'] : 0;
        $tidakHadirAbsen = $result && $result['tidak_hadir_absen'] ? (int) $result['tidak_hadir_absen'] : 0;

        return [
            'total_pegawai'    => $totalPegawai,
            'hadir'            => $hadir,
            'tidak_hadir'      => $tidakHadirAbsen,
            'tidak_absen'      => $tidakAbsen,
        ];
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
                  FROM absensi a";

        if (!empty($filters['jenis']) || !empty($filters['tanggal'])) {
            $query .= " JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan";
        }
        
        $query .= " WHERE 1=1";

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
                    k.kode_kegiatan, k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan, k.waktu_mulai, k.waktu_selesai
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

    public function findByKodeAbsensi(string $kode)
    {
        $this->db->query(
            "SELECT a.*, 
                    p.nama_lengkap, p.id_jabatan, p.id_tim_kerja,
                    j.nama_jabatan, t.nama_tim_kerja,
                    k.kode_kegiatan, k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan, k.waktu_mulai, k.waktu_selesai
             FROM absensi a
             JOIN pegawai p ON a.nip = p.nip
             JOIN jabatan j ON p.id_jabatan = j.id_jabatan
             LEFT JOIN tim_kerja t ON p.id_tim_kerja = t.id_tim_kerja
             JOIN kegiatan k ON a.id_kegiatan = k.id_kegiatan
             WHERE a.kode_absensi = :kode"
        );
        $this->db->bind(':kode', $kode);
        return $this->db->fetch();
    }

    public function generateKodeAbsensi(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        
        for ($i = 0; $i < 3; $i++) {
            $candidates = [];
            for ($c = 0; $c < 5; $c++) {
                $kode = '';
                for ($j = 0; $j < 6; $j++) {
                    $kode .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $candidates[] = $kode;
            }
            
            $placeholders = implode(',', array_fill(0, count($candidates), '?'));
            $this->db->query("SELECT kode_absensi FROM absensi WHERE kode_absensi IN ($placeholders)");
            
            $stmt = $this->db->getStatement();
            foreach ($candidates as $index => $candidate) {
                $stmt->bindValue($index + 1, $candidate);
            }
            $existing = $this->db->fetchAll();
            $existingCodes = array_column($existing, 'kode_absensi');
            
            foreach ($candidates as $candidate) {
                if (!in_array($candidate, $existingCodes)) {
                    return $candidate;
                }
            }
        }
        
        return strtoupper(substr(md5(microtime()), 0, 6));
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

        // Hapus file foto fisik jika ada (Hadir)
        if ($result && $absensi && !empty($absensi['foto'])) {
            $fotoPath = __DIR__ . '/../../public/uploads/foto_absensi/' . $absensi['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }
        
        // Hapus file bukti (Tidak Hadir)
        if ($result && $absensi && !empty($absensi['file_bukti'])) {
            $buktiPath = __DIR__ . '/../../public/uploads/file_bukti/' . $absensi['file_bukti'];
            if (file_exists($buktiPath)) {
                unlink($buktiPath);
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
            "SELECT k.id_kegiatan, k.nama_kegiatan, k.jenis_kegiatan, k.tanggal_kegiatan
             FROM kegiatan k
             WHERE EXISTS (
                 SELECT 1 FROM absensi a WHERE a.id_kegiatan = k.id_kegiatan
             )
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
     * @return string|false
     */
    public function create(array $data)
    {
        $kode_absensi = $this->generateKodeAbsensi();

        $this->db->query("INSERT INTO absensi (kode_absensi, nip, id_kegiatan, foto, file_bukti, tipe_file_bukti, alasan_tidak_hadir, status_kehadiran, latitude_absensi, longitude_absensi, jarak_meter, lokasi_valid) 
                          VALUES (:kode_absensi, :nip, :id_kegiatan, :foto, :file_bukti, :tipe_file_bukti, :alasan_tidak_hadir, :status_kehadiran, :latitude_absensi, :longitude_absensi, :jarak_meter, :lokasi_valid)");
        
        $this->db->bind(':kode_absensi', $kode_absensi);
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':id_kegiatan', $data['id_kegiatan'], PDO::PARAM_INT);
        $this->db->bind(':foto', $data['foto']);
        $this->db->bind(':file_bukti', $data['file_bukti'] ?? null);
        $this->db->bind(':tipe_file_bukti', $data['tipe_file_bukti'] ?? null);
        $this->db->bind(':alasan_tidak_hadir', $data['alasan_tidak_hadir'] ?? null);
        $this->db->bind(':status_kehadiran', $data['status_kehadiran'] ?? 'Hadir');
        $this->db->bind(':latitude_absensi', !empty($data['latitude_absensi']) ? $data['latitude_absensi'] : null);
        $this->db->bind(':longitude_absensi', !empty($data['longitude_absensi']) ? $data['longitude_absensi'] : null);
        $this->db->bind(':jarak_meter', isset($data['jarak_meter']) && $data['jarak_meter'] !== '' ? (float)$data['jarak_meter'] : null);
        $this->db->bind(':lokasi_valid', isset($data['lokasi_valid']) && $data['lokasi_valid'] !== '' ? (int)$data['lokasi_valid'] : null);

        if ($this->db->execute()) {
            return $kode_absensi;
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
        if (!empty($filters['search'])) {
            $clause .= " AND (p.nama_lengkap LIKE :search OR p.nip LIKE :search)";
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
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
    }
}
