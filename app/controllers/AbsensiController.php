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
        // Mendapatkan identifier Kegiatan dari query string
        $kegiatan_identifier = query('kegiatan');
        
        if (!$kegiatan_identifier) {
            $this->notFound();
            return;
        }

        $kegiatanModel = $this->model('KegiatanModel');
        
        // Backward compatibility: Cek apakah input berupa ID angka atau slug
        if (ctype_digit($kegiatan_identifier)) {
            $kegiatan = $kegiatanModel->findById((int)$kegiatan_identifier);
        } else {
            $kegiatan = $kegiatanModel->findByKode($kegiatan_identifier);
        }

        if (!$kegiatan || $kegiatan['status_kegiatan'] !== 'Published') {
            // Tampilkan error jika kegiatan tidak valid atau belum dipublish
            $this->view('errors/404', ['message' => 'Kegiatan tidak ditemukan atau belum aktif.']);
            return;
        }

        $pegawaiModel = $this->model('PegawaiModel');
        $pegawaiList = $pegawaiModel->getListForDropdown(); // Optimasi: ambil field minimal

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
            $redirect_kegiatan = $_POST['kode_kegiatan'] ?? ($_POST['id_kegiatan'] ?? '');
            $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
            return;
        }

        $status_kehadiran = $_POST['status_kehadiran'] ?? '';

        // Validasi status_kehadiran
        if (!in_array($status_kehadiran, ['Hadir', 'Tidak Hadir'])) {
            setFlash('error', 'Status kehadiran tidak valid.');
            $redirect_kegiatan = $_POST['kode_kegiatan'] ?? ($_POST['id_kegiatan'] ?? '');
            $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
            return;
        }

        $id_kegiatan = $_POST['id_kegiatan'] ?? '';
        $nip = $_POST['nip'] ?? '';
        $redirect_kegiatan = $_POST['kode_kegiatan'] ?? $id_kegiatan;

        if (empty($id_kegiatan) || empty($nip)) {
            setFlash('error', 'Data tidak lengkap.');
            $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
            return;
        }

        $kegiatanModel = $this->model('KegiatanModel');
        $kegiatan = $kegiatanModel->findById((int)$id_kegiatan);

        if (!$kegiatan || $kegiatan['status_kegiatan'] !== 'Published') {
            setFlash('error', 'Kegiatan tidak ditemukan atau belum aktif.');
            $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
            return;
        }

        $absensiModel = $this->model('AbsensiModel');

        // Cek duplikasi
        if ($absensiModel->hasAbsensi($nip, (int)$id_kegiatan)) {
            setFlash('warning', 'Anda sudah melakukan absensi untuk kegiatan ini.');
            $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
            return;
        }

        // ===== VALIDASI LOKASI GPS (HANYA UNTUK HADIR) =====
        $latitude_absensi  = $_POST['latitude_absensi'] ?? null;
        $longitude_absensi = $_POST['longitude_absensi'] ?? null;
        $lokasi_valid      = null;
        $jarak_meter       = null;

        if ($status_kehadiran === 'Hadir') {
            // Jika kegiatan punya koordinat lokasi, validasi jarak
            if ($kegiatan && !empty($kegiatan['latitude_kegiatan']) && !empty($kegiatan['longitude_kegiatan'])) {
                
                // Pastikan pegawai mengirim koordinat
                if (empty($latitude_absensi) || empty($longitude_absensi)) {
                    setFlash('error', 'Lokasi GPS Anda tidak terdeteksi. Aktifkan GPS dan coba lagi.');
                    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
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
                    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
                    return;
                }
            }
        }

        // Handle upload file berdasarkan status kehadiran
        $foto = '';
        $file_bukti = '';
        $tipe_file_bukti = null;

        if ($status_kehadiran === 'Hadir') {
            // ========== UPLOAD FOTO (untuk Hadir) ==========
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['foto']['tmp_name'];
                $file_name = $_FILES['foto']['name'];
                $file_size = $_FILES['foto']['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                $allowed_exts = ['jpg', 'jpeg', 'png'];
                
                // Validasi MIME type sebenarnya (mencegah ekstensi palsu)
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($tmp_name);
                $allowed_mimes = ['image/jpeg', 'image/png'];

                if (!in_array($file_ext, $allowed_exts) || !in_array($mime_type, $allowed_mimes)) {
                    setFlash('error', 'Format file tidak diizinkan. Hanya JPG/PNG.');
                    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
                    return;
                }

                if ($file_size > 5 * 1024 * 1024) {
                    setFlash('error', 'Ukuran foto maksimal 5MB.');
                    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
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
                    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
                    return;
                }
            }
            
            if (empty($foto)) {
                setFlash('error', 'Foto wajib diunggah untuk status Hadir.');
                $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
                return;
            }
        } else {
            // ========== UPLOAD FILE BUKTI (untuk Tidak Hadir) ==========
            if (isset($_FILES['file_bukti']) && $_FILES['file_bukti']['error'] === UPLOAD_ERR_OK) {
                $result = $this->prosesFileBukti($_FILES['file_bukti'], $nip, (string)$id_kegiatan);
                
                if ($result['success']) {
                    $file_bukti = $result['filename'];
                    $tipe_file_bukti = $result['tipe'];
                } else {
                    setFlash('error', $result['error']);
                    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
                    return;
                }
            } else {
                setFlash('error', 'File bukti ketidakhadiran wajib diunggah.');
                $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
                return;
            }
        }

        // Simpan ke database
        $data = [
            'nip'               => $nip,
            'id_kegiatan'       => (int)$id_kegiatan,
            'foto'              => $foto ?: null,
            'file_bukti'        => $file_bukti ?: null,
            'tipe_file_bukti'   => $tipe_file_bukti,
            'status_kehadiran'  => $status_kehadiran,
            'latitude_absensi'  => ($status_kehadiran === 'Hadir') ? $latitude_absensi : null,
            'longitude_absensi' => ($status_kehadiran === 'Hadir') ? $longitude_absensi : null,
            'jarak_meter'       => ($status_kehadiran === 'Hadir') ? $jarak_meter : null,
            'lokasi_valid'      => ($status_kehadiran === 'Hadir') ? $lokasi_valid : null
        ];

        $insertId = $absensiModel->create($data);

        if ($insertId) {
            // Redirect ke halaman sukses
            $this->redirect('absensi/sukses/' . $insertId);
        } else {
            setFlash('error', 'Gagal menyimpan absensi.');
            $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
        }
    }

    /**
     * Halaman Sukses Absensi
     * Ditampilkan setelah pegawai berhasil melakukan absensi
     * 
     * @param int|null $id ID Absensi
     */
    public function sukses($identifier = null): void
    {
        if (!$identifier) {
            $this->redirect('');
            return;
        }

        // Gunakan AbsensiModel yang sudah dibuat (karena memiliki metode findById yang di-join dengan pegawai & kegiatan)
        $model = $this->model('AbsensiModel');
        
        if (ctype_digit($identifier)) {
            $absensi = $model->findById((int)$identifier);
        } else {
            $absensi = $model->findByKodeAbsensi($identifier);
        }

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

    /**
     * Proses upload dan validasi file bukti ketidakhadiran
     * Mendukung gambar (JPG/PNG) dan PDF
     * Gambar dikompresi < 1.5 MB, PDF divalidasi keamanannya
     * 
     * @param array $file Data $_FILES['file_bukti']
     * @param string $nip NIP pegawai
     * @param string $id_kegiatan ID kegiatan
     * @return array ['success' => bool, 'filename' => string, 'tipe' => string, 'error' => string]
     */
    private function prosesFileBukti(array $file, string $nip, string $id_kegiatan): array
    {
        $tmp_name = $file['tmp_name'];
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // 1. Validasi ekstensi
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($file_ext, $allowed_exts)) {
            return ['success' => false, 'error' => 'Format file tidak diizinkan. Hanya JPG, PNG, atau PDF.'];
        }
        
        // 2. Validasi MIME type sebenarnya (mencegah ekstensi palsu)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($tmp_name);
        $allowed_mimes = [
            'image/jpeg',
            'image/png',
            'application/pdf'
        ];
        
        if (!in_array($mime_type, $allowed_mimes)) {
            return ['success' => false, 'error' => 'Tipe file tidak valid. File terdeteksi sebagai: ' . $mime_type];
        }
        
        // 3. Cross-check: Ekstensi harus cocok dengan MIME type
        $mime_ext_map = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png'  => ['png'],
            'application/pdf' => ['pdf']
        ];
        if (!isset($mime_ext_map[$mime_type]) || !in_array($file_ext, $mime_ext_map[$mime_type])) {
            return ['success' => false, 'error' => 'Ekstensi file tidak cocok dengan isi file. Kemungkinan file telah dimanipulasi.'];
        }
        
        // 4. Validasi ukuran (max 5 MB)
        if ($file_size > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Ukuran file maksimal 5MB.'];
        }
        
        // 5. SECURITY CHECK: Scan konten file untuk script jahat
        $security_check = $this->scanFileSecurity($tmp_name, $mime_type);
        if (!$security_check['safe']) {
            return ['success' => false, 'error' => 'File ditolak karena alasan keamanan: ' . $security_check['reason']];
        }
        
        // 6. Generate nama file unik
        $timestamp = time();
        $upload_dir = __DIR__ . '/../../public/uploads/file_bukti/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // 7. Proses berdasarkan tipe file
        if (in_array($mime_type, ['image/jpeg', 'image/png'])) {
            // === GAMBAR: Kompresi ke < 1.5 MB ===
            $output_filename = "bukti_{$nip}_{$id_kegiatan}_{$timestamp}.jpg";
            $target_file = $upload_dir . $output_filename;
            
            try {
                if ($file_ext === 'png') {
                    $source_image = @imagecreatefrompng($tmp_name);
                } else {
                    $source_image = @imagecreatefromjpeg($tmp_name);
                }
                
                if (!$source_image) {
                    return ['success' => false, 'error' => 'Gagal membaca file gambar.'];
                }
                
                // Fix Orientation (EXIF)
                if (function_exists('exif_read_data') && in_array($file_ext, ['jpg', 'jpeg'])) {
                    $exif = @exif_read_data($tmp_name);
                    if ($exif && isset($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3: $source_image = imagerotate($source_image, 180, 0); break;
                            case 6: $source_image = imagerotate($source_image, -90, 0); break;
                            case 8: $source_image = imagerotate($source_image, 90, 0); break;
                        }
                    }
                }
                
                // Resize max 1600px (lebih kecil dari foto hadir, karena ini hanya bukti)
                $width = imagesx($source_image);
                $height = imagesy($source_image);
                $max_dim = 1600;
                
                if ($width > $max_dim || $height > $max_dim) {
                    if ($width > $height) {
                        $new_width = $max_dim;
                        $new_height = (int)($height * ($max_dim / $width));
                    } else {
                        $new_height = $max_dim;
                        $new_width = (int)($width * ($max_dim / $height));
                    }
                    
                    $resized = imagecreatetruecolor($new_width, $new_height);
                    $white = imagecolorallocate($resized, 255, 255, 255);
                    imagefill($resized, 0, 0, $white);
                    imagecopyresampled($resized, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagedestroy($source_image);
                    $source_image = $resized;
                } else if ($file_ext === 'png') {
                    $bg = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefill($bg, 0, 0, $white);
                    imagecopy($bg, $source_image, 0, 0, 0, 0, $width, $height);
                    imagedestroy($source_image);
                    $source_image = $bg;
                }
                
                // Simpan dengan quality 70 agar ukuran < 1.5 MB
                if (!imagejpeg($source_image, $target_file, 70)) {
                    return ['success' => false, 'error' => 'Gagal menyimpan gambar hasil kompresi.'];
                }
                
                imagedestroy($source_image);
                
                // Verifikasi ukuran hasil kompresi
                $final_size = filesize($target_file);
                if ($final_size > 1.5 * 1024 * 1024) {
                    // Jika masih > 1.5 MB, re-kompresi dengan quality lebih rendah
                    $source_image = imagecreatefromjpeg($target_file);
                    imagejpeg($source_image, $target_file, 50);
                    imagedestroy($source_image);
                }
                
                return ['success' => true, 'filename' => $output_filename, 'tipe' => 'image'];
                
            } catch (Exception $e) {
                return ['success' => false, 'error' => 'Kompresi gambar gagal: ' . $e->getMessage()];
            }
            
        } else {
            // === PDF: Validasi dan kompresi sederhana ===
            $output_filename = "bukti_{$nip}_{$id_kegiatan}_{$timestamp}.pdf";
            $target_file = $upload_dir . $output_filename;
            
            // Pindahkan file ke folder tujuan
            if (!move_uploaded_file($tmp_name, $target_file)) {
                return ['success' => false, 'error' => 'Gagal menyimpan file PDF.'];
            }
            
            // Cek apakah ukuran > 1.5 MB
            $final_size = filesize($target_file);
            if ($final_size > 1.5 * 1024 * 1024) {
                // Coba kompresi dengan Ghostscript (jika tersedia)
                $this->compressPdfWithGhostscript($target_file);
            }
            
            return ['success' => true, 'filename' => $output_filename, 'tipe' => 'pdf'];
        }
    }

    /**
     * Kompresi PDF menggunakan Ghostscript (opsional)
     * 
     * @param string $filepath Path lengkap ke file PDF
     * @return bool True jika berhasil dikompresi
     */
    private function compressPdfWithGhostscript(string $filepath): bool
    {
        // Cek apakah Ghostscript tersedia
        $gs_command = (PHP_OS_FAMILY === 'Windows') ? 'gswin64c' : 'gs';
        exec("which {$gs_command} 2>/dev/null", $output, $return_var);
        
        // Untuk Windows, gunakan 'where' sebagai pengganti 'which'
        if (PHP_OS_FAMILY === 'Windows') {
            exec("where {$gs_command} 2>nul", $output, $return_var);
        }
        
        if ($return_var !== 0) {
            return false; // Ghostscript tidak tersedia
        }
        
        $temp_output = $filepath . '.compressed.pdf';
        
        $command = sprintf(
            '%s -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s 2>&1',
            escapeshellcmd($gs_command),
            escapeshellarg($temp_output),
            escapeshellarg($filepath)
        );
        
        exec($command, $output, $return_var);
        
        if ($return_var === 0 && file_exists($temp_output)) {
            unlink($filepath);
            rename($temp_output, $filepath);
            return true;
        }
        
        if (file_exists($temp_output)) {
            unlink($temp_output);
        }
        
        return false;
    }

    /**
     * Scan file untuk deteksi konten berbahaya
     * 
     * @param string $filepath Path ke file temporary
     * @param string $mime_type MIME type yang terdeteksi
     * @return array ['safe' => bool, 'reason' => string]
     */
    private function scanFileSecurity(string $filepath, string $mime_type): array
    {
        // 1. CEK MAGIC BYTES (File Signature)
        $handle = fopen($filepath, 'rb');
        if (!$handle) {
            return ['safe' => false, 'reason' => 'Tidak bisa membaca file.'];
        }
        
        $header = fread($handle, 16); // Baca 16 byte pertama
        fclose($handle);
        
        $hex = strtoupper(bin2hex(substr($header, 0, 8)));
        
        $valid_signatures = false;
        
        if ($mime_type === 'image/jpeg') {
            $valid_signatures = (substr($hex, 0, 6) === 'FFD8FF');
        } elseif ($mime_type === 'image/png') {
            $valid_signatures = (substr($hex, 0, 16) === '89504E470D0A1A0A');
        } elseif ($mime_type === 'application/pdf') {
            $valid_signatures = (substr($hex, 0, 8) === '25504446'); // %PDF
        }
        
        if (!$valid_signatures) {
            return ['safe' => false, 'reason' => 'Header file tidak valid. File mungkin telah dimanipulasi.'];
        }
        
        // 2. CEK KONTEN BERBAHAYA DALAM FILE
        $content = file_get_contents($filepath);
        
        $dangerous_patterns = [
            '/<\?php/i',                    // PHP opening tag
            '/<\?=/i',                      // PHP short echo tag
            '/<script[\s>]/i',              // JavaScript tag
            '/eval\s*\(/i',                 // eval() call
            '/base64_decode\s*\(/i',        // base64_decode (sering dipakai malware)
            '/exec\s*\(/i',                 // exec() call
            '/system\s*\(/i',              // system() call
            '/passthru\s*\(/i',            // passthru() call
            '/shell_exec\s*\(/i',          // shell_exec() call
            '/\bproc_open\b/i',            // proc_open
            '/__HALT_COMPILER/i',          // PHP phar trick
        ];
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return ['safe' => false, 'reason' => 'File mengandung kode berbahaya yang terdeteksi.'];
            }
        }
        
        // 3. CEK NAMA FILE ASLI (Double Extension Attack)
        $original_name = $_FILES['file_bukti']['name'] ?? ($_FILES['foto']['name'] ?? '');
        $name_parts = explode('.', $original_name);
        if (count($name_parts) > 2) {
            $dangerous_exts = ['php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'sh', 'cgi', 'pl', 'py', 'asp', 'aspx', 'jsp', 'exe', 'bat', 'cmd'];
            foreach ($name_parts as $part) {
                if (in_array(strtolower($part), $dangerous_exts)) {
                    return ['safe' => false, 'reason' => 'Nama file mengandung ekstensi berbahaya (kemungkinan double extension attack).'];
                }
            }
        }
        
        // 4. OPSIONAL: ClamAV Scan
        if (function_exists('exec')) {
            exec('which clamscan 2>/dev/null', $output, $return_var);
            if ($return_var === 0) {
                exec('clamscan --no-summary ' . escapeshellarg($filepath), $scan_output, $scan_result);
                if ($scan_result === 1) {
                    return ['safe' => false, 'reason' => 'File terdeteksi mengandung virus oleh antivirus server.'];
                }
            }
        }
        
        return ['safe' => true, 'reason' => ''];
    }
}
