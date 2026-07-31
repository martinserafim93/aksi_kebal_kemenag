<?php ob_start(); ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Manajemen Absensi</h2>
        <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Melihat, memverifikasi, dan mengoreksi data absensi pegawai.</p>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <form action="<?= url('admin/absensi') ?>" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="kegiatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Kegiatan</label>
                <select name="kegiatan" id="kegiatan" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                    <option value="">Semua Kegiatan</option>
                    <?php foreach ($kegiatan_list as $k): ?>
                        <option value="<?= $k['id_kegiatan'] ?>" <?= (int)$filters['kegiatan'] === (int)$k['id_kegiatan'] ? 'selected' : '' ?>>
                            <?= e($k['nama_kegiatan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label for="jenis" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Jenis Kegiatan</label>
                <select name="jenis" id="jenis" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
                    <option value="">Semua Jenis</option>
                    <option value="Kerja Bakti" <?= $filters['jenis'] === 'Kerja Bakti' ? 'selected' : '' ?>>Kerja Bakti</option>
                    <option value="Doa Bersama" <?= $filters['jenis'] === 'Doa Bersama' ? 'selected' : '' ?>>Doa Bersama</option>
                    <option value="Apel" <?= $filters['jenis'] === 'Apel' ? 'selected' : '' ?>>Apel</option>
                    <option value="Rapat" <?= $filters['jenis'] === 'Rapat' ? 'selected' : '' ?>>Rapat</option>
                    <option value="Sosialisasi" <?= $filters['jenis'] === 'Sosialisasi' ? 'selected' : '' ?>>Sosialisasi</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 150px;">
                <label for="tanggal" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="<?= e($filters['tanggal']) ?>" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.375rem;">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                    <i class='bx bx-filter'></i> Filter
                </button>
                <?php if (!empty($filters['kegiatan']) || !empty($filters['jenis']) || !empty($filters['tanggal'])): ?>
                    <a href="<?= url('admin/absensi') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main);">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 200px; margin-bottom: 0;">
        <div class="card-body" style="text-align: center;">
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Total Data</h3>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-main); margin-top: 0.5rem;"><?= $statistik['total'] ?></div>
        </div>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; margin-bottom: 0; border-bottom: 4px solid #10b981;">
        <div class="card-body" style="text-align: center;">
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Hadir</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #10b981; margin-top: 0.5rem;"><?= $statistik['hadir'] ?></div>
        </div>
    </div>
    <div class="card" style="flex: 1; min-width: 200px; margin-bottom: 0; border-bottom: 4px solid #ef4444;">
        <div class="card-body" style="text-align: center;">
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Tidak Hadir</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #ef4444; margin-top: 0.5rem;"><?= $statistik['tidak_hadir'] ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Daftar Absensi</h3>
        <div style="display: flex; gap: 0.5rem;">
            <!-- Export buttons will be added here in Issue #11 -->
        </div>
    </div>
    
    <div class="table-responsive" style="border-radius: 0.5rem; overflow: hidden; box-shadow: 0 0 0 1px var(--border-color); overflow-x: auto;">
        <table class="table table-hover" style="width: 100%; border-collapse: collapse; background: #fff;">
            <thead style="background: #f8fafc; border-bottom: 2px solid var(--border-color);">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">No</th>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Pegawai</th>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Kegiatan</th>
                    <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Kehadiran</th>
                    <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Foto</th>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Waktu Submit</th>
                    <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($absensi)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">Tidak ada data absensi ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $no = ($page - 1) * 10 + 1;
                    foreach ($absensi as $a): 
                    ?>
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;">
                            <td style="padding: 1rem; vertical-align: middle;"><?= $no++ ?></td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                <div style="font-weight: 600; color: var(--text-main);"><?= e($a['nama_lengkap']) ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">NIP: <?= e($a['nip']) ?></div>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                <div style="font-weight: 500; color: var(--text-main);"><?= e($a['nama_kegiatan']) ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;"><?= date('d M Y', strtotime($a['tanggal_kegiatan'])) ?></div>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                <?php if ($a['status_kehadiran'] === 'Hadir'): ?>
                                    <span class="badge" style="background: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">Hadir</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">Tidak Hadir</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                <?php if (!empty($a['foto'])): ?>
                                    <img src="<?= url('uploads/' . $a['foto']) ?>" alt="Foto Absensi" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem; cursor: pointer; border: 1px solid var(--border-color);" onclick="showImageModal(this.src)">
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-size: 0.9rem;">
                                <?= date('d M Y, H:i', strtotime($a['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; gap: 0.35rem; justify-content: center; flex-wrap: wrap;">
                                    <a href="<?= url('admin/absensi-edit/' . $a['id_absensi']) ?>" class="btn btn-warning" title="Edit/Koreksi" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <i class='bx bx-edit'></i>
                                    </a>
                                    
                                    <form action="<?= url('admin/absensi-delete/' . $a['id_absensi']) ?>" method="POST" class="form-delete" style="display: inline-block;">
                                        <input type="hidden" name="csrf_token" value="<?= e(Middleware::generateCsrfToken()) ?>">
                                        <button type="submit" class="btn btn-danger" title="Hapus Data" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
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
    
    <!-- Pagination -->
    <?php if ($total_page > 1): ?>
    <div class="card-body" style="border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
        <div style="display: flex; gap: 0.25rem;">
            <?php 
                $queryString = '';
                if (!empty($filters['kegiatan'])) $queryString .= '&kegiatan=' . urlencode($filters['kegiatan']);
                if (!empty($filters['jenis'])) $queryString .= '&jenis=' . urlencode($filters['jenis']);
                if (!empty($filters['tanggal'])) $queryString .= '&tanggal=' . urlencode($filters['tanggal']);
            ?>
            <?php for ($i = 1; $i <= $total_page; $i++): ?>
                <a href="<?= url('admin/absensi?page=' . $i . $queryString) ?>" 
                   style="padding: 0.5rem 0.75rem; border-radius: 0.25rem; text-decoration: none; font-size: 0.9rem; font-weight: 500; 
                          <?= $i === $page ? 'background: var(--primary); color: white;' : 'background: #f1f5f9; color: #475569;' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Lightbox Modal -->
<div id="imageModal" class="modal" style="display: none; position: fixed; z-index: 1000; padding-top: 50px; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.9);">
    <span class="close" style="position: absolute; top: 15px; right: 35px; color: #f1f1f1; font-size: 40px; font-weight: bold; cursor: pointer;" onclick="closeImageModal()">&times;</span>
    <img class="modal-content" id="img01" style="margin: auto; display: block; max-width: 90%; max-height: 90vh; border-radius: 0.5rem;">
</div>

<?php ob_start(); ?>
<script>
    function showImageModal(src) {
        var modal = document.getElementById("imageModal");
        var modalImg = document.getElementById("img01");
        modal.style.display = "block";
        modalImg.src = src;
    }

    function closeImageModal() {
        var modal = document.getElementById("imageModal");
        modal.style.display = "none";
    }

    // Close modal when clicking outside the image
    window.onclick = function(event) {
        var modal = document.getElementById("imageModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<style>
    .table-hover tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .img-thumbnail:hover {
        opacity: 0.8;
        transform: scale(1.05);
        transition: all 0.2s ease-in-out;
    }
</style>
<?php 
$extra_js = ob_get_clean(); 
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
