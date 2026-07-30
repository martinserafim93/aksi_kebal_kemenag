<?php
/**
 * AKSI KEBAL - Absensi Controller (Pegawai)
 * 
 * Controller untuk menangani pengisian absensi oleh pegawai di sisi frontend.
 */

class AbsensiController extends Controller
{
    /**
     * Halaman formulir absensi (Belum diimplementasi secara penuh, placeholder untuk Issue #12)
     */
    public function index(): void
    {
        // Mendapatkan ID Kegiatan dari query string: ?kegiatan=1
        $id_kegiatan = query('kegiatan');
        
        if (!$id_kegiatan) {
            $this->notFound();
            return;
        }

        // TODO: Implementasi Issue #12 di sini
        echo "Halaman formulir absensi untuk kegiatan ID: " . e($id_kegiatan) . " (Akan diimplementasikan pada Issue #12)";
    }

    /**
     * Halaman Sukses Absensi
     * Ditampilkan setelah pegawai berhasil melakukan absensi
     * 
     * @param int|null $id ID Absensi
     */
    public function sukses($id = null): void
    {
        if (!$id) {
            $this->redirect('');
            return;
        }

        // Gunakan AbsensiModel yang sudah dibuat (karena memiliki metode findById yang di-join dengan pegawai & kegiatan)
        $model = $this->model('AbsensiModel');
        $absensi = $model->findById((int)$id);

        if (!$absensi) {
            $this->notFound();
            return;
        }

        // Tampilkan halaman sukses
        $this->view('pegawai/absensi/sukses', [
            'title' => 'Absensi Berhasil',
            'absensi' => $absensi
        ]);
    }
}
