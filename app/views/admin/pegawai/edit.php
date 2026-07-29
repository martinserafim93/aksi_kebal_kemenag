<?php
ob_start();
?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Edit Data Pegawai</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Perbarui profil atau akses sistem untuk pegawai <strong><?= e($pegawai['nama_lengkap']) ?></strong>.</p>
        </div>
        <div>
            <a href="<?= url('admin/pegawai') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <div class="card-header">
        <h3 class="card-title">Form Edit Pegawai</h3>
    </div>
    <div class="card-body">
        <form action="<?= url('admin/pegawai-edit/' . urlencode($pegawai['nip'])) ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Middleware::generateCsrfToken() ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="nip" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">NIP <span style="color: red;">*</span></label>
                    <input type="text" id="nip" name="nip" value="<?= e($pegawai['nip']) ?>" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>
                
                <div class="form-group">
                    <label for="nama_lengkap" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Nama Lengkap <span style="color: red;">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= e($pegawai['nama_lengkap']) ?>" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="id_jabatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Jabatan</label>
                    <select id="id_jabatan" name="id_jabatan" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <option value="">-- Pilih Jabatan --</option>
                        <?php foreach ($jabatan as $j): ?>
                            <option value="<?= $j['id_jabatan'] ?>" <?= ($pegawai['id_jabatan'] == $j['id_jabatan']) ? 'selected' : '' ?>><?= e($j['nama_jabatan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_tim_kerja" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Tim Kerja</label>
                    <select id="id_tim_kerja" name="id_tim_kerja" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <option value="">-- Pilih Tim Kerja --</option>
                        <?php foreach ($tim_kerja as $t): ?>
                            <option value="<?= $t['id_tim_kerja'] ?>" <?= ($pegawai['id_tim_kerja'] == $t['id_tim_kerja']) ? 'selected' : '' ?>><?= e($t['nama_tim_kerja']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Email Pribadi / Kantor</label>
                    <input type="email" id="email" name="email" value="<?= e($pegawai['email']) ?>" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                </div>
            </div>

            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2rem 0;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="role" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Hak Akses (Role)</label>
                    <select id="role" name="role" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <option value="pegawai" <?= ($pegawai['role'] === 'pegawai') ? 'selected' : '' ?>>Pegawai Biasa</option>
                        <option value="admin" <?= ($pegawai['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Ubah Password</label>
                    <input type="password" id="password" name="password" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" minlength="6">
                    <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 0.25rem;">Biarkan kosong jika tidak ingin mengubah password.</small>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-gradient-success">
                    <i class='bx bx-save'></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
