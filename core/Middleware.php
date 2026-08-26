<?php
/**
 * AKSI KEBAL - Middleware
 * 
 * Middleware untuk proteksi halaman dan validasi akses.
 */

class Middleware
{
    /**
     * Cek apakah user sudah login sebagai admin
     * Redirect ke halaman login jika belum
     */
    public static function authAdmin(): void
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }

        // Cek session timeout
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];
            if ($elapsed > SESSION_LIFETIME) {
                session_unset();
                session_destroy();
                header('Location: ' . BASE_URL . '/admin/login?timeout=1');
                exit;
            }
        }

        // Update last activity
        $_SESSION['last_activity'] = time();
    }

    /**
     * Cek apakah user sudah login, jika ya redirect ke dashboard
     * Digunakan di halaman login agar admin yang sudah login tidak bisa akses lagi
     */
    public static function guest(): void
    {
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }
    }

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi CSRF token
     * @param string $token Token untuk divalidasi
     * @param bool $consume Jika true, token akan dihapus setelah validasi (default: true)
     */
    public static function validateCsrfToken(string $token, bool $consume = true): bool
    {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            // Regenerate token setelah validasi sukses (jika consume = true)
            if ($consume) {
                unset($_SESSION['csrf_token']);
            }
            return true;
        }
        return false;
    }
}
