<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Admin AKSI KEBAL">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Dashboard') ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin-auth.css') ?>">
</head>
<body>
    <div class="admin-dashboard-placeholder">
        <div class="dashboard-welcome">

            <!-- Flash Message -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="dashboard-alert">
                    <div class="auth-alert auth-alert-<?= e($flash['type']) ?>">
                        <span class="auth-alert-icon">
                            <?php if ($flash['type'] === 'success'): ?>✅<?php endif; ?>
                            <?php if ($flash['type'] === 'error'): ?>⚠️<?php endif; ?>
                        </span>
                        <span><?= $flash['message'] ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Welcome -->
            <div class="dashboard-welcome-icon">
                🏠
            </div>
            <h1>Dashboard Admin</h1>
            <p>Selamat datang, <strong><?= e(adminData('nama_lengkap')) ?></strong></p>
            <span class="badge-role"><?= e(adminData('role')) ?></span>

            <!-- Admin Info -->
            <div class="dashboard-info">
                <div class="dashboard-info-row">
                    <span class="dashboard-info-label">NIP</span>
                    <span class="dashboard-info-value"><?= e(adminData('nip')) ?></span>
                </div>
                <div class="dashboard-info-row">
                    <span class="dashboard-info-label">Email</span>
                    <span class="dashboard-info-value"><?= e(adminData('email') ?? '-') ?></span>
                </div>
                <div class="dashboard-info-row">
                    <span class="dashboard-info-label">Jabatan</span>
                    <span class="dashboard-info-value"><?= e(adminData('nama_jabatan') ?? '-') ?></span>
                </div>
                <div class="dashboard-info-row">
                    <span class="dashboard-info-label">Tim Kerja</span>
                    <span class="dashboard-info-value"><?= e(adminData('nama_tim_kerja') ?? '-') ?></span>
                </div>
            </div>

            <!-- Logout -->
            <a href="<?= url('admin/logout') ?>" class="btn-logout" id="btnLogout">
                🚪 Logout
            </a>

            <p style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--gray-600);">
                Dashboard lengkap akan dikembangkan pada issue berikutnya.
            </p>
        </div>
    </div>

    <script>
        // Auto-hide flash messages after 4 seconds
        const alerts = document.querySelectorAll('.dashboard-alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    alert.remove();
                }, 500);
            }, 4000);
        });
    </script>
</body>
</html>
