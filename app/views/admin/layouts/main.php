<?php
/**
 * AKSI KEBAL - Admin Main Layout
 * 
 * Template reusable untuk halaman admin.
 * Memuat Sidebar, Header, Content Area, dan Footer.
 * 
 * Variabel yang diekspektasikan:
 * - $title: Judul halaman
 * - $content: Konten unik tiap halaman (diambil dengan output buffering sebelum me-require layout ini)
 * - $active_menu: Nama menu yang aktif untuk sidebar highlight
 */

// Helper function to get current URL path
$current_url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$active_menu = $active_menu ?? explode('/', $current_url)[1] ?? 'dashboard';

// Ambil info admin
$admin_name = adminData('nama_lengkap') ?? 'Admin';
$admin_role = adminData('nama_jabatan') ?? 'Administrator';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Dashboard') ?> - <?= e(APP_NAME) ?></title>
    
    <!-- Boxicons for Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Layout CSS -->
    <link rel="stylesheet" href="<?= asset('css/admin-layout.css') ?>?v=<?= time() ?>">
    
    <!-- Custom Page CSS (optional) -->
    <?php if (isset($extra_css)) echo $extra_css; ?>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

    <div class="admin-wrapper">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= url('admin/dashboard') ?>" class="sidebar-brand">
                    <img src="<?= asset('img/kemenag-new-2025.png') ?>" alt="Logo Kemenag" class="sidebar-logo">
                    <span>AKSI KEBAL</span>
                </a>
            </div>

            <div class="sidebar-user">
                <div class="user-avatar">
                    <?= strtoupper(substr($admin_name, 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name" title="<?= e($admin_name) ?>"><?= e($admin_name) ?></div>
                    <div class="user-role" title="<?= e($admin_role) ?>"><?= e($admin_role) ?></div>
                </div>
            </div>

            <ul class="sidebar-menu">
                <div class="menu-label">Menu Utama</div>
                <li>
                    <a href="<?= url('admin/dashboard') ?>" class="<?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <div class="menu-label">Master Data</div>
                <li>
                    <a href="<?= url('admin/pegawai') ?>" class="<?= $active_menu === 'pegawai' ? 'active' : '' ?>">
                        <i class='bx bxs-user-detail'></i>
                        <span>Pegawai</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/tim-kerja') ?>" class="<?= $active_menu === 'tim-kerja' || $active_menu === 'tim_kerja' ? 'active' : '' ?>">
                        <i class='bx bxs-group'></i>
                        <span>Tim Kerja</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/jabatan') ?>" class="<?= $active_menu === 'jabatan' ? 'active' : '' ?>">
                        <i class='bx bxs-briefcase'></i>
                        <span>Jabatan</span>
                    </a>
                </li>

                <div class="menu-label">Kegiatan & Absensi</div>
                <li>
                    <a href="<?= url('admin/kegiatan') ?>" class="<?= $active_menu === 'kegiatan' ? 'active' : '' ?>">
                        <i class='bx bxs-calendar-event'></i>
                        <span>Kegiatan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= url('admin/absensi') ?>" class="<?= $active_menu === 'absensi' ? 'active' : '' ?>">
                        <i class='bx bxs-check-square'></i>
                        <span>Absensi</span>
                    </a>
                </li>

                <div class="menu-label">Pengaturan</div>
                <li>
                    <a href="<?= url('admin/logout') ?>">
                        <i class='bx bx-log-out text-danger'></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main" id="mainContent">
            
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class='bx bx-menu'></i>
                    </button>
                </div>
                
                <div class="header-right">
                    <!-- Tambahan menu kanan header bisa di sini -->
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">
                        <?= e(formatTanggal(date('Y-m-d'))) ?>
                    </span>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                
                <!-- Render the unique page content here -->
                <?= $content ?? '' ?>

            </div>

            <!-- Footer -->
            <footer class="admin-footer">
                &copy; <?= date('Y') ?> &mdash; <strong><?= e(APP_NAME) ?></strong> &mdash;<br>
                Kantor Wilayah Kementerian Agama Provinsi Kalimantan Utara
            </footer>

        </main>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                if (window.innerWidth <= 991) {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                }
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            
            // Close sidebar on mobile when overlay is clicked
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
            
            // Handle resize events
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                }
            });
        });
    </script>

    <!-- SweetAlert2: Delete Confirmation & Flash Messages -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle semua form delete dengan SweetAlert2
        document.querySelectorAll('form.form-delete').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var formEl = this;
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="bx bx-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'swal-popup-custom'
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        formEl.submit();
                    }
                });
            });
        });

        // Tampilkan flash message sebagai SweetAlert toast
        <?php $swalFlash = getFlash(); ?>
        <?php if ($swalFlash): ?>
            Swal.fire({
                icon: '<?= $swalFlash['type'] === 'success' ? 'success' : 'error' ?>',
                title: '<?= $swalFlash['type'] === 'success' ? 'Berhasil!' : 'Gagal!' ?>',
                text: '<?= addslashes($swalFlash['message']) ?>',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        <?php endif; ?>
    });
    </script>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Custom Page JS (optional) -->
    <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
