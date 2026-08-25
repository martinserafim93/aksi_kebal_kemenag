<?php
ob_start();
?>

<div class="card" >
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Manajemen Pegawai</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Kelola data pegawai,
                jabatan, dan akses sistem.</p>
        </div>
        <div>
            <a href="<?= url('admin/pegawai-create') ?>" class="btn btn-gradient-primary">
                <i class='bx bx-plus'></i> Tambah Pegawai
            </a>
        </div>
    </div>
</div>

<div class="card" >
    <div class="card-header"
        >
        <h3 class="card-title" style="font-weight: 600;">Daftar Pegawai</h3>

        <form action="<?= url('admin/pegawai') ?>" method="GET"
            style="display: flex; gap: 0.5rem; align-items: center;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class='bx bx-search' style="position: absolute; left: 1rem; color: #94a3b8; font-size: 1.1rem;"></i>
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari NIP atau Nama..."
                    class="form-control"
                    style="width: 300px; padding: 0.6rem 1.2rem 0.6rem 2.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; outline: none; transition: all 0.3s ease; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                    onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.2)';"
                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';">
            </div>
            <button type="submit" class="btn btn-gradient-primary"
                style="padding: 0.6rem 1.2rem; border-radius: 9999px; font-weight: 500;">
                Cari
            </button>
            <?php if (!empty($search)): ?>
                <a href="<?= url('admin/pegawai') ?>" class="btn"
                    style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.6rem 1.2rem; border-radius: 9999px;">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body" >
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--content-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th
                            style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 60px;">
                            No</th>
                        <th
                            style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                            NIP / Nama</th>
                        <th
                            style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                            Jabatan, Tim & Unit Kerja</th>
                        <th
                            style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                            Kontak</th>
                        <th
                            style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pegawai)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Tidak ada data pegawai ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = ($page - 1) * 10 + 1; ?>
                        <?php foreach ($pegawai as $p): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; text-align: center; color: var(--text-muted); font-weight: 500;">
                                    <?= $no++ ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 600; color: var(--text-main);"><?= e($p['nama_lengkap']) ?></div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">NIP:
                                        <?= e($p['nip']) ?>
                                    </div>
                                    <?php if ($p['role'] === 'admin'): ?>
                                        <span
                                            style="display: inline-block; padding: 0.15rem 0.5rem; background: #fee2e2; color: #991b1b; font-size: 0.7rem; border-radius: 999px; margin-top: 0.25rem; font-weight: 600;">ADMIN</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; font-size: 0.95rem;">
                                    <div style="color: var(--text-main); font-weight: 500;"><?= e($p['nama_jabatan'] ?? '-') ?>
                                    </div>
                                    <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                                        <i class='bx bx-group'></i> <?= e($p['nama_tim_kerja'] ?? '-') ?>
                                    </div>
                                    <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">
                                        <i class='bx bx-building'></i> <?= e($p['nama_unit_kerja'] ?? '-') ?>
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-size: 0.95rem; color: var(--text-muted);">
                                    <?= e($p['email'] ?? '-') ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="<?= url('admin/pegawai-edit/' . urlencode($p['nip'])) ?>" class="btn"
                                            style="background: #eff6ff; color: #1d4ed8; padding: 0.4rem 0.6rem; border-radius: 0.25rem;"
                                            title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </a>

                                        <?php if ($p['nip'] !== adminData('nip')): ?>
                                            <form action="<?= url('admin/pegawai-delete/' . urlencode($p['nip'])) ?>" method="POST"
                                                class="form-delete" style="display: inline-block;">
                                                <?= csrfField() ?>
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_page > 1): ?>
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding: 1.5rem;">
            <span style="color: var(--text-muted); font-size: 0.9rem;">
                Halaman <?= $page ?> dari <?= $total_page ?>
            </span>
            <div style="display: flex; gap: 0.25rem; align-items: center;">
                <!-- Tombol Sebelumnya -->
                <?php if ($page > 1): ?>
                    <a href="<?= url('admin/pegawai?page=' . ($page - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>"
                        style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 500; background: #f1f5f9; color: #475569;">
                        &laquo; Sebelumnya
                    </a>
                <?php else: ?>
                    <span style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; font-size: 0.9rem; font-weight: 500; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;">
                        &laquo; Sebelumnya
                    </span>
                <?php endif; ?>

                <!-- Angka Halaman -->
                <?php
                $start = max(1, $page - 2);
                $end = min($total_page, $page + 2);
                
                if ($start > 1) {
                    echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="<?= url('admin/pegawai?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>"
                        style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 500; 
                          <?= $i === $page ? 'background: var(--primary); color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; 
                
                if ($end < $total_page) {
                    echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>';
                }
                ?>

                <!-- Tombol Selanjutnya -->
                <?php if ($page < $total_page): ?>
                    <a href="<?= url('admin/pegawai?page=' . ($page + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>"
                        style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 500; background: #f1f5f9; color: #475569;">
                        Selanjutnya &raquo;
                    </a>
                <?php else: ?>
                    <span style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; font-size: 0.9rem; font-weight: 500; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;">
                        Selanjutnya &raquo;
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
