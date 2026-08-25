<?php
ob_start();
?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Edit Unit Kerja</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Ubah data unit kerja.</p>
        </div>
        <div>
            <a href="<?= url('admin/unit-kerja') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Form Data Unit Kerja</h3>
    </div>
    <div class="card-body">
        <form action="<?= url('admin/unit-kerja-edit/' . $unit_kerja['id_unit_kerja']) ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Middleware::generateCsrfToken() ?>">
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="nama_unit_kerja" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Nama Unit Kerja <span style="color: red;">*</span></label>
                <input type="text" id="nama_unit_kerja" name="nama_unit_kerja" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" value="<?= e($unit_kerja['nama_unit_kerja']) ?>" required autofocus>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-gradient-success">
                    <i class='bx bx-save'></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
