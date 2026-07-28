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
        $query = "SELECT t.id_tim_kerja, t.nama_tim_kerja, t.created_at, 
                         COUNT(p.nip) as jumlah_anggota 
                  FROM tim_kerja t
                  LEFT JOIN pegawai p ON t.id_tim_kerja = p.id_tim_kerja
                  GROUP BY t.id_tim_kerja, t.nama_tim_kerja, t.created_at
                  ORDER BY t.nama_tim_kerja ASC";
                  
        $this->db->query($query);
        return $this->db->fetchAll();
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
     * Cek apakah nama tim kerja sudah ada
     * 
     * @param string $nama_tim_kerja
     * @param int|null $excludeId ID untuk dikecualikan saat update
     * @return bool
     */
    public function isNameExists(string $nama_tim_kerja, ?int $excludeId = null): bool
    {
        $query = "SELECT COUNT(*) as total FROM tim_kerja WHERE nama_tim_kerja = :nama";
        if ($excludeId !== null) {
            $query .= " AND id_tim_kerja != :excludeId";
        }

        $this->db->query($query);
        $this->db->bind(':nama', $nama_tim_kerja);
        
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
        $query = "INSERT INTO tim_kerja (nama_tim_kerja) VALUES (:nama_tim_kerja)";
        $this->db->query($query);
        $this->db->bind(':nama_tim_kerja', $data['nama_tim_kerja']);

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
        $query = "UPDATE tim_kerja SET nama_tim_kerja = :nama_tim_kerja WHERE id_tim_kerja = :id";
        $this->db->query($query);
        $this->db->bind(':nama_tim_kerja', $data['nama_tim_kerja']);
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
