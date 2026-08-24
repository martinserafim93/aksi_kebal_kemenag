<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Admin AKSI KEBAL - Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title ?? 'Login Admin') ?></title>
    <link rel="stylesheet" href="<?= asset('css/admin-auth.css') ?>?v=<?= time() ?>">
</head>
<body class="auth-page">

    <!-- Decorative floating shapes -->
    <div class="auth-decoration" aria-hidden="true">
        <div class="auth-shape auth-shape-1"></div>
        <div class="auth-shape auth-shape-2"></div>
        <div class="auth-shape auth-shape-3"></div>
    </div>

    <!-- Login Container -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- Branding -->
            <div class="auth-brand">
                <img src="<?= asset('img/kemenag-new-2025.png') ?>" alt="Logo Kemenag" class="auth-brand-logo">
                <h1><?= e(APP_NAME) ?></h1>
                <p><?= e(APP_FULL_NAME) ?></p>
            </div>

            <div class="auth-divider">
                <span>Login Administrator</span>
            </div>

            <!-- Flash Messages -->
            <div class="auth-alert-wrapper">
                <?php $flash = getFlash(); ?>
                <?php if ($flash): ?>
                    <div class="auth-alert auth-alert-<?= e($flash['type']) ?> <?= $flash['type'] !== 'success' ? 'auth-alert-dismissible' : '' ?>" role="alert" aria-live="assertive">
                        <span class="auth-alert-icon" aria-hidden="true">
                            <?php if ($flash['type'] === 'error'): ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                            <?php elseif ($flash['type'] === 'success'): ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                            <?php elseif ($flash['type'] === 'warning'): ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php endif; ?>
                        </span>
                        <span><?= $flash['message'] ?></span>
                        
                        <?php if ($flash['type'] !== 'success'): ?>
                            <button type="button" class="auth-alert-close" aria-label="Tutup pesan">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Session Timeout Warning -->
                <?php if (isset($timeout) && $timeout): ?>
                    <div class="auth-alert auth-alert-warning auth-alert-dismissible" role="alert" aria-live="assertive">
                        <span class="auth-alert-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </span>
                        <span>Sesi Anda telah berakhir. Silakan login kembali.</span>
                        <button type="button" class="auth-alert-close" aria-label="Tutup pesan">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Login Form -->
            <form action="<?= url('admin/login') ?>" method="POST" class="auth-form" id="loginForm">
                <?= csrfField() ?>

                <!-- Email / NIP -->
                <div class="form-group">
                    <label for="identifier" class="form-label">Email atau NIP</label>
                    <div class="form-input-wrapper">
                        <span class="form-input-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
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
                        <span class="form-input-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="form-toggle-password" id="togglePassword" aria-label="Tampilkan password">
                            <svg id="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg id="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
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
            <p>&copy; <?= date('Y') ?> &mdash; <?= e(APP_NAME) ?></p>
            <p>Kantor Wilayah Kementerian Agama Provinsi Kalimantan Utara</p>
            <span class="auth-version">v<?= e(APP_VERSION) ?></span>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toggle password visibility
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const iconEye = document.getElementById('icon-eye');
        const iconEyeOff = document.getElementById('icon-eye-off');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                
                if (isPassword) {
                    iconEye.style.display = 'none';
                    iconEyeOff.style.display = 'block';
                    this.setAttribute('aria-label', 'Sembunyikan password');
                } else {
                    iconEye.style.display = 'block';
                    iconEyeOff.style.display = 'none';
                    this.setAttribute('aria-label', 'Tampilkan password');
                }
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

        // Auto-hide success messages only
        const successAlerts = document.querySelectorAll('.auth-alert-success');
        successAlerts.forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    alert.remove();
                }, 500);
            }, 5000);
        });

        // Close button for dismissible alerts
        const closeBtns = document.querySelectorAll('.auth-alert-close');
        closeBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const alert = this.closest('.auth-alert');
                alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';
                setTimeout(function () {
                    alert.remove();
                }, 300);
            });
        });
    </script>
</body>
</html>
