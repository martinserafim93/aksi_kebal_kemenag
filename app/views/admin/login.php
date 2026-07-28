<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Admin AKSI KEBAL - Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Login Admin') ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin-auth.css') ?>">
</head>
<body class="auth-page">
    <!-- Animated Background -->
    <div class="auth-bg"></div>
    <div class="auth-grid"></div>

    <!-- Login Container -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- Branding -->
            <div class="auth-brand">
                <div class="auth-brand-icon" title="AKSI KEBAL">
                    📋
                </div>
                <h1><?= e(APP_NAME) ?></h1>
                <p><?= e(APP_FULL_NAME) ?></p>
            </div>

            <!-- Flash Messages -->
            <?php $flash = getFlash(); ?>
            <?php if ($flash): ?>
                <div class="auth-alert auth-alert-<?= e($flash['type']) ?>">
                    <span class="auth-alert-icon">
                        <?php if ($flash['type'] === 'error'): ?>⚠️<?php endif; ?>
                        <?php if ($flash['type'] === 'success'): ?>✅<?php endif; ?>
                        <?php if ($flash['type'] === 'warning'): ?>⏳<?php endif; ?>
                    </span>
                    <span><?= $flash['message'] ?></span>
                </div>
            <?php endif; ?>

            <!-- Session Timeout Warning -->
            <?php if (isset($timeout) && $timeout): ?>
                <div class="auth-alert auth-alert-warning">
                    <span class="auth-alert-icon">⏳</span>
                    <span>Sesi Anda telah berakhir. Silakan login kembali.</span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= url('admin/login') ?>" method="POST" class="auth-form" id="loginForm">
                <?= csrfField() ?>

                <!-- Email / NIP -->
                <div class="form-group">
                    <label for="identifier" class="form-label">Email atau NIP</label>
                    <div class="form-input-wrapper">
                        <span class="form-input-icon">👤</span>
                        <input
                            type="text"
                            id="identifier"
                            name="identifier"
                            class="form-input"
                            placeholder="Masukkan email atau NIP Anda"
                            autocomplete="username"
                            autofocus
                            required
                        >
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="form-input-wrapper">
                        <span class="form-input-icon">🔒</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                            required
                            style="padding-right: 2.75rem;"
                        >
                        <button type="button" class="form-toggle-password" id="togglePassword" title="Tampilkan/Sembunyikan Password">
                            👁️
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-text">Masuk ke Dashboard</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="auth-footer">
            <span>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?> — Kementerian Agama</span>
            <span class="auth-version">v<?= e(APP_VERSION) ?></span>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                this.textContent = isPassword ? '🔓' : '👁️';
            });
        }

        // Loading state on form submit
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('btnLogin');

        if (form && btn) {
            form.addEventListener('submit', function () {
                btn.classList.add('loading');
                btn.disabled = true;
            });
        }

        // Auto-hide flash messages after 5 seconds
        const alerts = document.querySelectorAll('.auth-alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    </script>
</body>
</html>
