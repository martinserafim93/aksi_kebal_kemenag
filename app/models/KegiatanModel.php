<?php
/**
 * AKSI KEBAL - Kegiatan Model
 */

class KegiatanModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Ambil semua data kegiatan
     */
    public function getAll(string $search = '', string $status = '', string $jenis = ''): array
    {
        $query = "SELECT * FROM kegiatan WHERE 1=1";
        
        if (!empty($search)) {
            $query .= " AND nama_kegiatan LIKE :search";
        }
        if (!empty($status)) {
            $query .= " AND status_kegiatan = :status";
        }
        if (!empty($jenis)) {
            $query .= " AND jenis_kegiatan = :jenis";
        }
        
        $query .= " ORDER BY tanggal_kegiatan DESC, waktu_mulai DESC";
        
        $this->db->query($query);
        
        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        if (!empty($status)) {
            $this->db->bind(':status', $status);
        }
        if (!empty($jenis)) {
            $this->db->bind(':jenis', $jenis);
        }
        
        return $this->db->fetchAll();
    }

    public function findById(int $id)
    {
        $this->db->query("SELECT * FROM kegiatan WHERE id_kegiatan = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->fetch();
    }

    public function create(array $data): bool
    {
        $query = "INSERT INTO kegiatan (nama_kegiatan, jenis_kegiatan, tanggal_kegiatan, waktu_mulai, waktu_selesai, lokasi_kegiatan, deskripsi_kegiatan, status_kegiatan) 
                  VALUES (:nama, :jenis, :tanggal, :waktu_mulai, :waktu_selesai, :lokasi, :deskripsi, 'Draft')";
        
        $this->db->query($query);
        $this->db->bind(':nama', $data['nama_kegiatan']);
        $this->db->bind(':jenis', $data['jenis_kegiatan']);
        $this->db->bind(':tanggal', $data['tanggal_kegiatan']);
        $this->db->bind(':waktu_mulai', $data['waktu_mulai']);
        $this->db->bind(':waktu_selesai', $data['waktu_selesai']);
        $this->db->bind(':lokasi', $data['lokasi_kegiatan']);
        $this->db->bind(':deskripsi', $data['deskripsi_kegiatan']);
        
        return $this->db->execute();
    }

    public function update(int $id, array $data): bool
    {
        $query = "UPDATE kegiatan SET 
                    nama_kegiatan = :nama, 
                    jenis_kegiatan = :jenis, 
                    tanggal_kegiatan = :tanggal, 
                    waktu_mulai = :waktu_mulai, 
                    waktu_selesai = :waktu_selesai, 
                    lokasi_kegiatan = :lokasi, 
                    deskripsi_kegiatan = :deskripsi 
                  WHERE id_kegiatan = :id";
        
        $this->db->query($query);
        $this->db->bind(':nama', $data['nama_kegiatan']);
        $this->db->bind(':jenis', $data['jenis_kegiatan']);
        $this->db->bind(':tanggal', $data['tanggal_kegiatan']);
        $this->db->bind(':waktu_mulai', $data['waktu_mulai']);
        $this->db->bind(':waktu_selesai', $data['waktu_selesai']);
        $this->db->bind(':lokasi', $data['lokasi_kegiatan']);
        $this->db->bind(':deskripsi', $data['deskripsi_kegiatan']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function publish(int $id, string $qrCodeUrl): bool
    {
        $query = "UPDATE kegiatan SET status_kegiatan = 'Published', qr_code = :qr_code WHERE id_kegiatan = :id";
        $this->db->query($query);
        $this->db->bind(':qr_code', $qrCodeUrl);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function delete(int $id): bool
    {
        if ($this->checkAbsensiRelation($id)) {
            return false; // Ada absensi terikat
        }
        $this->db->query("DELETE FROM kegiatan WHERE id_kegiatan = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function checkAbsensiRelation(int $id): bool
    {
        $this->db->query("SELECT COUNT(*) as total FROM absensi WHERE id_kegiatan = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $result = $this->db->fetch();
        return $result['total'] > 0;
    }
}
