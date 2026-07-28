<?php
/**
 * AKSI KEBAL - Auth Model
 * 
 * Model untuk autentikasi admin.
 * Menangani query database terkait login dan verifikasi user.
 */

class AuthModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Cari admin berdasarkan Email atau NIP
     * Hanya mengembalikan user dengan role 'admin' yang memiliki password
     * 
     * @param string $identifier Email atau NIP
     * @return array|false Data admin atau false jika tidak ditemukan
     */
    public function findAdminByEmailOrNip(string $identifier)
    {
        $this->db->query(
            "SELECT p.nip, p.nama_lengkap, p.email, p.password, p.role,
                    j.nama_jabatan, t.nama_tim_kerja
             FROM pegawai p
             LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
             LEFT JOIN tim_kerja t ON p.id_tim_kerja = t.id_tim_kerja
             WHERE (p.email = :identifier OR p.nip = :identifier2)
               AND p.role = 'admin'
               AND p.password IS NOT NULL"
        );
        $this->db->bind(':identifier', $identifier);
        $this->db->bind(':identifier2', $identifier);

        return $this->db->fetch();
    }

    /**
     * Cari pegawai berdasarkan NIP
     * 
     * @param string $nip NIP pegawai
     * @return array|false Data pegawai atau false jika tidak ditemukan
     */
    public function findByNip(string $nip)
    {
        $this->db->query(
            "SELECT p.nip, p.nama_lengkap, p.email, p.role,
                    j.nama_jabatan, t.nama_tim_kerja
             FROM pegawai p
             LEFT JOIN jabatan j ON p.id_jabatan = j.id_jabatan
             LEFT JOIN tim_kerja t ON p.id_tim_kerja = t.id_tim_kerja
             WHERE p.nip = :nip"
        );
        $this->db->bind(':nip', $nip);

        return $this->db->fetch();
    }
}
