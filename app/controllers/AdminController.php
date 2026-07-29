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

    // ==========================================
    // MANAJEMEN TIM KERJA
    // ==========================================

    /**
     * Halaman Daftar Tim Kerja
     */
    public function tim_kerja(): void
    {
        Middleware::authAdmin();
        $model = $this->model('TimKerjaModel');
        $tim_kerja = $model->getAll();
        
        $this->view('admin/tim-kerja/index', [
            'title' => 'Manajemen Tim Kerja - AKSI KEBAL',
            'tim_kerja' => $tim_kerja,
            'active_menu' => 'tim_kerja'
        ]);
    }

    /**
     * Proses Tambah Tim Kerja
     */
    public function tim_kerja_create(): void
    {
        Middleware::authAdmin();
        $model = $this->model('TimKerjaModel');

        if (isPost()) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
                $this->redirect('admin/tim-kerja-create');
                return;
            }

            $nama_tim_kerja = input('nama_tim_kerja');

            if (empty($nama_tim_kerja)) {
                setFlash('error', 'Nama tim kerja tidak boleh kosong.');
                $this->redirect('admin/tim-kerja-create');
                return;
            }

            if ($model->isNameExists($nama_tim_kerja)) {
                setFlash('error', 'Nama tim kerja sudah terdaftar.');
                $this->redirect('admin/tim-kerja-create');
                return;
            }

            if ($model->create(['nama_tim_kerja' => $nama_tim_kerja])) {
                setFlash('success', 'Tim Kerja berhasil ditambahkan.');
                $this->redirect('admin/tim-kerja');
                return;
            } else {
                setFlash('error', 'Gagal menambahkan tim kerja.');
            }
        }

        $this->view('admin/tim-kerja/create', [
            'title' => 'Tambah Tim Kerja - AKSI KEBAL',
            'active_menu' => 'tim_kerja'
        ]);
    }

    /**
     * Proses Edit Tim Kerja
     */
    public function tim_kerja_edit($id = null): void
    {
        Middleware::authAdmin();
        if (!$id) {
            $this->redirect('admin/tim-kerja');
            return;
        }

        $model = $this->model('TimKerjaModel');
        $tim_kerja = $model->findById((int)$id);

        if (!$tim_kerja) {
            setFlash('error', 'Tim kerja tidak ditemukan.');
            $this->redirect('admin/tim-kerja');
            return;
        }

        if (isPost()) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
                $this->redirect('admin/tim-kerja-edit/' . $id);
                return;
            }

            $nama_tim_kerja = input('nama_tim_kerja');

            if (empty($nama_tim_kerja)) {
                setFlash('error', 'Nama tim kerja tidak boleh kosong.');
                $this->redirect('admin/tim-kerja-edit/' . $id);
                return;
            }

            if ($model->isNameExists($nama_tim_kerja, (int)$id)) {
                setFlash('error', 'Nama tim kerja sudah terdaftar.');
                $this->redirect('admin/tim-kerja-edit/' . $id);
                return;
            }

            if ($model->update((int)$id, ['nama_tim_kerja' => $nama_tim_kerja])) {
                setFlash('success', 'Tim Kerja berhasil diperbarui.');
                $this->redirect('admin/tim-kerja');
                return;
            } else {
                setFlash('error', 'Gagal memperbarui tim kerja.');
            }
        }

        $this->view('admin/tim-kerja/edit', [
            'title' => 'Edit Tim Kerja - AKSI KEBAL',
            'tim_kerja' => $tim_kerja,
            'active_menu' => 'tim_kerja'
        ]);
    }

    /**
     * Proses Hapus Tim Kerja
     */
    public function tim_kerja_delete($id = null): void
    {
        Middleware::authAdmin();

        if (isPost() && $id) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
            } else {
                $model = $this->model('TimKerjaModel');
                
                if ($model->delete((int)$id)) {
                    setFlash('success', 'Tim Kerja berhasil dihapus.');
                } else {
                    setFlash('error', 'Gagal menghapus! Tim Kerja ini masih memiliki anggota pegawai.');
                }
            }
        }
        
        $this->redirect('admin/tim-kerja');
    }

    // ==========================================
    // MANAJEMEN JABATAN
    // ==========================================

    /**
     * Halaman Daftar Jabatan
     */
    public function jabatan(): void
    {
        Middleware::authAdmin();
        $model = $this->model('JabatanModel');
        $jabatan = $model->getAll();
        
        $this->view('admin/jabatan/index', [
            'title' => 'Manajemen Jabatan - AKSI KEBAL',
            'jabatan' => $jabatan,
            'active_menu' => 'jabatan'
        ]);
    }

    /**
     * Proses Tambah Jabatan
     */
    public function jabatan_create(): void
    {
        Middleware::authAdmin();
        $model = $this->model('JabatanModel');

        if (isPost()) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
                $this->redirect('admin/jabatan-create');
                return;
            }

            $nama_jabatan = input('nama_jabatan');

            if (empty($nama_jabatan)) {
                setFlash('error', 'Nama jabatan tidak boleh kosong.');
                $this->redirect('admin/jabatan-create');
                return;
            }

            if ($model->isNameExists($nama_jabatan)) {
                setFlash('error', 'Nama jabatan sudah terdaftar.');
                $this->redirect('admin/jabatan-create');
                return;
            }

            if ($model->create(['nama_jabatan' => $nama_jabatan])) {
                setFlash('success', 'Jabatan berhasil ditambahkan.');
                $this->redirect('admin/jabatan');
                return;
            } else {
                setFlash('error', 'Gagal menambahkan jabatan.');
            }
        }

        $this->view('admin/jabatan/create', [
            'title' => 'Tambah Jabatan - AKSI KEBAL',
            'active_menu' => 'jabatan'
        ]);
    }

    /**
     * Proses Edit Jabatan
     */
    public function jabatan_edit($id = null): void
    {
        Middleware::authAdmin();
        if (!$id) {
            $this->redirect('admin/jabatan');
            return;
        }

        $model = $this->model('JabatanModel');
        $jabatan = $model->findById((int)$id);

        if (!$jabatan) {
            setFlash('error', 'Jabatan tidak ditemukan.');
            $this->redirect('admin/jabatan');
            return;
        }

        if (isPost()) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
                $this->redirect('admin/jabatan-edit/' . $id);
                return;
            }

            $nama_jabatan = input('nama_jabatan');

            if (empty($nama_jabatan)) {
                setFlash('error', 'Nama jabatan tidak boleh kosong.');
                $this->redirect('admin/jabatan-edit/' . $id);
                return;
            }

            if ($model->isNameExists($nama_jabatan, (int)$id)) {
                setFlash('error', 'Nama jabatan sudah terdaftar.');
                $this->redirect('admin/jabatan-edit/' . $id);
                return;
            }

            if ($model->update((int)$id, ['nama_jabatan' => $nama_jabatan])) {
                setFlash('success', 'Jabatan berhasil diperbarui.');
                $this->redirect('admin/jabatan');
                return;
            } else {
                setFlash('error', 'Gagal memperbarui jabatan.');
            }
        }

        $this->view('admin/jabatan/edit', [
            'title' => 'Edit Jabatan - AKSI KEBAL',
            'jabatan' => $jabatan,
            'active_menu' => 'jabatan'
        ]);
    }

    /**
     * Proses Hapus Jabatan
     */
    public function jabatan_delete($id = null): void
    {
        Middleware::authAdmin();

        if (isPost() && $id) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
            } else {
                $model = $this->model('JabatanModel');
                
                if ($model->delete((int)$id)) {
                    setFlash('success', 'Jabatan berhasil dihapus.');
                } else {
                    setFlash('error', 'Gagal menghapus! Jabatan ini masih memiliki anggota pegawai.');
                }
            }
        }
        
        $this->redirect('admin/jabatan');
    }

    // ==========================================
    // MANAJEMEN KEGIATAN
    // ==========================================

    public function kegiatan(): void
    {
        Middleware::authAdmin();
        $model = $this->model('KegiatanModel');
        
        $search = input('search') ?? '';
        $status = input('status') ?? '';
        $jenis = input('jenis') ?? '';
        
        $kegiatan = $model->getAll($search, $status, $jenis);

        $this->view('admin/kegiatan/index', [
            'title' => 'Manajemen Kegiatan - AKSI KEBAL',
            'kegiatan' => $kegiatan,
            'search' => $search,
            'status' => $status,
            'jenis' => $jenis,
            'active_menu' => 'kegiatan'
        ]);
    }

    public function kegiatan_create(): void
    {
        Middleware::authAdmin();
        $model = $this->model('KegiatanModel');

        if (isPost()) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
                $this->redirect('admin/kegiatan-create');
                return;
            }

            $data = [
                'nama_kegiatan' => input('nama_kegiatan'),
                'jenis_kegiatan' => input('jenis_kegiatan'),
                'tanggal_kegiatan' => input('tanggal_kegiatan'),
                'waktu_mulai' => input('waktu_mulai'),
                'waktu_selesai' => input('waktu_selesai'),
                'lokasi_kegiatan' => input('lokasi_kegiatan'),
                'deskripsi_kegiatan' => input('deskripsi_kegiatan')
            ];

            if (empty($data['nama_kegiatan']) || empty($data['jenis_kegiatan']) || empty($data['tanggal_kegiatan']) || empty($data['waktu_mulai']) || empty($data['waktu_selesai'])) {
                setFlash('error', 'Semua kolom wajib (*) harus diisi.');
            } else {
                if ($model->create($data)) {
                    setFlash('success', 'Kegiatan baru berhasil ditambahkan.');
                    $this->redirect('admin/kegiatan');
                    return;
                } else {
                    setFlash('error', 'Terjadi kesalahan sistem.');
                }
            }
        }

        $this->view('admin/kegiatan/create', [
            'title' => 'Tambah Kegiatan - AKSI KEBAL',
            'active_menu' => 'kegiatan'
        ]);
    }

    public function kegiatan_edit($id = null): void
    {
        Middleware::authAdmin();
        if (!$id) {
            $this->redirect('admin/kegiatan');
            return;
        }

        $model = $this->model('KegiatanModel');
        $kegiatan = $model->findById((int)$id);

        if (!$kegiatan) {
            setFlash('error', 'Kegiatan tidak ditemukan.');
            $this->redirect('admin/kegiatan');
            return;
        }

        if (isPost()) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
                $this->redirect("admin/kegiatan-edit/$id");
                return;
            }

            $data = [
                'nama_kegiatan' => input('nama_kegiatan'),
                'jenis_kegiatan' => input('jenis_kegiatan'),
                'tanggal_kegiatan' => input('tanggal_kegiatan'),
                'waktu_mulai' => input('waktu_mulai'),
                'waktu_selesai' => input('waktu_selesai'),
                'lokasi_kegiatan' => input('lokasi_kegiatan'),
                'deskripsi_kegiatan' => input('deskripsi_kegiatan')
            ];

            if (empty($data['nama_kegiatan']) || empty($data['jenis_kegiatan']) || empty($data['tanggal_kegiatan']) || empty($data['waktu_mulai']) || empty($data['waktu_selesai'])) {
                setFlash('error', 'Semua kolom wajib (*) harus diisi.');
            } else {
                if ($model->update((int)$id, $data)) {
                    setFlash('success', 'Kegiatan berhasil diperbarui.');
                    $this->redirect('admin/kegiatan');
                    return;
                } else {
                    setFlash('error', 'Terjadi kesalahan sistem.');
                }
            }
        }

        $this->view('admin/kegiatan/edit', [
            'title' => 'Edit Kegiatan - AKSI KEBAL',
            'kegiatan' => $kegiatan,
            'active_menu' => 'kegiatan'
        ]);
    }

    public function kegiatan_delete($id = null): void
    {
        Middleware::authAdmin();

        if (isPost() && $id) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
            } else {
                $model = $this->model('KegiatanModel');
                if ($model->delete((int)$id)) {
                    setFlash('success', 'Kegiatan berhasil dihapus.');
                } else {
                    setFlash('error', 'Gagal menghapus! Kegiatan ini memiliki data absensi terikat.');
                }
            }
        }
        $this->redirect('admin/kegiatan');
    }

    public function kegiatan_publish($id = null): void
    {
        Middleware::authAdmin();

        if (isPost() && $id) {
            $csrfToken = input('csrf_token');
            if (!$csrfToken || !Middleware::validateCsrfToken($csrfToken)) {
                setFlash('error', 'Sesi tidak valid.');
            } else {
                $model = $this->model('KegiatanModel');
                $kegiatan = $model->findById((int)$id);
                if ($kegiatan) {
                    // Pastikan config app URL digunakan dengan baik, url() function ada dari helpers.php
                    $url = url("absensi?kegiatan=" . $id);
                    if ($model->publish((int)$id, $url)) {
                        setFlash('success', 'Kegiatan berhasil di-publish!');
                    } else {
                        setFlash('error', 'Gagal mem-publish kegiatan.');
                    }
                }
            }
        }
        $this->redirect('admin/kegiatan');
    }

    public function kegiatan_qrcode($id = null): void
    {
        Middleware::authAdmin();
        if (!$id) {
            $this->redirect('admin/kegiatan');
            return;
        }

        $model = $this->model('KegiatanModel');
        $kegiatan = $model->findById((int)$id);

        if (!$kegiatan || $kegiatan['status_kegiatan'] !== 'Published') {
            setFlash('error', 'Kegiatan tidak valid atau belum di-publish.');
            $this->redirect('admin/kegiatan');
            return;
        }

        $this->view('admin/kegiatan/qrcode', [
            'title' => 'QR Code Kegiatan - AKSI KEBAL',
            'kegiatan' => $kegiatan,
            'active_menu' => 'kegiatan'
        ]);
    }
}
