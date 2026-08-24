<?php ob_start(); ?>

<link rel="stylesheet" href="<?= asset('css/absensi.css') ?>?v=<?= time() ?>">

<div class="card abs-card stagger-1" style="max-width: 600px; margin: 0 auto; text-align: center;">
    <div class="card-body">
        
        <div class="stagger-2" style="margin-bottom: 1.5rem;">
            <i class='bx bxs-check-circle' style="font-size: 5rem; color: var(--abs-primary);"></i>
        </div>
        
        <h1 class="card-title stagger-2" style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; color: #1e293b;">
            Absensi Berhasil!
        </h1>
        <p class="text-muted stagger-2" style="margin-bottom: 2.5rem; font-size: 1.1rem;">
            Terima kasih, data kehadiran Anda telah tersimpan.
        </p>
        
        <div class="data-list stagger-3" style="text-align: left;">
            <div class="data-item">
                <span class="data-label">Nama Pegawai</span>
                <span class="data-value"><?= e($absensi['nama_lengkap']) ?></span>
            </div>
            
            <div class="data-item">
                <span class="data-label">NIP</span>
                <span class="data-value"><?= e($absensi['nip']) ?></span>
            </div>
            
            <div class="data-item">
                <span class="data-label">Jabatan</span>
                <span class="data-value" style="font-weight: 600; color: var(--abs-primary-dark);"><?= e($absensi['nama_jabatan'] ?? '-') ?></span>
            </div>
            
            <div class="data-item">
                <span class="data-label">Tim Kerja</span>
                <span class="data-value" style="font-size: 0.95rem; color: #64748b;"><?= e($absensi['nama_tim_kerja'] ?? '-') ?></span>
            </div>
            
            <hr style="border: 0; border-top: 1px dashed rgba(0,0,0,0.1); margin: 1rem 0;">
            
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
                <div style="margin-top: 0.25rem;">
                    <?php if (isset($absensi['status_kehadiran']) && $absensi['status_kehadiran'] === 'Tidak Hadir'): ?>
                        <div class="lokasi-alert lokasi-fail" style="display: inline-flex; padding: 0.5rem 1rem; flex-direction: row; align-items: center; gap: 0.5rem;">
                            <i class='bx bxs-x-circle' style="font-size: 1.25rem;"></i>
                            <span style="font-weight: 700;">Tidak Hadir</span>
                        </div>
                    <?php else: ?>
                        <div class="lokasi-alert lokasi-ok" style="display: inline-flex; padding: 0.5rem 1rem; flex-direction: row; align-items: center; gap: 0.5rem;">
                            <i class='bx bxs-check-circle' style="font-size: 1.25rem;"></i>
                            <span style="font-weight: 700;">Hadir</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="stagger-4" style="margin-top: 2rem;">
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1rem;">
                Halaman akan dialihkan dalam <span id="countdown" style="font-weight: 700; color: var(--abs-primary);">15</span> detik...
            </p>
            
            <a href="<?= url('absensi?kegiatan=' . $absensi['kode_kegiatan']) ?>" class="btn btn-primary" id="btnBack" style="width: 100%;">
                <i class='bx bx-left-arrow-alt'></i> Kembali ke Formulir
            </a>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
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
