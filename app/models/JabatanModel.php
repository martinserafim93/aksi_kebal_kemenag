<?php
/**
 * AKSI KEBAL - Jabatan Model
 * 
 * Menangani operasi database untuk entitas Jabatan.
 */

class JabatanModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil semua data jabatan beserta jumlah pegawainya
     *
     * @return array
     */
    public function getAll(): array
    {
        // Join dengan tabel pegawai untuk menghitung jumlah pegawai per jabatan
        $query = "SELECT j.id_jabatan, j.nama_jabatan, j.slug_jabatan, j.created_at, 
                         COUNT(p.nip) as jumlah_pegawai 
                  FROM jabatan j
                  LEFT JOIN pegawai p ON j.id_jabatan = p.id_jabatan
                  GROUP BY j.id_jabatan, j.nama_jabatan, j.slug_jabatan, j.created_at
                  ORDER BY j.nama_jabatan ASC";
                  
        $this->db->query($query);
        return $this->db->fetchAll();
    }

    /**
     * Ambil jabatan berdasarkan ID
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id)
    {
        $this->db->query("SELECT * FROM jabatan WHERE id_jabatan = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->fetch();
    }

    /**
     * Ambil jabatan berdasarkan slug
     *
     * @param string $slug
     * @return array|false
     */
    public function findBySlug(string $slug)
    {
        $this->db->query("SELECT * FROM jabatan WHERE slug_jabatan = :slug");
        $this->db->bind(':slug', $slug);
        return $this->db->fetch();
    }

    /**
     * Cek apakah slug jabatan sudah ada
     * 
     * @param string $slug
     * @param int|null $excludeId ID untuk dikecualikan saat update
     * @return bool
     */
    public function isSlugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as total FROM jabatan WHERE slug_jabatan = :slug";
        if ($excludeId !== null) {
            $query .= " AND id_jabatan != :excludeId";
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
     * Tambah data jabatan baru
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $slug = generateSlug($data['nama_jabatan']);

        $query = "INSERT INTO jabatan (nama_jabatan, slug_jabatan) VALUES (:nama_jabatan, :slug)";
        $this->db->query($query);
        $this->db->bind(':nama_jabatan', $data['nama_jabatan']);
        $this->db->bind(':slug', $slug);

        return $this->db->execute();
    }

    /**
     * Update data jabatan
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $slug = generateSlug($data['nama_jabatan']);

        $query = "UPDATE jabatan SET nama_jabatan = :nama_jabatan, slug_jabatan = :slug WHERE id_jabatan = :id";
        $this->db->query($query);
        $this->db->bind(':nama_jabatan', $data['nama_jabatan']);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':id', $id, PDO::PARAM_INT);

        return $this->db->execute();
    }

    /**
     * Hapus data jabatan
     * Hanya akan berhasil jika tidak ada pegawai yang memiliki id_jabatan ini
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        // Periksa jumlah anggota terlebih dahulu
        $this->db->query("SELECT COUNT(*) as total FROM pegawai WHERE id_jabatan = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->fetch();
        
        if ($result['total'] > 0) {
            return false;
        }

        $this->db->query("DELETE FROM jabatan WHERE id_jabatan = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}
