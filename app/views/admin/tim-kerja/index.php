<?php
ob_start();
?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Manajemen Tim Kerja</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Kelola data referensi Tim Kerja untuk penugasan pegawai.</p>
        </div>
        <div>
            <a href="<?= url('admin/tim-kerja-create') ?>" class="btn btn-gradient-primary">
                <i class='bx bx-plus'></i> Tambah Tim Kerja
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Tim Kerja</h3>
    </div>
    
    <div class="card-body" style="padding: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center; padding: 1rem; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; width: 60px;">No</th>
                        <th style="text-align: left; padding: 1rem; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Tim Kerja</th>
                        <th style="text-align: center; padding: 1rem; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Jumlah Anggota</th>
                        <th style="text-align: center; padding: 1rem; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tim_kerja)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Tidak ada data tim kerja ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($tim_kerja as $t): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">
                                    <?= $no++ ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 1rem;"><?= e($t['nama_tim_kerja']) ?></div>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #eff6ff; color: #1d4ed8; font-size: 0.85rem; border-radius: 999px; font-weight: 600;">
                                        <?= e($t['jumlah_anggota']) ?> Pegawai
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="<?= url('admin/tim-kerja-edit/' . urlencode($t['id_tim_kerja'])) ?>" class="btn" style="background: #eff6ff; color: #1d4ed8; padding: 0.4rem 0.6rem; border-radius: 0.25rem;" title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </a>
                                        
                                        <form action="<?= url('admin/tim-kerja-delete/' . urlencode($t['id_tim_kerja'])) ?>" method="POST" class="form-delete" style="display: inline-block;">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
