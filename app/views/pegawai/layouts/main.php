<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'AKSI KEBAL - Absensi' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('img/favicon-32.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('img/apple-touch-icon.png') ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/pegawai.css') ?>?v=<?= time() ?>">
</head>
<body>
    
    <!-- Background Kantor Kemenag (blur/transparan) -->
    <div class="kemenag-bg-overlay" 
         style="background-image: url('<?= asset('img/kemenag-kaltara.jpeg') ?>');"></div>
    
    <div class="pegawai-wrapper">
        <header class="pegawai-header">
            <div class="container header-container">
                <div class="brand">
                    <img src="<?= asset('img/kemenag-new-2025.png') ?>" alt="Logo Kemenag" class="brand-logo">
                    <span>AKSI KEBAL</span>
                </div>
            </div>
        </header>

        <main class="pegawai-main container">
            <?= $content ?? '' ?>
        </main>

        <footer class="pegawai-footer">
            <p>&copy; <?= date('Y') ?> &mdash; <?= e(APP_NAME) ?> &mdash;<br>
            Kantor Wilayah Kementerian Agama Provinsi Kalimantan Utara</p>
        </footer>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
                position: 'top-end',
                customClass: { popup: 'swal2-toast-custom' }
            });
        <?php endif; ?>
    });
    </script>
    
    <!-- Extra JS -->
    <?= $extra_js ?? '' ?>
</body>
</html>
