<?php
/**
 * AKSI KEBAL - Pegawai Model
 * 
 * Model untuk manajemen data pegawai (CRUD).
 */

class PegawaiModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Mengambil daftar pegawai dengan pagination dan pencarian
     */
    public function getAllPaginated(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $query = "SELECT p.*, j.nama_jabatan, t.nama_tim_kerja, u.nama_unit_kerja 
                  FROM pegawai p
                  LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
                  LEFT JOIN tim_kerja t ON p.id_tim_kerja = t.id_tim_kerja
                  LEFT JOIN unit_kerja u ON p.id_unit_kerja = u.id_unit_kerja";
        
        if (!empty($search)) {
            $query .= " WHERE p.nip LIKE :search_nip OR p.nama_lengkap LIKE :search_nama";
        }
        
        $query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $this->db->query($query);

        if (!empty($search)) {
            $this->db->bind(':search_nip', "%$search%");
            $this->db->bind(':search_nama', "%$search%");
        }
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);

        return $this->db->fetchAll();
    }

    /**
     * Menghitung total data pegawai (untuk pagination)
     */
    public function countAll(string $search = ''): int
    {
        $query = "SELECT COUNT(*) as total FROM pegawai";
        
        if (!empty($search)) {
            $query .= " WHERE nip LIKE :search_nip OR nama_lengkap LIKE :search_nama";
        }

        $this->db->query($query);

        if (!empty($search)) {
            $this->db->bind(':search_nip', "%$search%");
            $this->db->bind(':search_nama', "%$search%");
        }

        $result = $this->db->fetch();
        return $result ? (int) $result['total'] : 0;
    }

    /**
     * Mengambil data satu pegawai berdasarkan NIP
     */
    public function findByNip(string $nip)
    {
        $this->db->query("SELECT * FROM pegawai WHERE nip = :nip");
        $this->db->bind(':nip', $nip);
        return $this->db->fetch();
    }

    /**
     * Mengambil data satu pegawai berdasarkan nama jabatan
     */
    public function findByJabatanName(string $namaJabatan)
    {
        $this->db->query("SELECT p.*, j.nama_jabatan FROM pegawai p
                          LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
                          WHERE j.nama_jabatan = :nama_jabatan LIMIT 1");
        $this->db->bind(':nama_jabatan', $namaJabatan);
        return $this->db->fetch();
    }

    /**
     * Mengambil data satu pegawai beserta nama jabatan dan tim kerja berdasarkan NIP
     */
    public function findDetailByNip(string $nip)
    {
        $this->db->query("SELECT p.*, j.nama_jabatan, t.nama_tim_kerja, u.nama_unit_kerja 
                          FROM pegawai p
                          LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
                          LEFT JOIN tim_kerja t ON p.id_tim_kerja = t.id_tim_kerja
                          LEFT JOIN unit_kerja u ON p.id_unit_kerja = u.id_unit_kerja
                          WHERE p.nip = :nip");
        $this->db->bind(':nip', $nip);
        return $this->db->fetch();
    }

    /**
     * Cek apakah NIP sudah ada (kecuali untuk NIP tertentu saat edit)
     */
    public function isNipExists(string $nip, string $excludeNip = null): bool
    {
        $query = "SELECT nip FROM pegawai WHERE nip = :nip";
        if ($excludeNip) {
            $query .= " AND nip != :exclude_nip";
        }
        
        $this->db->query($query);
        $this->db->bind(':nip', $nip);
        if ($excludeNip) {
            $this->db->bind(':exclude_nip', $excludeNip);
        }

        return $this->db->fetch() !== false;
    }

    /**
     * Menambahkan pegawai baru
     */
    public function create(array $data): bool
    {
        $this->db->query("INSERT INTO pegawai (nip, nama_lengkap, id_jabatan, id_tim_kerja, id_unit_kerja, email, password, role) 
                          VALUES (:nip, :nama_lengkap, :id_jabatan, :id_tim_kerja, :id_unit_kerja, :email, :password, :role)");
        
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':id_jabatan', $data['id_jabatan'] ?: null);
        $this->db->bind(':id_tim_kerja', $data['id_tim_kerja'] ?: null);
        $this->db->bind(':id_unit_kerja', $data['id_unit_kerja'] ?: null);
        $this->db->bind(':email', $data['email'] ?: null);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role'] ?? 'pegawai');

        return $this->db->execute();
    }

    /**
     * Mengubah data pegawai
     */
    public function update(string $nip_lama, array $data): bool
    {
        $query = "UPDATE pegawai SET 
                    nip = :nip, 
                    nama_lengkap = :nama_lengkap, 
                    id_jabatan = :id_jabatan, 
                    id_tim_kerja = :id_tim_kerja, 
                    id_unit_kerja = :id_unit_kerja, 
                    email = :email,
                    role = :role";
        
        // Update password hanya jika diisi
        if (!empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE nip = :nip_lama";

        $this->db->query($query);
        
        $this->db->bind(':nip', $data['nip']);
        $this->db->bind(':nama_lengkap', $data['nama_lengkap']);
        $this->db->bind(':id_jabatan', $data['id_jabatan'] ?: null);
        $this->db->bind(':id_tim_kerja', $data['id_tim_kerja'] ?: null);
        $this->db->bind(':id_unit_kerja', $data['id_unit_kerja'] ?: null);
        $this->db->bind(':email', $data['email'] ?: null);
        $this->db->bind(':role', $data['role'] ?? 'pegawai');
        $this->db->bind(':nip_lama', $nip_lama);
        
        if (!empty($data['password'])) {
            $this->db->bind(':password', $data['password']);
        }

        return $this->db->execute();
    }

    /**
     * Menghapus pegawai
     */
    public function delete(string $nip): bool
    {
        $this->db->query("DELETE FROM pegawai WHERE nip = :nip");
        $this->db->bind(':nip', $nip);
        return $this->db->execute();
    }

    /**
     * Mengambil daftar seluruh jabatan untuk opsi select
     */
    public function getAllJabatan(): array
    {
        $this->db->query("SELECT * FROM jabatan ORDER BY nama_jabatan ASC");
        return $this->db->fetchAll();
    }

    /**
     * Mengambil daftar seluruh tim kerja untuk opsi select
     */
    public function getAllTimKerja(): array
    {
        $this->db->query("SELECT * FROM tim_kerja ORDER BY nama_tim_kerja ASC");
        return $this->db->fetchAll();
    }

    /**
     * Mengambil daftar seluruh unit kerja untuk opsi select
     */
    public function getAllUnitKerja(): array
    {
        $this->db->query("SELECT * FROM unit_kerja ORDER BY nama_unit_kerja ASC");
        return $this->db->fetchAll();
    }

    /**
     * Mengambil daftar pegawai untuk dropdown (hanya field tertentu)
     */
    public function getListForDropdown(): array
    {
        $this->db->query("SELECT nip, nama_lengkap FROM pegawai ORDER BY nama_lengkap ASC");
        return $this->db->fetchAll();
    }
}
