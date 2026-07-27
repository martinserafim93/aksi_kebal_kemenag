<?php
/**
 * AKSI KEBAL - Helper Functions
 * 
 * Kumpulan fungsi bantuan yang digunakan di seluruh aplikasi.
 */

/**
 * Escape output untuk mencegah XSS
 * 
 * @param string|null $string String yang akan di-escape
 * @return string String yang sudah di-escape
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect ke URL
 * 
 * @param string $path Path relatif dari BASE_URL
 */
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Set flash message ke session
 * 
 * @param string $type Tipe pesan (success, error, warning, info)
 * @param string $message Isi pesan
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Ambil dan hapus flash message dari session
 * 
 * @return array|null Flash message atau null jika tidak ada
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Cek apakah request method adalah POST
 */
function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Cek apakah request method adalah GET
 */
function isGet(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Ambil input dari POST dengan sanitasi
 * 
 * @param string $key Nama field
 * @param mixed $default Nilai default jika tidak ada
 * @return mixed
 */
function input(string $key, $default = null)
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

/**
 * Ambil parameter dari GET dengan sanitasi
 * 
 * @param string $key Nama parameter
 * @param mixed $default Nilai default jika tidak ada
 * @return mixed
 */
function query(string $key, $default = null)
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

/**
 * Generate URL lengkap dari path relatif
 * 
 * @param string $path Path relatif
 * @return string URL lengkap
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Generate path ke asset (CSS, JS, images)
 * 
 * @param string $path Path relatif dari folder assets
 * @return string URL lengkap ke asset
 */
function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Format tanggal ke bahasa Indonesia
 * 
 * @param string $date Tanggal (format: Y-m-d)
 * @param bool $withDay Tampilkan nama hari
 * @return string Tanggal terformat
 */
function formatTanggal(string $date, bool $withDay = true): string
{
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $timestamp = strtotime($date);
    $dayIndex = (int) date('w', $timestamp);
    $day = (int) date('j', $timestamp);
    $month = (int) date('n', $timestamp);
    $year = date('Y', $timestamp);

    if ($withDay) {
        return "{$hari[$dayIndex]}, {$day} {$bulan[$month]} {$year}";
    }

    return "{$day} {$bulan[$month]} {$year}";
}

/**
 * Format waktu (HH:MM)
 * 
 * @param string $time Waktu (format: H:i:s)
 * @return string Waktu terformat
 */
function formatWaktu(string $time): string
{
    return date('H:i', strtotime($time));
}

/**
 * Generate CSRF token input field
 * 
 * @return string HTML hidden input field
 */
function csrfField(): string
{
    $token = Middleware::generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Cek apakah admin sudah login
 */
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Ambil data admin yang sedang login
 * 
 * @param string|null $key Key spesifik (nip, nama_lengkap, email)
 * @return mixed
 */
function adminData(?string $key = null)
{
    if ($key) {
        return $_SESSION['admin_data'][$key] ?? null;
    }
    return $_SESSION['admin_data'] ?? null;
}

/**
 * Truncate string dengan ellipsis
 * 
 * @param string $string String yang akan dipotong
 * @param int $length Panjang maksimal
 * @return string
 */
function truncate(string $string, int $length = 100): string
{
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length) . '...';
}
