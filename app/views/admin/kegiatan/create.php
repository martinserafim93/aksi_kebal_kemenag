<?php ob_start(); ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">Tambah Kegiatan</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Isi formulir di bawah ini untuk menambahkan kegiatan baru.</p>
        </div>
        <div>
            <a href="<?= url('admin/kegiatan') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main);">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-body">
        <form action="<?= url('admin/kegiatan-create') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(Middleware::generateCsrfToken()) ?>">

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="nama_kegiatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Nama Kegiatan <span style="color: red;">*</span></label>
                    <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="jenis_kegiatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Jenis Kegiatan <span style="color: red;">*</span></label>
                    <input list="jenis_kegiatan_options" id="jenis_kegiatan" name="jenis_kegiatan" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required placeholder="Pilih atau ketik jenis kegiatan...">
                    <datalist id="jenis_kegiatan_options">
                        <option value="Kerja Bakti">
                        <option value="Doa Bersama">
                        <option value="Apel">
                        <option value="Rapat">
                        <option value="Sosialisasi">
                    </datalist>
                </div>

                <div class="form-group">
                    <label for="tanggal_kegiatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Tanggal Kegiatan <span style="color: red;">*</span></label>
                    <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="waktu_mulai" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Waktu Mulai <span style="color: red;">*</span></label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>

                <div class="form-group">
                    <label for="waktu_selesai" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Waktu Selesai <span style="color: red;">*</span></label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="lokasi_kegiatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Lokasi</label>
                    <input type="text" id="lokasi_kegiatan" name="lokasi_kegiatan" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                </div>
                
                <div class="form-group">
                    <label for="deskripsi_kegiatan" style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">Deskripsi</label>
                    <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="4" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; resize: vertical;"></textarea>
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-gradient-success">
                    <i class='bx bx-save'></i> Simpan Kegiatan
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
