<?php
/**
 * AKSI KEBAL - Tim Kerja Model
 * 
 * Menangani operasi database untuk entitas Tim Kerja.
 */

class TimKerjaModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil semua data tim kerja beserta jumlah anggotanya
     *
     * @return array
     */
    public function getAll(): array
    {
        // Join dengan tabel pegawai untuk menghitung jumlah anggota per tim kerja
        $query = "SELECT t.id_tim_kerja, t.nama_tim_kerja, t.slug_tim_kerja, t.created_at, 
                         COUNT(p.nip) as jumlah_anggota 
                  FROM tim_kerja t
                  LEFT JOIN pegawai p ON t.id_tim_kerja = p.id_tim_kerja
                  GROUP BY t.id_tim_kerja, t.nama_tim_kerja, t.slug_tim_kerja, t.created_at
                  ORDER BY t.nama_tim_kerja ASC";
                  
        $this->db->query($query);
        return $this->db->fetchAll();
    }

    /**
     * Ambil data tim kerja dengan pagination dan pencarian
     */
    public function getAllPaginated(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $query = "SELECT t.id_tim_kerja, t.nama_tim_kerja, t.slug_tim_kerja, t.created_at, 
                         COUNT(p.nip) as jumlah_anggota 
                  FROM tim_kerja t
                  LEFT JOIN pegawai p ON t.id_tim_kerja = p.id_tim_kerja";
                  
        if (!empty($search)) {
            $query .= " WHERE t.nama_tim_kerja LIKE :search";
        }
        
        $query .= " GROUP BY t.id_tim_kerja, t.nama_tim_kerja, t.slug_tim_kerja, t.created_at
                    ORDER BY t.nama_tim_kerja ASC LIMIT :limit OFFSET :offset";

        $this->db->query($query);

        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->fetchAll();
    }

    /**
     * Menghitung total data tim kerja (untuk pagination)
     */
    public function countAll(string $search = ''): int
    {
        $query = "SELECT COUNT(*) as total FROM tim_kerja";
        
        if (!empty($search)) {
            $query .= " WHERE nama_tim_kerja LIKE :search";
        }

        $this->db->query($query);

        if (!empty($search)) {
            $this->db->bind(':search', "%$search%");
        }

        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Ambil tim kerja berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id)
    {
        $this->db->query("SELECT * FROM tim_kerja WHERE id_tim_kerja = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->fetch();
    }

    /**
     * Ambil tim kerja berdasarkan slug
     *
     * @param string $slug
     * @return array|false
     */
    public function findBySlug(string $slug)
    {
        $this->db->query("SELECT * FROM tim_kerja WHERE slug_tim_kerja = :slug");
        $this->db->bind(':slug', $slug);
        return $this->db->fetch();
    }

    /**
     * Cek apakah slug tim kerja sudah ada
     * 
     * @param string $slug
     * @param int|null $excludeId ID untuk dikecualikan saat update
     * @return bool
     */
    public function isSlugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as total FROM tim_kerja WHERE slug_tim_kerja = :slug";
        if ($excludeId !== null) {
            $query .= " AND id_tim_kerja != :excludeId";
        }

        $this->db->query($query);
        $this->db->bind(':slug', $slug);
        
        if ($excludeId !== null) {
            $this->db->bind(':excludeId', $excludeId, PDO::PARAM_INT);
        }

        $result = $this->db->fetch();
        return $result['total'] > 0;
    }

    /**
     * Tambah data tim kerja baru
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $slug = generateSlug($data['nama_tim_kerja']);

        $query = "INSERT INTO tim_kerja (nama_tim_kerja, slug_tim_kerja) VALUES (:nama_tim_kerja, :slug)";
        $this->db->query($query);
        $this->db->bind(':nama_tim_kerja', $data['nama_tim_kerja']);
        $this->db->bind(':slug', $slug);
        return $this->db->execute();
    }

    /**
     * Update data tim kerja
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $slug = generateSlug($data['nama_tim_kerja']);

        $query = "UPDATE tim_kerja SET nama_tim_kerja = :nama_tim_kerja, slug_tim_kerja = :slug WHERE id_tim_kerja = :id";
        $this->db->query($query);
        $this->db->bind(':nama_tim_kerja', $data['nama_tim_kerja']);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Hapus data tim kerja
     * Hanya akan berhasil jika tidak ada pegawai yang memiliki id_tim_kerja ini
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        // Periksa jumlah anggota terlebih dahulu (sudah dilakukan di controller, 
        // tapi ini adalah validasi ekstra di level model)
        $this->db->query("SELECT COUNT(*) as total FROM pegawai WHERE id_tim_kerja = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->fetch();
        
        if ($result['total'] > 0) {
            return false;
        }

        $this->db->query("DELETE FROM tim_kerja WHERE id_tim_kerja = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}
