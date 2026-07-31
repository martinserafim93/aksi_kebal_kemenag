<?php
/**
 * AKSI KEBAL - Absensi Controller (Pegawai)
 * 
 * Controller untuk menangani pengisian absensi oleh pegawai di sisi frontend.
 */

class AbsensiController extends Controller
{
    /**
     * Halaman formulir absensi
     */
    public function index(): void
    {
        // Mendapatkan ID Kegiatan dari query string: ?kegiatan=1
        $id_kegiatan = query('kegiatan');
        
        if (!$id_kegiatan) {
            $this->notFound();
            return;
        }

        $kegiatanModel = $this->model('KegiatanModel');
        $kegiatan = $kegiatanModel->findById((int)$id_kegiatan);

        if (!$kegiatan || $kegiatan['status_kegiatan'] !== 'Published') {
            // Tampilkan error jika kegiatan tidak valid atau belum dipublish
            $this->view('errors/404', ['message' => 'Kegiatan tidak ditemukan atau belum aktif.']);
            return;
        }

        $pegawaiModel = $this->model('PegawaiModel');
        $pegawaiList = $pegawaiModel->getAllPaginated('', 1000, 0); // Ambil semua pegawai (asumsi < 1000)

        // Tampilkan halaman formulir absensi
        $this->view('pegawai/absensi/index', [
            'title' => 'Formulir Absensi - ' . $kegiatan['nama_kegiatan'],
            'kegiatan' => $kegiatan,
            'pegawaiList' => $pegawaiList
        ]);
    }

    /**
     * Endpoint untuk mendapatkan data pegawai via AJAX
     */
    public function getPegawaiData(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            return;
        }

        $nip = query('nip');
        if (!$nip) {
            http_response_code(400);
            echo json_encode(['error' => 'NIP required']);
            return;
        }

        $pegawaiModel = $this->model('PegawaiModel');
        $pegawai = $pegawaiModel->findDetailByNip($nip);

        if ($pegawai) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'nip' => $pegawai['nip'],
                    'nama_jabatan' => $pegawai['nama_jabatan'] ?? '-',
                    'nama_tim_kerja' => $pegawai['nama_tim_kerja'] ?? '-'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Pegawai tidak ditemukan']);
        }
    }

    /**
     * Memproses submit formulir absensi
     */
    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
            return;
        }

        $id_kegiatan = $_POST['id_kegiatan'] ?? '';
        $nip = $_POST['nip'] ?? '';
        
        if (empty($id_kegiatan) || empty($nip)) {
            setFlash('error', 'Data tidak lengkap.');
            $this->redirect('absensi?kegiatan=' . $id_kegiatan);
            return;
        }

        $absensiModel = $this->model('AbsensiModel');

        // Cek duplikasi
        if ($absensiModel->hasAbsensi($nip, (int)$id_kegiatan)) {
            setFlash('warning', 'Anda sudah melakukan absensi untuk kegiatan ini.');
            $this->redirect('absensi?kegiatan=' . $id_kegiatan);
            return;
        }

        // Handle upload foto
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['foto']['tmp_name'];
            $file_name = $_FILES['foto']['name'];
            $file_size = $_FILES['foto']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts = ['jpg', 'jpeg', 'png'];
            if (!in_array($file_ext, $allowed_exts)) {
                setFlash('error', 'Format foto tidak valid. Gunakan JPG/PNG.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }

            if ($file_size > 5 * 1024 * 1024) {
                setFlash('error', 'Ukuran foto maksimal 5MB.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }

            // Generate nama file unik
            $foto = 'absen_' . $nip . '_' . time() . '.' . $file_ext;
            $upload_dir = __DIR__ . '/../../public/uploads/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (!move_uploaded_file($tmp_name, $upload_dir . $foto)) {
                setFlash('error', 'Gagal mengupload foto.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }
        } else {
            setFlash('error', 'Foto wajib diupload.');
            $this->redirect('absensi?kegiatan=' . $id_kegiatan);
            return;
        }

        // Simpan ke database
        $data = [
            'nip' => $nip,
            'id_kegiatan' => (int)$id_kegiatan,
            'foto' => $foto,
            'status_kehadiran' => 'Hadir'
        ];

        $id_absensi = $absensiModel->create($data);

        if ($id_absensi) {
            // Redirect ke halaman sukses
            $this->redirect('absensi/sukses/' . $id_absensi);
        } else {
            setFlash('error', 'Terjadi kesalahan sistem saat menyimpan absensi.');
            $this->redirect('absensi?kegiatan=' . $id_kegiatan);
        }
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
