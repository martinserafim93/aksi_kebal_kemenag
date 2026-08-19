<?php ob_start(); ?>

<div class="card">
    <div class="card-body">
        <div class="success-icon-container">
            <i class='bx bxs-check-circle success-icon'></i>
        </div>
        
        <h1 class="success-title">Absensi Berhasil!</h1>
        <p class="success-subtitle">Terima kasih, data kehadiran Anda telah tersimpan.</p>
        
        <div class="data-list">
            <div class="data-item">
                <span class="data-label">Nama Pegawai</span>
                <span class="data-value"><?= e($absensi['nama_lengkap']) ?></span>
            </div>
            
            <div class="data-item">
                <span class="data-label">NIP</span>
                <span class="data-value"><?= e($absensi['nip']) ?></span>
            </div>
            
            <div class="data-item" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <span class="data-label">Jabatan</span>
                    <span class="data-value"><?= e($absensi['nama_jabatan'] ?? '-') ?></span>
                </div>
                <div>
                    <span class="data-label">Tim Kerja</span>
                    <span class="data-value"><?= e($absensi['nama_tim_kerja'] ?? '-') ?></span>
                </div>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 0.5rem 0;">
            
            <div class="data-item">
                <span class="data-label">Kegiatan</span>
                <span class="data-value"><?= e($absensi['nama_kegiatan']) ?></span>
            </div>
            
            <div class="data-item">
                <span class="data-label">Waktu Pelaksanaan</span>
                <span class="data-value">
                    <?= date('d M Y', strtotime($absensi['tanggal_kegiatan'])) ?> 
                    (<?= date('H:i', strtotime($absensi['waktu_mulai'])) ?> - <?= date('H:i', strtotime($absensi['waktu_selesai'])) ?>)
                </span>
            </div>
            
            <div class="data-item" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                <div>
                    <span class="data-label">Tanggal Submit</span>
                    <span class="data-value"><?= date('d M Y', strtotime($absensi['created_at'])) ?></span>
                </div>
                <div>
                    <span class="data-label">Waktu Submit</span>
                    <span class="data-value"><?= date('H:i:s', strtotime($absensi['created_at'])) ?></span>
                </div>
            </div>
            
            <div class="data-item" style="margin-top: 0.5rem;">
                <span class="data-label">Status Kehadiran</span>
                <div>
                    <span class="badge-success">
                        <i class='bx bx-check'></i> Hadir
                    </span>
                </div>
            </div>
        </div>
        
        <p class="timer-text">
            Halaman akan dialihkan dalam <span class="timer-count" id="countdown">15</span> detik...
        </p>
        
        <!-- Redirect to formulir absensi (placeholder for Issue #12 URL) -->
        <a href="<?= url('absensi?kegiatan=' . $absensi['kode_kegiatan']) ?>" class="btn btn-primary" id="btnBack">
            Kembali ke Formulir
        </a>
    </div>
</div>

<?php ob_start(); ?>
<script>
    // Countdown Timer Logic
    let seconds = 15;
    const countdownEl = document.getElementById('countdown');
    const redirectUrl = document.getElementById('btnBack').href;
    
    const interval = setInterval(function() {
        seconds--;
        countdownEl.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = redirectUrl;
        }
    }, 1000);
</script>
<?php 
$extra_js = ob_get_clean();
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
