<?php
ob_start();
?>

<div class="card" >
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Tambah Pegawai Baru</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Masukkan data profil dan akses untuk pegawai baru.</p>
        </div>
        <div>
            <a href="<?= url('admin/pegawai') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Form Data Pegawai</h3>
    </div>
    <div class="card-body">
        <form action="<?= url('admin/pegawai-create') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Middleware::generateCsrfToken() ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="nip" class="form-label">NIP <span style="color: red;">*</span></label>
                    <input type="text" id="nip" name="nip" class="form-control"  required>
                </div>
                
                <div class="form-group">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span style="color: red;">*</span></label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"  required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="id_unit_kerja" class="form-label">Unit Kerja</label>
                    <select id="id_unit_kerja" name="id_unit_kerja" class="form-control" >
                        <option value="">-- Pilih Unit Kerja --</option>
                        <?php foreach ($unit_kerja as $u): ?>
                            <option value="<?= $u['id_unit_kerja'] ?>"><?= e($u['nama_unit_kerja']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_jabatan" class="form-label">Jabatan</label>
                    <select id="id_jabatan" name="id_jabatan" class="form-control" >
                        <option value="">-- Pilih Jabatan --</option>
                        <?php foreach ($jabatan as $j): ?>
                            <option value="<?= $j['id_jabatan'] ?>"><?= e($j['nama_jabatan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_tim_kerja" class="form-label">Tim Kerja</label>
                    <select id="id_tim_kerja" name="id_tim_kerja" class="form-control" >
                        <option value="">-- Pilih Tim Kerja --</option>
                        <?php foreach ($tim_kerja as $t): ?>
                            <option value="<?= $t['id_tim_kerja'] ?>"><?= e($t['nama_tim_kerja']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="email" class="form-label">Email Pribadi / Kantor</label>
                    <input type="email" id="email" name="email" class="form-control" >
                </div>
            </div>

            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 2rem 0;">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="role" class="form-label">Hak Akses (Role)</label>
                    <select id="role" name="role" class="form-control"  onchange="togglePassword(this.value)">
                        <option value="pegawai">Pegawai Biasa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div class="form-group" id="password_group" style="display: none;">
                    <label for="password" class="form-label">Password Akun <span style="color: red;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control"  minlength="6">
                    <small style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-top: 0.25rem;">Minimal 6 karakter.</small>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-gradient-success">
                    <i class='bx bx-save'></i> Simpan Pegawai
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();

ob_start();
?>
<script>
    function togglePassword(role) {
        const passGroup = document.getElementById('password_group');
        const passInput = document.getElementById('password');
        if (role === 'admin') {
            passGroup.style.display = 'block';
            passInput.required = true;
        } else {
            passGroup.style.display = 'none';
            passInput.required = false;
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        togglePassword(document.getElementById('role').value);
    });
</script>
<?php
$extra_js = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
