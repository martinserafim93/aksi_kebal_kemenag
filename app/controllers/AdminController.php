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
     * Halaman utama setelah login menampilkan statistik
     */
    public function dashboard(): void
    {
        // Proteksi halaman — hanya admin yang login
        Middleware::authAdmin();

        $dashboardModel = $this->model('DashboardModel');

        $data = [
            'title' => 'Dashboard - ' . APP_NAME,
            'total_pegawai' => $dashboardModel->getTotalPegawai(),
            'total_kegiatan' => $dashboardModel->getTotalKegiatan(),
            'total_kegiatan_published' => $dashboardModel->getTotalKegiatanPublished(),
            'total_absensi_hari_ini' => $dashboardModel->getTotalAbsensiHariIni(),
            'kegiatan_terbaru' => $dashboardModel->getKegiatanTerbaru(5)
        ];

        $this->view('admin/dashboard', $data);
    }

    // =========================================================================
    // MANAJEMEN PEGAWAI (CRUD)
    // =========================================================================

    /**
     * Halaman Daftar Pegawai (Read)
     */
    public function pegawai(): void
    {
        Middleware::authAdmin();

        $pegawaiModel = $this->model('PegawaiModel');

        // Parameter pencarian & paginasi
        $search = query('search', '');
        $page = (int) query('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Ambil data
        $pegawai = $pegawaiModel->getAllPaginated($search, $limit, $offset);
        $total_data = $pegawaiModel->countAll($search);
        $total_page = ceil($total_data / $limit);

        $this->view('admin/pegawai/index', [
            'title'      => 'Manajemen Pegawai - ' . APP_NAME,
            'pegawai'    => $pegawai,
            'search'     => $search,
            'page'       => $page,
            'total_page' => $total_page,
            'active_menu'=> 'pegawai'
        ]);
    }

    /**
     * Halaman Tambah Pegawai (Create)
     */
    public function pegawai_create(): void
    {
        Middleware::authAdmin();
        $pegawaiModel = $this->model('PegawaiModel');

        // POST Request - Proses simpan
        if (isPost()) {
            // Validasi CSRF
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
                $this->redirect('admin/pegawai-create');
                return;
            }

            $nip = input('nip');
            $nama = input('nama_lengkap');
            $id_jabatan = input('id_jabatan');
            $id_tim_kerja = input('id_tim_kerja');
            $email = input('email');
            $password = input('password');
            $role = input('role', 'pegawai');

            // Validasi Input
            $errors = [];
            if (empty($nip)) $errors[] = 'NIP wajib diisi.';
            if (empty($nama)) $errors[] = 'Nama lengkap wajib diisi.';
            if (empty($password)) $errors[] = 'Password wajib diisi.';
            
            if ($pegawaiModel->isNipExists($nip)) {
                $errors[] = 'NIP sudah terdaftar.';
            }

            if (!empty($errors)) {
                setFlash('error', implode('<br>', $errors));
                $this->redirect('admin/pegawai-create');
                return;
            }

            // Eksekusi Simpan
            $data = [
                'nip' => $nip,
                'nama_lengkap' => $nama,
                'id_jabatan' => $id_jabatan,
                'id_tim_kerja' => $id_tim_kerja,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];

            if ($pegawaiModel->create($data)) {
                setFlash('success', 'Data pegawai berhasil ditambahkan.');
                $this->redirect('admin/pegawai');
            } else {
                setFlash('error', 'Terjadi kesalahan sistem saat menyimpan data.');
                $this->redirect('admin/pegawai-create');
            }
            return;
        }

        // GET Request - Tampilkan form
        $this->view('admin/pegawai/create', [
            'title'      => 'Tambah Pegawai - ' . APP_NAME,
            'jabatan'    => $pegawaiModel->getAllJabatan(),
            'tim_kerja'  => $pegawaiModel->getAllTimKerja(),
            'active_menu'=> 'pegawai'
        ]);
    }

    /**
     * Halaman Edit Pegawai (Update)
     * 
     * @param string $nip NIP Pegawai
     */
    public function pegawai_edit($nip = null): void
    {
        Middleware::authAdmin();

        if (empty($nip)) {
            $this->redirect('admin/pegawai');
            return;
        }

        $pegawaiModel = $this->model('PegawaiModel');
        $pegawai = $pegawaiModel->findByNip($nip);

        if (!$pegawai) {
            setFlash('error', 'Data pegawai tidak ditemukan.');
            $this->redirect('admin/pegawai');
            return;
        }

        // POST Request - Proses update
        if (isPost()) {
            // Validasi CSRF
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid. Silakan coba lagi.');
                $this->redirect('admin/pegawai-edit/' . urlencode($nip));
                return;
            }

            $nip_baru = input('nip');
            $nama = input('nama_lengkap');
            $id_jabatan = input('id_jabatan');
            $id_tim_kerja = input('id_tim_kerja');
            $email = input('email');
            $password = input('password');
            $role = input('role', 'pegawai');

            // Validasi Input
            $errors = [];
            if (empty($nip_baru)) $errors[] = 'NIP wajib diisi.';
            if (empty($nama)) $errors[] = 'Nama lengkap wajib diisi.';
            
            if ($pegawaiModel->isNipExists($nip_baru, $nip)) {
                $errors[] = 'NIP sudah terdaftar pada pegawai lain.';
            }

            if (!empty($errors)) {
                setFlash('error', implode('<br>', $errors));
                $this->redirect('admin/pegawai-edit/' . urlencode($nip));
                return;
            }

            // Eksekusi Update
            $data = [
                'nip' => $nip_baru,
                'nama_lengkap' => $nama,
                'id_jabatan' => $id_jabatan,
                'id_tim_kerja' => $id_tim_kerja,
                'email' => $email,
                'role' => $role
            ];

            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($pegawaiModel->update($nip, $data)) {
                setFlash('success', 'Data pegawai berhasil diperbarui.');
                $this->redirect('admin/pegawai');
            } else {
                setFlash('error', 'Terjadi kesalahan sistem saat memperbarui data.');
                $this->redirect('admin/pegawai-edit/' . urlencode($nip));
            }
            return;
        }

        // GET Request - Tampilkan form
        $this->view('admin/pegawai/edit', [
            'title'      => 'Edit Pegawai - ' . APP_NAME,
            'pegawai'    => $pegawai,
            'jabatan'    => $pegawaiModel->getAllJabatan(),
            'tim_kerja'  => $pegawaiModel->getAllTimKerja(),
            'active_menu'=> 'pegawai'
        ]);
    }

    /**
     * Proses Hapus Pegawai (Delete)
     * 
     * @param string $nip NIP Pegawai
     */
    public function pegawai_delete($nip = null): void
    {
        Middleware::authAdmin();

        if (isPost() && !empty($nip)) {
            // Validasi CSRF
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
            } else {
                // Cegah admin menghapus akunnya sendiri
                if ($nip === adminData('nip')) {
                    setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
                } else {
                    $pegawaiModel = $this->model('PegawaiModel');
                    if ($pegawaiModel->delete($nip)) {
                        setFlash('success', 'Data pegawai berhasil dihapus.');
                    } else {
                        setFlash('error', 'Gagal menghapus data pegawai.');
                    }
                }
            }
        }
        
        $this->redirect('admin/pegawai');
    }
}
