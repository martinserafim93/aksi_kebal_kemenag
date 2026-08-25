<?php
ob_start();
?>

<div class="card" >
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Manajemen Jabatan</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Kelola data referensi Jabatan untuk penugasan pegawai.</p>
        </div>
        <div>
            <a href="<?= url('admin/jabatan-create') ?>" class="btn btn-gradient-primary">
                <i class='bx bx-plus'></i> Tambah Jabatan
            </a>
        </div>
    </div>
</div>

<div class="card" >
    <div class="card-header" >
        <h3 class="card-title" style="font-weight: 600;">Daftar Jabatan</h3>
        
        <form action="<?= url('admin/jabatan') ?>" method="GET"
            style="display: flex; gap: 0.5rem; align-items: center;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class='bx bx-search' style="position: absolute; left: 1rem; color: #94a3b8; font-size: 1.1rem;"></i>
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari Jabatan..."
                    class="form-control"
                    style="width: 250px; padding: 0.6rem 1.2rem 0.6rem 2.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; outline: none; transition: all 0.3s ease; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);"
                    onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(37, 99, 235, 0.2)';"
                    onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';">
            </div>
            <button type="submit" class="btn btn-gradient-primary"
                style="padding: 0.6rem 1.2rem; border-radius: 9999px; font-weight: 500;">
                Cari
            </button>
            <?php if (!empty($search)): ?>
                <a href="<?= url('admin/jabatan') ?>" class="btn"
                    style="padding: 0.6rem 1.2rem; border-radius: 9999px; font-weight: 500; background: #f1f5f9; color: #475569;">
                    Reset
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="card-body" >
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--content-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 60px;">No</th>
                        <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Nama Jabatan</th>
                        <th style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Jumlah Pegawai</th>
                        <th style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jabatan)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Tidak ada data jabatan ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($jabatan as $j): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">
                                    <?= $no++ ?>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 600; color: var(--text-main); font-size: 1rem;"><?= e($j['nama_jabatan']) ?></div>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #eff6ff; color: #1d4ed8; font-size: 0.85rem; border-radius: 999px; font-weight: 600;">
                                        <?= e($j['jumlah_pegawai']) ?> Pegawai
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="<?= url('admin/jabatan-edit/' . urlencode($j['slug_jabatan'])) ?>" class="btn" style="background: #eff6ff; color: #1d4ed8; padding: 0.4rem 0.6rem; border-radius: 0.25rem;" title="Edit">
                                            <i class='bx bx-edit-alt'></i>
                                        </a>
                                        
                                        <form action="<?= url('admin/jabatan-delete/' . urlencode($j['slug_jabatan'])) ?>" method="POST" class="form-delete" style="display: inline-block;">
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
    
    <!-- Pagination -->
    <?php if ($total_page > 1): ?>
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding: 1.5rem;">
            <span style="color: var(--text-muted); font-size: 0.9rem;">
                Halaman <?= $page ?> dari <?= $total_page ?>
            </span>
            <div style="display: flex; gap: 0.25rem; align-items: center;">
                <!-- Tombol Sebelumnya -->
                <?php if ($page > 1): ?>
                    <a href="<?= url('admin/jabatan?page=' . ($page - 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>"
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
                    <a href="<?= url('admin/jabatan?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>"
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
                    <a href="<?= url('admin/jabatan?page=' . ($page + 1) . (!empty($search) ? '&search=' . urlencode($search) : '')) ?>"
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
