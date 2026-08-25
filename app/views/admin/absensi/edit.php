<?php ob_start(); ?>

<div class="card" >
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Koreksi Absensi</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Ubah status kehadiran pegawai untuk kegiatan ini.</p>
        </div>
        <div>
            <a href="<?= url('admin/absensi-detail/' . $absensi['kode_kegiatan']) ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main);">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Data Absensi</h3>
    </div>
    <div class="card-body">
        <form action="<?= url('admin/absensi-edit/' . $absensi['kode_absensi']) ?>" method="POST">
            <?= csrfField() ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Pegawai</label>
                    <div style="padding: 0.75rem; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 0.375rem; color: var(--text-muted);">
                        <strong><?= e($absensi['nama_lengkap']) ?></strong> (NIP: <?= e($absensi['nip']) ?>)
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Kegiatan</label>
                    <div style="padding: 0.75rem; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 0.375rem; color: var(--text-muted);">
                        <strong><?= e($absensi['nama_kegiatan']) ?></strong><br>
                        <small><?= date('d M Y', strtotime($absensi['tanggal_kegiatan'])) ?> (<?= date('H:i', strtotime($absensi['waktu_mulai'])) ?> - <?= date('H:i', strtotime($absensi['waktu_selesai'])) ?>)</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Waktu Submit</label>
                    <div style="padding: 0.75rem; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 0.375rem; color: var(--text-muted);">
                        <?= date('d M Y, H:i:s', strtotime($absensi['created_at'])) ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status_kehadiran" class="form-label">Status Kehadiran <span style="color: #ef4444;">*</span></label>
                    <select name="status_kehadiran" id="status_kehadiran" class="form-control" required >
                        <option value="Hadir" <?= $absensi['status_kehadiran'] === 'Hadir' ? 'selected' : '' ?>>Hadir</option>
                        <option value="Tidak Hadir" <?= $absensi['status_kehadiran'] === 'Tidak Hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                    </select>
                </div>
                
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label">Bukti (Foto/File)</label>
                    <?php if ($absensi['status_kehadiran'] === 'Tidak Hadir' && !empty($absensi['file_bukti'])): ?>
                        <?php if ($absensi['tipe_file_bukti'] === 'pdf'): ?>
                            <div style="margin-top: 0.5rem; padding: 1rem; border: 1px dashed var(--border-color); border-radius: 0.5rem; background: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
                                <i class='bx bxs-file-pdf' style="font-size: 2rem; color: #ef4444;"></i>
                                <a href="<?= url('uploads/file_bukti/' . $absensi['file_bukti']) ?>" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                    Lihat File PDF Bukti
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 0.5rem; padding: 1rem; border: 1px dashed var(--border-color); border-radius: 0.5rem; text-align: center; background: #f8fafc;">
                                <a href="<?= url('uploads/file_bukti/' . $absensi['file_bukti']) ?>" target="_blank">
                                    <img src="<?= url('uploads/file_bukti/' . $absensi['file_bukti']) ?>" alt="Bukti Tidak Hadir" style="max-width: 100%; max-height: 300px; border-radius: 0.25rem; border: 1px solid var(--border-color);">
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php elseif (!empty($absensi['foto'])): ?>
                        <div style="margin-top: 0.5rem; padding: 1rem; border: 1px dashed var(--border-color); border-radius: 0.5rem; text-align: center; background: #f8fafc;">
                            <a href="<?= url('uploads/foto_absensi/' . $absensi['foto']) ?>" target="_blank">
                                <img src="<?= url('uploads/foto_absensi/' . $absensi['foto']) ?>" alt="Foto Absensi" style="max-width: 100%; max-height: 300px; border-radius: 0.25rem; border: 1px solid var(--border-color);">
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="padding: 1rem; border: 1px dashed var(--border-color); border-radius: 0.5rem; text-align: center; background: #f8fafc; color: var(--text-muted);">
                            <i>Tidak ada foto atau bukti yang diunggah.</i>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-gradient-primary" style="flex: 1;">
                    <i class='bx bx-save'></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
