<?php
ob_start();
?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Edit Tim Kerja</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Perbarui nama tim kerja.</p>
        </div>
        <div>
            <a href="<?= url('admin/tim-kerja') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <?php if ($flash['type'] === 'success'): ?>
        <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #34d399;">
            <?= $flash['message'] ?>
        </div>
    <?php elseif ($flash['type'] === 'error'): ?>
        <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #f87171;">
            <?= $flash['message'] ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3 class="card-title">Form Edit Tim Kerja</h3>
    </div>
    <div class="card-body">
        <form action="<?= url('admin/tim-kerja-edit/' . urlencode($tim_kerja['id_tim_kerja'])) ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Middleware::generateCsrfToken() ?>">
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="nama_tim_kerja" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Nama Tim Kerja <span style="color: red;">*</span></label>
                <input type="text" id="nama_tim_kerja" name="nama_tim_kerja" value="<?= e($tim_kerja['nama_tim_kerja']) ?>" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required autofocus>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                    <i class='bx bx-save'></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
