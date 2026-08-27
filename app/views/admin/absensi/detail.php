<?php ob_start(); ?>

<div class="card" style="border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; padding: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <a href="<?= url('admin/absensi') ?>" class="btn" style="background: #f1f5f9; color: #475569; padding: 0.35rem 0.6rem; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; text-decoration: none; font-weight: 500;">
                    <i class='bx bx-arrow-back'></i> Kembali
                </a>
            </div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);"><?= e($kegiatan['nama_kegiatan']) ?></h2>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.75rem;">
                <div style="font-size: 0.9rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-calendar-event'></i> <?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'])) ?>
                </div>
                <div style="font-size: 0.9rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-time-five'></i> <?= date('H:i', strtotime($kegiatan['waktu_mulai'])) ?> - <?= date('H:i', strtotime($kegiatan['waktu_selesai'])) ?>
                </div>
                <div style="font-size: 0.9rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.35rem;">
                    <i class='bx bx-map'></i> <?= e($kegiatan['lokasi_kegiatan']) ?>
                </div>
            </div>
        </div>
        <div>
            <span class="badge" style="background: #f1f5f9; color: #475569; padding: 0.5rem 1rem; border-radius: 9999px; font-weight: 600; font-size: 0.85rem;">
                <?= e($kegiatan['jenis_kegiatan']) ?>
            </span>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body" >
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Total Pegawai</h3>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-main); margin-top: 0.5rem;"><?= $statistik['total_pegawai'] ?></div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body" >
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Hadir</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #10b981; margin-top: 0.5rem;"><?= $statistik['hadir'] ?></div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body" >
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Tidak Hadir</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #ef4444; margin-top: 0.5rem;"><?= $statistik['tidak_hadir'] ?></div>
        </div>
    </div>
    <div class="card" style="margin-bottom: 0;">
        <div class="card-body" >
            <h3 style="margin: 0; color: var(--text-muted); font-size: 1rem; font-weight: 500;">Belum Absen</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-top: 0.5rem;"><?= $statistik['tidak_absen'] ?></div>
        </div>
    </div>
</div>

