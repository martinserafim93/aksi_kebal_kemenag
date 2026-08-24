<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AKSI KEBAL - Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama">
    <title><?= e($title ?? APP_NAME) ?></title>
    
    <!-- Link ke file CSS eksternal baru -->
    <link rel="stylesheet" href="<?= asset('css/home.css') ?>?v=<?= time() ?>">
</head>
<body class="home-page">
    
    <!-- Decorative floating shapes (Background) -->
    <div class="home-decoration" aria-hidden="true">
        <div class="home-shape home-shape-1"></div>
        <div class="home-shape home-shape-2"></div>
    </div>

    <!-- Main Container (Glassmorphism) -->
    <div class="home-container">
        <!-- Logo -->
        <div class="home-logo-wrapper">
            <img src="<?= asset('img/kemenag-new-2025.png') ?>" alt="Logo Kementerian Agama" class="home-logo">
        </div>
        
        <!-- Typography -->
        <h1 class="home-title"><?= e(APP_NAME) ?></h1>
        <p class="home-subtitle">
            <strong><?= e(APP_FULL_NAME) ?></strong><br>
            Sistem Informasi Absensi Kegiatan Pegawai
        </p>
        
        <!-- Actions / Buttons -->
        <div class="home-actions">
            <a href="<?= url('admin/login') ?>" class="btn btn-primary">
                <span class="btn-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <span>Login Admin</span>
            </a>
        </div>

        <!-- Footer -->
        <div class="home-footer">
            <p>&copy; <?= date('Y') ?> &mdash; <strong><?= e(APP_NAME) ?></strong></p>
            <p>Kantor Wilayah Kementerian Agama Provinsi Kalimantan Utara</p>
        </div>
    </div>
</body>
</html>
