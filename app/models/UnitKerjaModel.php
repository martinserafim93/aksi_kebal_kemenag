<?php
/**
 * AKSI KEBAL - Unit Kerja Model
 * 
 * Menangani operasi database untuk entitas Unit Kerja.
 */

class UnitKerjaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil semua data unit kerja beserta jumlah anggotanya
     *
     * @return array
     */
    public function getAll(): array
    {
        // Join dengan tabel pegawai untuk menghitung jumlah anggota per unit kerja
        $query = "SELECT u.id_unit_kerja, u.nama_unit_kerja, u.created_at, 
                         COUNT(p.nip) as jumlah_anggota 
                  FROM unit_kerja u
                  LEFT JOIN pegawai p ON u.id_unit_kerja = p.id_unit_kerja
                  GROUP BY u.id_unit_kerja, u.nama_unit_kerja, u.created_at
                  ORDER BY u.nama_unit_kerja ASC";
                  
        $this->db->query($query);
        return $this->db->fetchAll();
    }

    /**
     * Ambil data unit kerja dengan pagination dan pencarian
     */
    public function getAllPaginated(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $query = "SELECT u.id_unit_kerja, u.nama_unit_kerja, u.created_at, 
                         COUNT(p.nip) as jumlah_anggota 
                  FROM unit_kerja u
                  LEFT JOIN pegawai p ON u.id_unit_kerja = p.id_unit_kerja";
                  
        if (!empty($search)) {
            $query .= " WHERE u.nama_unit_kerja LIKE :search";
        }
        
        $query .= " GROUP BY u.id_unit_kerja, u.nama_unit_kerja, u.created_at
                    ORDER BY u.nama_unit_kerja ASC LIMIT :limit OFFSET :offset";

        $this->db->query($query);

        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->fetchAll();
    }

    /**
     * Menghitung total data unit kerja (untuk pagination)
     */
    public function countAll(string $search = ''): int
    {
        $query = "SELECT COUNT(*) as total FROM unit_kerja";
        
        if (!empty($search)) {
            $query .= " WHERE nama_unit_kerja LIKE :search";
        }

        $this->db->query($query);

        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }

        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Ambil unit kerja berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id)
    {
        $this->db->query("SELECT * FROM unit_kerja WHERE id_unit_kerja = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->fetch();
    }

    /**
     * Cek apakah nama unit kerja sudah ada
     * 
     * @param string $nama
     * @param int|null $excludeId ID untuk dikecualikan saat update
     * @return bool
     */
    public function isNameExists(string $nama, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as total FROM unit_kerja WHERE nama_unit_kerja = :nama";
        if ($excludeId !== null) {
            $query .= " AND id_unit_kerja != :excludeId";
        }

        $this->db->query($query);
        $this->db->bind(':nama', $nama);
        
        if ($excludeId !== null) {
            $this->db->bind(':excludeId', $excludeId, PDO::PARAM_INT);
        }

        $result = $this->db->fetch();
        return $result['total'] > 0;
    }

    /**
     * Tambah data unit kerja baru
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $query = "INSERT INTO unit_kerja (nama_unit_kerja) VALUES (:nama_unit_kerja)";
        $this->db->query($query);
        $this->db->bind(':nama_unit_kerja', $data['nama_unit_kerja']);
        return $this->db->execute();
    }

    /**
     * Update data unit kerja
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $query = "UPDATE unit_kerja SET nama_unit_kerja = :nama_unit_kerja WHERE id_unit_kerja = :id";
        $this->db->query($query);
        $this->db->bind(':nama_unit_kerja', $data['nama_unit_kerja']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Hapus data unit kerja
     * Hanya akan berhasil jika tidak ada pegawai yang memiliki id_unit_kerja ini
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->db->query("SELECT COUNT(*) as total FROM pegawai WHERE id_unit_kerja = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->fetch();
        
        if ($result['total'] > 0) {
            return false;
        }

        $this->db->query("DELETE FROM unit_kerja WHERE id_unit_kerja = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}