<div class="card" style="border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
        <h3 class="card-title" style="margin: 0;">Daftar Hadir Pegawai</h3>
        <div style="display: flex; gap: 0.5rem;">
            <div style="position: relative; display: inline-block;" class="pdf-dropdown-container">
                <button class="btn btn-danger" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; padding: 0.5rem 1rem; cursor: pointer;" onclick="document.getElementById('pdfDropdown').style.display = document.getElementById('pdfDropdown').style.display === 'none' ? 'block' : 'none'; event.stopPropagation();">
                    <i class='bx bxs-file-pdf'></i> Export PDF <i class='bx bx-chevron-down'></i>
                </button>
                <div id="pdfDropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid var(--border-color); border-radius: 0.5rem; min-width: 200px; z-index: 50; overflow: hidden;">
                    <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=semua') ?>" target="_blank" style="display: block; padding: 0.75rem 1rem; color: var(--text-main); text-decoration: none; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">
                        Semua Pegawai
                    </a>
                    <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=hadir') ?>" target="_blank" style="display: block; padding: 0.75rem 1rem; color: var(--text-main); text-decoration: none; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">
                        Pegawai Hadir
                    </a>
                    <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=tidak_hadir') ?>" target="_blank" style="display: block; padding: 0.75rem 1rem; color: var(--text-main); text-decoration: none; font-size: 0.85rem; border-bottom: 1px solid var(--border-color);">
                        Pegawai Tidak Hadir
                    </a>
                    <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=tidak_absen') ?>" target="_blank" style="display: block; padding: 0.75rem 1rem; color: var(--text-main); text-decoration: none; font-size: 0.85rem;">
                        Pegawai Tidak Absen
                    </a>
                </div>
            </div>
            <a href="<?= url('admin/absensi-export/' . $kegiatan['kode_kegiatan']) ?>" class="btn btn-success" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; padding: 0.5rem 1rem;">
                <i class='bx bx-export'></i> Export CSV
            </a>
        </div>
    </div>
    
    <div class="table-responsive" style="border-radius: 0.5rem; overflow: hidden; box-shadow: 0 0 0 1px var(--border-color); overflow-x: auto;">
        <table class="table table-hover" style="width: 100%; border-collapse: collapse; background: #fff;">
            <thead style="background: #f8fafc; border-bottom: 2px solid var(--border-color);">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">No</th>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Pegawai</th>
                    <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Kehadiran</th>
                    <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Foto</th>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Waktu Submit</th>
                    <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Alasan</th>
                    <th style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($absensi)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada data absensi untuk kegiatan ini.</td>
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
                            <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                <?php if ($a['status_kehadiran'] === 'Hadir'): ?>
                                    <span class="badge" style="background: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">Hadir</span>
                                <?php else: ?>
                                    <span class="badge" style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">Tidak Hadir</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                <?php if ($a['status_kehadiran'] === 'Tidak Hadir' && !empty($a['file_bukti'])): ?>
                                    <?php if ($a['tipe_file_bukti'] === 'pdf'): ?>
                                        <a href="<?= url('uploads/file_bukti/' . $a['file_bukti']) ?>" target="_blank" class="btn btn-sm" style="background: #fee2e2; color: #ef4444; border: 1px solid #fca5a5; font-size: 0.8rem; padding: 0.25rem 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none;">
                                            <i class='bx bxs-file-pdf'></i> PDF
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm" style="background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc; font-size: 0.8rem; padding: 0.25rem 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem;" onclick="showImageModal('<?= url('uploads/file_bukti/' . $a['file_bukti']) ?>')">
                                            <i class='bx bx-image'></i> Bukti
                                        </button>
                                    <?php endif; ?>
                                <?php elseif (!empty($a['foto'])): ?>
                                    <button type="button" class="btn btn-sm" style="background: #dcfce7; color: #166534; border: 1px solid #86efac; font-size: 0.8rem; padding: 0.25rem 0.5rem; display: inline-flex; align-items: center; gap: 0.25rem;" onclick="showImageModal('<?= url('uploads/foto_absensi/' . $a['foto']) ?>')">
                                        <i class='bx bx-camera'></i> Foto
                                    </button>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-size: 0.9rem;">
                                <?= date('d M Y, H:i', strtotime($a['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-size: 0.85rem; max-width: 200px;">
                                <?php if ($a['status_kehadiran'] === 'Tidak Hadir' && !empty($a['alasan_tidak_hadir'])): ?>
                                    <span style="color: var(--text-main);" title="<?= e($a['alasan_tidak_hadir']) ?>">
                                        <?= e(mb_strimwidth($a['alasan_tidak_hadir'], 0, 50, '...')) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; gap: 0.35rem; justify-content: center; flex-wrap: wrap;">
                                    <a href="<?= url('admin/absensi-edit/' . $a['kode_absensi']) ?>" class="btn btn-warning" title="Edit/Koreksi" style="padding: 0.35rem 0.6rem; font-size: 0.875rem; border-radius: 0.375rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        <i class='bx bx-edit-alt'></i>
                                    </a>
                                    
                                    <form action="<?= url('admin/absensi-delete/' . $a['kode_absensi']) ?>" method="POST" class="form-delete" style="display: inline-block;">
                                        <input type="hidden" name="csrf_token" value="<?= e(Middleware::generateCsrfToken()) ?>">
                                        <!-- Add redirect logic in delete if necessary, otherwise it goes back to referer -->
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
    <div class="card-body" >
        <div style="display: flex; gap: 0.25rem;">
            <?php for ($i = 1; $i <= $total_page; $i++): ?>
                <a href="<?= url('admin/absensi-detail/' . $kegiatan['kode_kegiatan'] . '?page=' . $i) ?>" 
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
<script>document.addEventListener("click", function(event) { var dropdown = document.getElementById("pdfDropdown"); if (dropdown && dropdown.style.display === "block" && !event.target.closest(".pdf-dropdown-container")) { dropdown.style.display = "none"; } });</script>
<?php 
$extra_js = ob_get_clean(); 
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
