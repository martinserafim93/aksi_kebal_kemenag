<?php ob_start(); ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Manajemen Kegiatan</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Kelola data kegiatan, publikasi absensi, dan QR Code.</p>
        </div>
        <div>
            <a href="<?= url('admin/kegiatan-create') ?>" class="btn btn-gradient-primary">
                <i class='bx bx-plus'></i> Tambah Kegiatan
            </a>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid var(--border-color); border-radius: 0.75rem 0.75rem 0 0; padding: 1.25rem 1.5rem;">
        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;"><i class='bx bx-filter-alt'></i> Filter Pencarian</h4>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form action="<?= url('admin/kegiatan') ?>" method="GET" style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 250px;">
                <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.95rem; color: #475569;">Nama Kegiatan</label>
                <div style="position: relative;">
                    <i class='bx bx-search' style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem;"></i>
                    <input type="text" name="search" id="search" value="<?= e($search) ?>" placeholder="Masukkan kata kunci..." class="form-control" style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';">
                </div>
            </div>
            <div class="form-group" style="flex: 1; min-width: 180px;">
                <label for="status" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.95rem; color: #475569;">Status</label>
                <select name="status" id="status" class="form-control" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                    <option value="">Semua Status</option>
                    <option value="Draft" <?= $status === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Published" <?= $status === 'Published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 180px;">
                <label for="jenis" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.95rem; color: #475569;">Jenis Kegiatan</label>
                <select name="jenis" id="jenis" class="form-control" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s; cursor: pointer;">
                    <option value="">Semua Jenis</option>
                    <option value="Kerja Bakti" <?= $jenis === 'Kerja Bakti' ? 'selected' : '' ?>>Kerja Bakti</option>
                    <option value="Doa Bersama" <?= $jenis === 'Doa Bersama' ? 'selected' : '' ?>>Doa Bersama</option>
                    <option value="Apel" <?= $jenis === 'Apel' ? 'selected' : '' ?>>Apel</option>
                    <option value="Rapat" <?= $jenis === 'Rapat' ? 'selected' : '' ?>>Rapat</option>
                    <option value="Sosialisasi" <?= $jenis === 'Sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
                </select>
            </div>
            <div class="form-group" style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn btn-gradient-primary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <i class='bx bx-filter'></i> Terapkan
                </button>
                <?php if (!empty($search) || !empty($status) || !empty($jenis)): ?>
                    <a href="<?= url('admin/kegiatan') ?>" class="btn" style="background: white; border: 1.5px solid #e2e8f0; color: #64748b; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; text-decoration: none;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='white'; this.style.color='#64748b'; this.style.borderColor='#e2e8f0';">
                        <i class='bx bx-reset'></i> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Daftar Kegiatan</h3>
    </div>
    
    <div class="table-responsive" style="border-radius: 0.5rem; overflow: hidden; box-shadow: 0 0 0 1px var(--border-color);">
        <table class="table table-hover" style="width: 100%; border-collapse: collapse; background: #fff;">
            <thead style="background: #f8fafc; border-bottom: 2px solid var(--border-color);">
                <tr>
                    <th width="5%" style="padding: 1.25rem 1rem; font-weight: 600; text-align: center;">No</th>
                    <th width="20%" style="padding: 1.25rem 1rem; font-weight: 600;">Nama Kegiatan</th>
                    <th width="15%" style="padding: 1.25rem 1rem; font-weight: 600;">Jenis</th>
                    <th width="15%" style="padding: 1.25rem 1rem; font-weight: 600;">Waktu</th>
                    <th width="15%" style="padding: 1.25rem 1rem; font-weight: 600;">Lokasi</th>
                    <th width="10%" style="padding: 1.25rem 1rem; font-weight: 600;">Status</th>
                    <th width="20%" style="padding: 1.25rem 1rem; font-weight: 600; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kegiatan)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">Tidak ada data kegiatan ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($kegiatan as $k): ?>
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;">
                            <td style="padding: 1.25rem 1rem; vertical-align: middle; text-align: center; font-weight: 500; color: var(--text-muted);"><?= $no++ ?></td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle;"><strong><?= e($k['nama_kegiatan']) ?></strong></td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle;">
                                <span style="background: #f1f5f9; padding: 0.35rem 0.6rem; border-radius: 0.25rem; font-size: 0.85rem; color: #475569; font-weight: 500;"><?= e($k['jenis_kegiatan']) ?></span>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle;">
                                <div style="font-weight: 500;"><?= date('d M Y', strtotime($k['tanggal_kegiatan'])) ?></div>
                                <small style="color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;"><i class='bx bx-time-five'></i> <?= date('H:i', strtotime($k['waktu_mulai'])) ?> - <?= date('H:i', strtotime($k['waktu_selesai'])) ?></small>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle;"><div style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= e($k['lokasi_kegiatan']) ?>"><i class='bx bx-map'></i> <?= e($k['lokasi_kegiatan']) ?></div></td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle;">
                                <?php if ($k['status_kegiatan'] === 'Published'): ?>
                                    <span class="badge" style="background: #dcfce7; color: #166534; padding: 0.35rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Published</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #fef08a; color: #854d0e; padding: 0.35rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; justify-content: center;">
                                    <?php if ($k['status_kegiatan'] === 'Draft'): ?>
                                        <a href="<?= url('admin/kegiatan-edit/' . $k['id_kegiatan']) ?>" class="btn btn-warning" title="Edit Kegiatan" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class='bx bx-edit'></i>
                                        </a>
                                        <form action="<?= url('admin/kegiatan-publish/' . $k['id_kegiatan']) ?>" method="POST" class="form-publish" style="display: inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?= e(Middleware::generateCsrfToken()) ?>">
                                            <button type="submit" class="btn btn-success" title="Publish Kegiatan" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                <i class='bx bx-broadcast'></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?= url('admin/kegiatan-qrcode/' . $k['kode_kegiatan']) ?>" class="btn btn-primary" title="Lihat QR Code" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class='bx bx-qr-scan'></i> QR
                                        </a>
                                    <?php endif; ?>
                                    
                                    <form action="<?= url('admin/kegiatan-delete/' . $k['id_kegiatan']) ?>" method="POST" class="form-delete" style="display: inline-block;">
                                        <input type="hidden" name="csrf_token" value="<?= e(Middleware::generateCsrfToken()) ?>">
                                        <button type="submit" class="btn btn-danger" title="Hapus Kegiatan" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php ob_start(); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form.form-publish').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var formEl = this;
            Swal.fire({
                title: 'Publish Kegiatan?',
                text: 'Setelah di-publish, status akan berubah dan QR Code Absensi akan otomatis dibuat!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="bx bx-broadcast"></i> Ya, Publish!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-popup-custom'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    formEl.submit();
                }
            });
        });
    });
});
</script>
<style>
    .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
    }
</style>
<?php 
$extra_js = ob_get_clean(); 
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
