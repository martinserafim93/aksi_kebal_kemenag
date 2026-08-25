<?php
ob_start();
?>

<div class="card" >
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Tambah Tim Kerja</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Buat data tim kerja baru.</p>
        </div>
        <div>
            <a href="<?= url('admin/tim-kerja') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Form Data Tim Kerja</h3>
    </div>
    <div class="card-body">
        <form action="<?= url('admin/tim-kerja-create') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Middleware::generateCsrfToken() ?>">
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="nama_tim_kerja" class="form-label">Nama Tim Kerja <span style="color: red;">*</span></label>
                <input type="text" id="nama_tim_kerja" name="nama_tim_kerja" class="form-control"  required autofocus placeholder="Contoh: Tim Pelayanan">
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-gradient-success">
                    <i class='bx bx-save'></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
