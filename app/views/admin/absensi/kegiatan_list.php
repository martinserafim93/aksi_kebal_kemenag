<?php ob_start(); ?>

<div class="card" style="margin-bottom: 2rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Manajemen Absensi</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Pilih kegiatan untuk melihat daftar absensi pegawai.</p>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid var(--border-color); border-radius: 0.75rem 0.75rem 0 0; padding: 1.25rem 1.5rem;">
        <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem;"><i class='bx bx-search-alt'></i> Cari Kegiatan</h4>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form action="<?= url('admin/absensi') ?>" method="GET" style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 250px;">
                <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.95rem; color: #475569;">Nama Kegiatan</label>
                <div style="position: relative;">
                    <i class='bx bx-search' style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem;"></i>
                    <input type="text" name="search" id="search" value="<?= e($search) ?>" placeholder="Masukkan kata kunci..." class="form-control" style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1.5px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;" onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)';">
                </div>
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
                <?php if (!empty($search) || !empty($jenis)): ?>
                    <a href="<?= url('admin/absensi') ?>" class="btn" style="background: white; border: 1.5px solid #e2e8f0; color: #64748b; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; text-decoration: none;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'; this.style.borderColor='#cbd5e1';" onmouseout="this.style.background='white'; this.style.color='#64748b'; this.style.borderColor='#e2e8f0';">
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
                    <th width="30%" style="padding: 1.25rem 1rem; font-weight: 600;">Nama Kegiatan</th>
                    <th width="15%" style="padding: 1.25rem 1rem; font-weight: 600;">Jenis</th>
                    <th width="20%" style="padding: 1.25rem 1rem; font-weight: 600;">Waktu & Lokasi</th>
                    <th width="10%" style="padding: 1.25rem 1rem; font-weight: 600;">Status</th>
                    <th width="20%" style="padding: 1.25rem 1rem; font-weight: 600; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kegiatan)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">Tidak ada data kegiatan ditemukan.</td>
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
                                <small style="color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem;"><i class='bx bx-map'></i> <?= e($k['lokasi_kegiatan']) ?></small>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle;">
                                <?php if ($k['status_kegiatan'] === 'Published'): ?>
                                    <span class="badge" style="background: #dcfce7; color: #166534; padding: 0.35rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Published</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #fef08a; color: #854d0e; padding: 0.35rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: middle; text-align: center;">
                                <a href="<?= url('admin/absensi-detail/' . $k['id_kegiatan']) ?>" class="btn btn-gradient-primary" style="padding: 0.6rem 1rem; font-size: 0.9rem; font-weight: 600; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px -1px rgba(37, 99, 235, 0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(37, 99, 235, 0.2)';">
                                    <i class='bx bx-list-ul' style="font-size: 1.1rem;"></i> Lihat Absensi
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
    }
</style>
<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
