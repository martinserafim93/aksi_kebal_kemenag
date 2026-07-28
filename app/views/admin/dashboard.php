<?php
/**
 * Konten Dashboard Admin
 */
ob_start();
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Selamat Datang di AKSI KEBAL</h3>
    </div>
    <div class="card-body">
        <div style="display: flex; align-items: flex-start; gap: 1.5rem;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), #14b8a6); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white;">
                <i class='bx bx-home-smile'></i>
            </div>
            <div>
                <h4 style="margin-bottom: 0.5rem; font-size: 1.25rem;">Halo, <?= e(adminData('nama_lengkap')) ?>!</h4>
                <p style="color: var(--text-muted); margin-bottom: 1rem;">
                    Anda masuk sebagai <strong><?= e(adminData('role')) ?></strong> (<?= e(adminData('nama_jabatan')) ?>).
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div style="background: #f1f5f9; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">NIP</div>
                        <div style="font-weight: 500; margin-top: 0.25rem;"><?= e(adminData('nip')) ?></div>
                    </div>
                    <div style="background: #f1f5f9; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Tim Kerja</div>
                        <div style="font-weight: 500; margin-top: 0.25rem;"><?= e(adminData('nama_tim_kerja') ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <p style="font-size: 0.85rem; color: var(--text-muted);">
                <i class='bx bx-info-circle'></i> Ini adalah halaman dashboard sementara. Grafik dan statistik lengkap akan ditambahkan pada issue berikutnya.
            </p>
        </div>
    </div>
</div>

<?php
// Simpan output buffer ke variabel $content
$content = ob_get_clean();

// Panggil layout utama
require_once __DIR__ . '/layouts/main.php';
