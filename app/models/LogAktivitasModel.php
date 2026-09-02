<?php
/**
 * AKSI KEBAL - Log Aktivitas Model (File-based, Daily Rotation)
 *
 * Model untuk audit trail: mencatat & menampilkan aktivitas admin dan
 * pengisian absensi pegawai. Insert bersifat "best-effort" sehingga 
 * kegagalan pencatatan tidak menggagalkan aksi utama.
 */

class LogAktivitasModel
{
    private $logDir;

    public function __construct()
    {
        $this->logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0777, true);
        }
        
        // Bersihkan log yang usianya lebih dari 30 hari (dijalankan sesekali)
        $this->cleanupOldLogs(30);
    }

    /**
     * Simpan satu baris log aktivitas ke file hari ini.
     *
     * @param array $data Kunci: aktor_nip, aktor_nama, aksi, modul, deskripsi, ip_address, user_agent
     */
    public function catat(array $data): bool
    {
        $date = date('Y-m-d');
        $logFile = $this->logDir . '/audit-' . $date . '.log';
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $json = json_encode($data);
        if ($json === false) {
            return false;
        }

        // Best effort file append
        $result = @file_put_contents($logFile, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        return $result !== false;
    }

    /**
     * Ambil log dengan filter + pagination.
     */
    public function getAllPaginated(array $filters = [], int $limit = 15, int $offset = 0): array
    {
        $logs = $this->getFilteredLogs($filters);
        return array_slice($logs, $offset, $limit);
    }

    /**
     * Hitung total log sesuai filter (untuk pagination).
     */
    public function countAll(array $filters = []): int
    {
        return count($this->getFilteredLogs($filters));
    }

    /**
     * Daftar modul unik untuk opsi filter dropdown.
     */
    public function getModulList(): array
    {
        return ['auth', 'pegawai', 'tim_kerja', 'unit_kerja', 'jabatan', 'kegiatan', 'absensi'];
    }

    /**
     * Daftar aksi unik untuk opsi filter dropdown.
     */
    public function getAksiList(): array
    {
        return ['login', 'login_gagal', 'logout', 'tambah', 'ubah', 'hapus', 'publish', 'ekspor', 'absensi'];
    }

    /**
     * Helper: Membaca log dari file dan mengembalikan array data yang sudah di-filter
     * dan diurutkan secara descending (terbaru di atas).
     */
    private function getFilteredLogs(array $filters): array
    {
        $filesToRead = [];
        
        if (!empty($filters['tanggal'])) {
            $specificFile = $this->logDir . '/audit-' . $filters['tanggal'] . '.log';
            if (file_exists($specificFile)) {
                $filesToRead[] = $specificFile;
            }
        } else {
            $files = glob($this->logDir . '/audit-*.log');
            if ($files) {
                rsort($files); // Urutkan file descending (Z-A, tanggal terbaru di atas)
                $filesToRead = $files;
            }
        }

        $filtered = [];
        $search = !empty($filters['search']) ? strtolower($filters['search']) : null;
        $aksi = !empty($filters['aksi']) ? $filters['aksi'] : null;
        $modul = !empty($filters['modul']) ? $filters['modul'] : null;

        foreach ($filesToRead as $file) {
            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (!$lines) continue;
            
            // Karena baris ditambahkan ke akhir file, baris terakhir adalah log terbaru
            $lines = array_reverse($lines);
            
            foreach ($lines as $line) {
                $item = json_decode($line, true);
                if (!$item) continue;
                
                // Filter Aksi
                if ($aksi && (!isset($item['aksi']) || $item['aksi'] !== $aksi)) {
                    continue;
                }
                
                // Filter Modul
                if ($modul && (!isset($item['modul']) || $item['modul'] !== $modul)) {
                    continue;
                }
                
                // Filter Search
                if ($search) {
                    $deskripsi = isset($item['deskripsi']) ? strtolower($item['deskripsi']) : '';
                    $aktorNama = isset($item['aktor_nama']) ? strtolower($item['aktor_nama']) : '';
                    $aktorNip = isset($item['aktor_nip']) ? strtolower($item['aktor_nip']) : '';
                    
                    if (strpos($deskripsi, $search) === false && 
                        strpos($aktorNama, $search) === false && 
                        strpos($aktorNip, $search) === false) {
                        continue;
                    }
                }
                
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /**
     * Hapus file log yang lebih tua dari batas hari yang ditentukan.
     * Fungsi ini dijalankan dengan probabilitas 10% setiap kali model diinisiasi
     * agar tidak memberatkan I/O server.
     */
    private function cleanupOldLogs(int $daysToKeep = 30): void
    {
        // Peluang 10% untuk dieksekusi
        if (mt_rand(1, 100) > 10) {
            return;
        }

        $files = glob($this->logDir . '/audit-*.log');
        if (!$files) return;

        $threshold = strtotime("-{$daysToKeep} days");

        foreach ($files as $file) {
            $basename = basename($file, '.log');
            $dateStr = str_replace('audit-', '', $basename);
            $fileTime = strtotime($dateStr);
            
            if ($fileTime && $fileTime < $threshold) {
                @unlink($file);
            }
        }
    }
}
