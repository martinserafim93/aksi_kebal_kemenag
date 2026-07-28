<?php
/**
 * AKSI KEBAL - Admin Controller
 * 
 * Controller untuk semua halaman admin.
 * Menangani login, logout, dashboard, dan fitur admin lainnya.
 */

class AdminController extends Controller
{
    /**
     * Halaman Login Admin (GET & POST)
     * 
     * GET  → Tampilkan form login
     * POST → Proses autentikasi
     */
    public function login(): void
    {
        // Jika sudah login, redirect ke dashboard
        Middleware::guest();

        if (isPost()) {
            $this->processLogin();
            return;
        }

        // Cek apakah ada parameter timeout
        $timeout = query('timeout');

        $this->view('admin/login', [
            'title'   => 'Login Admin - ' . APP_NAME,
            'timeout' => $timeout
        ]);
    }

    /**
     * Proses login admin
     */
    private function processLogin(): void
    {
        // Validasi CSRF token
        $csrfToken = input('csrf_token');
        if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
            setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
            $this->redirect('admin/login');
            return;
        }

        // Ambil input
        $identifier = input('identifier'); // Email atau NIP
        $password   = input('password');

        // Validasi input tidak kosong
        $errors = [];
        if (empty($identifier)) {
            $errors[] = 'Email atau NIP wajib diisi.';
        }
        if (empty($password)) {
            $errors[] = 'Password wajib diisi.';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            $this->redirect('admin/login');
            return;
        }

        // Cari admin di database
        $authModel = $this->model('AuthModel');
        $admin = $authModel->findAdminByEmailOrNip($identifier);

        if (!$admin) {
            setFlash('error', 'Email/NIP atau password salah.');
            $this->redirect('admin/login');
            return;
        }

        // Verifikasi password
        if (!password_verify($password, $admin['password'])) {
            setFlash('error', 'Email/NIP atau password salah.');
            $this->redirect('admin/login');
            return;
        }

        // Login berhasil — set session
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_data'] = [
            'nip'            => $admin['nip'],
            'nama_lengkap'   => $admin['nama_lengkap'],
            'email'          => $admin['email'],
            'role'           => $admin['role'],
            'nama_jabatan'   => $admin['nama_jabatan'],
            'nama_tim_kerja' => $admin['nama_tim_kerja']
        ];
        $_SESSION['last_activity'] = time();

        setFlash('success', 'Selamat datang, ' . e($admin['nama_lengkap']) . '!');
        $this->redirect('admin/dashboard');
    }

    /**
     * Logout Admin
     * Menghancurkan session dan redirect ke halaman login
     */
    public function logout(): void
    {
        // Hapus semua data session
        $_SESSION = [];

        // Hapus session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Hancurkan session
        session_destroy();

        // Mulai session baru untuk flash message
        session_start();
        setFlash('success', 'Anda berhasil logout.');
        $this->redirect('admin/login');
    }

    /**
     * Dashboard Admin
     * Halaman utama setelah login (placeholder)
     */
    public function dashboard(): void
    {
        // Proteksi halaman — hanya admin yang login
        Middleware::authAdmin();

        $this->view('admin/dashboard', [
            'title' => 'Dashboard - ' . APP_NAME
        ]);
    }
}
