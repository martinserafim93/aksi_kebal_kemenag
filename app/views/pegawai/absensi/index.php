<?php ob_start(); ?>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div style="padding: 1rem; margin-bottom: 1rem; border-radius: 8px; 
                background: <?= $flash['type'] === 'error' ? '#fee2e2' : ($flash['type'] === 'success' ? '#dcfce7' : '#fef3c7') ?>;
                color: <?= $flash['type'] === 'error' ? '#991b1b' : ($flash['type'] === 'success' ? '#166534' : '#92400e') ?>;">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h1 class="card-title">Formulir Absensi</h1>
        <p class="text-muted" style="margin-bottom: 2rem;">Silakan isi formulir di bawah ini untuk mencatat kehadiran Anda.</p>

        <div class="data-list">
            <div class="data-item">
                <span class="data-label">Nama Kegiatan</span>
                <span class="data-value"><?= e($kegiatan['nama_kegiatan']) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Jenis Kegiatan</span>
                <span class="data-value"><?= e($kegiatan['jenis_kegiatan']) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Tanggal</span>
                <span class="data-value"><?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Waktu</span>
                <span class="data-value"><?= date('H:i', strtotime($kegiatan['waktu_mulai'])) ?> - <?= date('H:i', strtotime($kegiatan['waktu_selesai'])) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Lokasi</span>
                <span class="data-value"><?= e($kegiatan['lokasi_kegiatan']) ?></span>
            </div>
        </div>

        <form action="<?= url('absensi/submit') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_kegiatan" value="<?= e($kegiatan['id_kegiatan']) ?>">
            
            <div class="form-group">
                <label for="nip" class="form-label">Nama Lengkap <span style="color: var(--danger-color)">*</span></label>
                <select name="nip" id="nip" class="form-control" required onchange="fetchPegawaiData()">
                    <option value="">-- Pilih Nama Pegawai --</option>
                    <?php foreach ($pegawaiList as $pegawai): ?>
                        <option value="<?= e($pegawai['nip']) ?>"><?= e($pegawai['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">NIP</label>
                <input type="text" id="display_nip" class="form-control" readonly placeholder="NIP akan terisi otomatis">
            </div>

            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label">Jabatan</label>
                    <input type="text" id="display_jabatan" class="form-control" readonly placeholder="-">
                </div>
                <div>
                    <label class="form-label">Tim Kerja</label>
                    <input type="text" id="display_tim_kerja" class="form-control" readonly placeholder="-">
                </div>
            </div>

            <div class="form-group">
                <label for="foto" class="form-label">Upload Foto <span style="color: var(--danger-color)">*</span></label>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg, image/png" required onchange="previewImage(event)">
                <small class="text-muted" style="display: block; margin-top: 0.5rem; font-size: 0.85rem;">Format: JPG/PNG, Maksimal: 5MB.</small>
                
                <div id="imagePreviewContainer" style="display: none; margin-top: 1rem;">
                    <p style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">Preview:</p>
                    <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 250px; border-radius: 0.75rem; border: 2px dashed var(--border-color); padding: 0.25rem;">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">
                <i class='bx bx-send'></i> Submit Absensi
            </button>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<script>
    function fetchPegawaiData() {
        const nip = document.getElementById('nip').value;
        const displayNip = document.getElementById('display_nip');
        const displayJabatan = document.getElementById('display_jabatan');
        const displayTimKerja = document.getElementById('display_tim_kerja');

        if (!nip) {
            displayNip.value = '';
            displayJabatan.value = '';
            displayTimKerja.value = '';
            return;
        }

        fetch('<?= url('absensi/getPegawaiData') ?>?nip=' + nip)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    displayNip.value = res.data.nip;
                    displayJabatan.value = res.data.nama_jabatan;
                    displayTimKerja.value = res.data.nama_tim_kerja;
                } else {
                    alert(res.error || 'Terjadi kesalahan mengambil data pegawai.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan pada server.');
            });
    }

    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('imagePreviewContainer');
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            container.style.display = 'none';
        }
    }
</script>
<?php 
$extra_js = ob_get_clean();
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
