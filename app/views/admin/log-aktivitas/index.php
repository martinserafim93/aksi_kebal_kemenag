<?php
/**
 * AKSI KEBAL - Halaman Log Aktivitas (audit trail)
 *
 * Variabel dari AdminController::log_aktivitas():
 * - $log, $search, $aksi, $modul, $tanggal, $page, $total_page, $total_data
 * - $modul_list, $aksi_list (untuk dropdown filter)
 */

// Label & warna badge per aksi
$aksiLabels = [
    'login'       => 'Login',
    'login_gagal' => 'Login Gagal',
    'logout'      => 'Logout',
    'tambah'      => 'Tambah',
    'ubah'        => 'Ubah',
    'hapus'       => 'Hapus',
    'publish'     => 'Publish',
    'ekspor'      => 'Ekspor',
    'absensi'     => 'Absensi',
];
$aksiColors = [
    'login'       => ['#dcfce7', '#166534'],
    'login_gagal' => ['#fee2e2', '#991b1b'],
    'logout'      => ['#e2e8f0', '#475569'],
    'tambah'      => ['#dbeafe', '#1e40af'],
    'ubah'        => ['#fef3c7', '#92400e'],
    'hapus'       => ['#fee2e2', '#991b1b'],
    'publish'     => ['#d1fae5', '#065f46'],
    'ekspor'      => ['#e0e7ff', '#3730a3'],
    'absensi'     => ['#cffafe', '#155e75'],
];
$modulLabels = [
    'auth'       => 'Autentikasi',
    'pegawai'    => 'Pegawai',
    'tim_kerja'  => 'Tim Kerja',
    'unit_kerja' => 'Unit Kerja',
    'jabatan'    => 'Jabatan',
    'kegiatan'   => 'Kegiatan',
    'absensi'    => 'Absensi',
];

// Query string filter aktif (untuk link pagination)
$filterParams = array_filter([
    'search'  => $search,
    'aksi'    => $aksi,
    'modul'   => $modul,
    'tanggal' => $tanggal,
], fn($v) => $v !== '' && $v !== null);
$filterQuery = http_build_query($filterParams);

ob_start();
?>

<div class="card">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Log Aktivitas</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">
                Jejak audit aktivitas admin dan pengisian absensi pegawai.
            </p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary); line-height: 1;"><?= number_format($total_data, 0, ',', '.') ?></div>
            <div style="color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">Total Catatan</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="font-weight: 600;">Daftar Aktivitas</h3>

        <form action="<?= url('admin/log-aktivitas') ?>" method="GET"
            style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; display: flex; align-items: center;">
                <i class='bx bx-search' style="position: absolute; left: 1rem; color: #94a3b8; font-size: 1.1rem;"></i>
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari deskripsi / aktor..."
                    class="form-control"
                    style="width: 240px; padding: 0.6rem 1.2rem 0.6rem 2.5rem; border-radius: 9999px; border: 1px solid #e2e8f0; outline: none;">
            </div>

            <select name="modul" class="form-control" style="padding: 0.6rem 1rem; border-radius: 9999px; border: 1px solid #e2e8f0;">
                <option value="">Semua Modul</option>
                <?php foreach ($modul_list as $m): ?>
                    <option value="<?= e($m) ?>" <?= $modul === $m ? 'selected' : '' ?>>
                        <?= e($modulLabels[$m] ?? ucfirst($m)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="aksi" class="form-control" style="padding: 0.6rem 1rem; border-radius: 9999px; border: 1px solid #e2e8f0;">
                <option value="">Semua Aksi</option>
                <?php foreach ($aksi_list as $a): ?>
                    <option value="<?= e($a) ?>" <?= $aksi === $a ? 'selected' : '' ?>>
                        <?= e($aksiLabels[$a] ?? ucfirst($a)) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="tanggal" value="<?= e($tanggal) ?>" class="form-control"
                style="padding: 0.6rem 1rem; border-radius: 9999px; border: 1px solid #e2e8f0;">

            <button type="submit" class="btn btn-gradient-primary"
                style="padding: 0.6rem 1.2rem; border-radius: 9999px; font-weight: 500;">
                Filter
            </button>
            <?php if (!empty($filterParams)): ?>
                <a href="<?= url('admin/log-aktivitas') ?>" class="btn"
                    style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.6rem 1.2rem; border-radius: 9999px;">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--content-bg); border-bottom: 1px solid var(--border-color);">
                    <tr>
                        <th style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 60px;">No</th>
                        <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 150px;">Waktu</th>
                        <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Aktor</th>
                        <th style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 120px;">Aksi</th>
                        <th style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; width: 120px;">Modul</th>
                        <th style="text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($log)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Tidak ada catatan aktivitas ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = ($page - 1) * 15 + 1; ?>
                        <?php foreach ($log as $row): ?>
                            <?php
                            [$bg, $fg] = $aksiColors[$row['aksi']] ?? ['#e2e8f0', '#475569'];
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 1rem; text-align: center; color: var(--text-muted); font-weight: 500;">
                                    <?= $no++ ?>
                                </td>
                                <td style="padding: 1rem; font-size: 0.9rem;">
                                    <div style="color: var(--text-main); font-weight: 500;">
                                        <?= e(date('d M Y', strtotime($row['created_at']))) ?>
                                    </div>
                                    <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.15rem;">
                                        <i class='bx bx-time-five'></i> <?= e(date('H:i', strtotime($row['created_at']))) ?> WITA
                                    </div>
                                </td>
                                <td style="padding: 1rem; font-size: 0.9rem;">
                                    <div style="color: var(--text-main); font-weight: 600;"><?= e($row['aktor_nama'] ?? '-') ?></div>
                                    <?php if (!empty($row['aktor_nip'])): ?>
                                        <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.15rem;">NIP: <?= e($row['aktor_nip']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['ip_address'])): ?>
                                        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.15rem;"><i class='bx bx-globe'></i> <?= e($row['ip_address']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.25rem 0.7rem; background: <?= $bg ?>; color: <?= $fg ?>; font-size: 0.75rem; border-radius: 999px; font-weight: 600; white-space: nowrap;">
                                        <?= e($aksiLabels[$row['aksi']] ?? ucfirst($row['aksi'])) ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                                    <?= e($modulLabels[$row['modul']] ?? ucfirst($row['modul'])) ?>
                                </td>
                                <td style="padding: 1rem; font-size: 0.9rem; color: var(--text-main);">
                                    <?= e($row['deskripsi']) ?>
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
                <?php $qs = $filterQuery ? $filterQuery . '&' : ''; ?>
                <?php if ($page > 1): ?>
                    <a href="<?= url('admin/log-aktivitas?' . $qs . 'page=' . ($page - 1)) ?>"
                        style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 500; background: #f1f5f9; color: #475569;">
                        &laquo; Sebelumnya
                    </a>
                <?php else: ?>
                    <span style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; font-size: 0.9rem; font-weight: 500; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;">
                        &laquo; Sebelumnya
                    </span>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($total_page, $page + 2);

                if ($start > 1) {
                    echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>';
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="<?= url('admin/log-aktivitas?' . $qs . 'page=' . $i) ?>"
                        style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 500;
                          <?= $i === $page ? 'background: var(--primary); color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor;

                if ($end < $total_page) {
                    echo '<span style="padding: 0.5rem; color: #94a3b8;">...</span>';
                }
                ?>

                <?php if ($page < $total_page): ?>
                    <a href="<?= url('admin/log-aktivitas?' . $qs . 'page=' . ($page + 1)) ?>"
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
