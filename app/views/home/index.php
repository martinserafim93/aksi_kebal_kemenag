<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AKSI KEBAL - Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama">
    <title><?= e($title ?? APP_NAME) ?></title>
    
    <!-- Link ke file CSS eksternal baru -->
    <link rel="stylesheet" href="<?= asset('css/home.css') ?>?v=<?= time() ?>">
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
</head>
<body class="home-page">
    
    <!-- Particles Container -->
    <div id="particles-js" style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; z-index: 0; pointer-events: none;"></div>

    <!-- Decorative floating shapes (Background) -->
    <div class="home-decoration" aria-hidden="true">
        <div class="home-shape home-shape-1"></div>
        <div class="home-shape home-shape-2"></div>
    </div>

    <!-- Main Container Wrapper for 3D Tilt -->
    <div class="home-tilt-wrapper" style="perspective: 1000px; z-index: 1; width: 100%; max-width: 650px;">
        <!-- Main Container (Glassmorphism) -->
        <div class="home-container" id="tilt-card" style="transition: transform 0.1s ease-out; transform-style: preserve-3d;">
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
    </div>

    <!-- Overdrive Animations Script -->
    <script>
        // 1. Particles.js Configuration
        particlesJS('particles-js', {
            "particles": {
                "number": { "value": 60, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.3, "random": true },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.2, "width": 1 },
                "move": { "enable": true, "speed": 1.5, "direction": "none", "random": true, "out_mode": "out" }
            },
            "interactivity": {
                "detect_on": "window",
                "events": {
                    "onhover": { "enable": true, "mode": "grab" },
                    "resize": true
                },
                "modes": {
                    "grab": { "distance": 200, "line_linked": { "opacity": 0.4 } }
                }
            },
            "retina_detect": true
        });

        // 2. 3D Tilt Effect on Card
        const wrapper = document.querySelector('.home-tilt-wrapper');
        const card = document.getElementById('tilt-card');
        
        wrapper.addEventListener('mousemove', (e) => {
            const rect = wrapper.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * -8;
            const rotateY = ((x - centerX) / centerX) * 8;
            
            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });
        
        wrapper.addEventListener('mouseleave', () => {
            card.style.transform = `rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
            setTimeout(() => {
                card.style.transition = 'transform 0.1s ease-out';
            }, 300);
            card.style.transition = 'transform 0.5s ease-out';
        });
        
        wrapper.addEventListener('mouseenter', () => {
            card.style.transition = 'transform 0.1s ease-out';
        });
    </script>
</body>
</html>
