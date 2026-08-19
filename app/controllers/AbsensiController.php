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

        // Validasi CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
            setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
            $this->redirect('absensi?kegiatan=' . ($_POST['id_kegiatan'] ?? ''));
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

        // ===== VALIDASI LOKASI GPS =====
        $latitude_absensi  = $_POST['latitude_absensi'] ?? null;
        $longitude_absensi = $_POST['longitude_absensi'] ?? null;
        $lokasi_valid      = null;
        $jarak_meter       = null;

        // Ambil data kegiatan untuk cek koordinat
        $kegiatanModel = $this->model('KegiatanModel');
        $kegiatan = $kegiatanModel->findById((int)$id_kegiatan);

        // Jika kegiatan punya koordinat lokasi, validasi jarak
        if ($kegiatan && !empty($kegiatan['latitude_kegiatan']) && !empty($kegiatan['longitude_kegiatan'])) {
            
            // Pastikan pegawai mengirim koordinat
            if (empty($latitude_absensi) || empty($longitude_absensi)) {
                setFlash('error', 'Lokasi GPS Anda tidak terdeteksi. Aktifkan GPS dan coba lagi.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }

            // Hitung jarak di server (Haversine Formula)
            $jarak_meter = $this->hitungJarak(
                (float)$latitude_absensi,
                (float)$longitude_absensi,
                (float)$kegiatan['latitude_kegiatan'],
                (float)$kegiatan['longitude_kegiatan']
            );

            $radius = (int)($kegiatan['radius_meter'] ?? 50);
            $lokasi_valid = ($jarak_meter <= $radius) ? 1 : 0;

            // Tolak jika di luar radius
            if (!$lokasi_valid) {
                setFlash('error', 'Lokasi Anda berada ' . round($jarak_meter, 1) . ' meter dari lokasi kegiatan. Maksimal radius: ' . $radius . ' meter. Silakan pindah ke lokasi kegiatan dan coba absensi ulang.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }
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

            // Validasi MIME type sebenarnya (mencegah ekstensi palsu)
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($tmp_name);
            $allowed_mimes = ['image/jpeg', 'image/png'];
            if (!in_array($mime_type, $allowed_mimes)) {
                setFlash('error', 'File bukan gambar yang valid.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }

            if ($file_size > 5 * 1024 * 1024) {
                setFlash('error', 'Ukuran foto maksimal 5MB.');
                $this->redirect('absensi?kegiatan=' . $id_kegiatan);
                return;
            }

            // Generate nama file unik
            $timestamp = time();
            $foto = "{$nip}_{$id_kegiatan}_{$timestamp}.jpg";
            $upload_dir = __DIR__ . '/../../public/uploads/foto_absensi/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $target_file = $upload_dir . $foto;

            // Proses Kompresi GD
            try {
                if ($file_ext === 'png') {
                    $source_image = @imagecreatefrompng($tmp_name);
                } else {
                    $source_image = @imagecreatefromjpeg($tmp_name);
                }

                if (!$source_image) {
                    throw new Exception('Gagal membaca file gambar.');
                }

                // Fix Orientation
                if (function_exists('exif_read_data')) {
                    $exif = @exif_read_data($tmp_name);
                    if ($exif && isset($exif['Orientation'])) {
                        $orientation = $exif['Orientation'];
                        switch ($orientation) {
                            case 3:
                                $source_image = imagerotate($source_image, 180, 0);
                                break;
                            case 6:
                                $source_image = imagerotate($source_image, -90, 0);
                                break;
                            case 8:
                                $source_image = imagerotate($source_image, 90, 0);
                                break;
                        }
                    }
                }

                // Resize max 1920px
                $width = imagesx($source_image);
                $height = imagesy($source_image);
                $max_dim = 1920;
                
                if ($width > $max_dim || $height > $max_dim) {
                    if ($width > $height) {
                        $new_width = $max_dim;
                        $new_height = (int)($height * ($max_dim / $width));
                    } else {
                        $new_height = $max_dim;
                        $new_width = (int)($width * ($max_dim / $height));
                    }

                    $resized_image = imagecreatetruecolor($new_width, $new_height);
                    
                    // Beri background putih untuk PNG transparan sebelum convert ke JPG
                    $white = imagecolorallocate($resized_image, 255, 255, 255);
                    imagefill($resized_image, 0, 0, $white);

                    imagecopyresampled($resized_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagedestroy($source_image);
                    $source_image = $resized_image;
                } else {
                    // Beri background putih jika PNG punya transparansi dan ukuran < 1920
                    if ($file_ext === 'png') {
                        $bg_image = imagecreatetruecolor($width, $height);
                        $white = imagecolorallocate($bg_image, 255, 255, 255);
                        imagefill($bg_image, 0, 0, $white);
                        imagecopy($bg_image, $source_image, 0, 0, 0, 0, $width, $height);
                        imagedestroy($source_image);
                        $source_image = $bg_image;
                    }
                }

                // Simpan sbg JPEG dengan quality 75 agar ukuran < 1MB
                if (!imagejpeg($source_image, $target_file, 75)) {
                    throw new Exception('Gagal menyimpan file gambar hasil kompresi.');
                }
                
                imagedestroy($source_image);

            } catch (Exception $e) {
                setFlash('error', 'Kompresi foto gagal: ' . $e->getMessage());
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
            'nip'               => $nip,
            'id_kegiatan'       => (int)$id_kegiatan,
            'foto'              => $foto,
            'status_kehadiran'  => 'Hadir',
            'latitude_absensi'  => $latitude_absensi,
            'longitude_absensi' => $longitude_absensi,
            'jarak_meter'       => $jarak_meter,
            'lokasi_valid'      => $lokasi_valid
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

    /**
     * Hitung jarak antara 2 titik koordinat menggunakan Haversine Formula
     * 
     * @param float $lat1 Latitude titik 1
     * @param float $lng1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lng2 Longitude titik 2
     * @return float Jarak dalam meter
     */
    private function hitungJarak(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
