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

                <!-- === SECTION: Pilih Lokasi di Peta === -->
                <div class="form-group" style="margin-top: 0.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">
                        📍 Pilih Titik Lokasi di Peta <span style="color: red;">*</span>
                    </label>
                    <p style="margin: 0 0 0.75rem 0; color: var(--text-muted); font-size: 0.85rem;">
                        Klik pada peta untuk menentukan titik lokasi kegiatan. Lingkaran menunjukkan radius validasi absensi.
                    </p>
                    
                    <!-- Container Peta -->
                    <div id="map" style="width: 100%; height: 350px; border-radius: 0.5rem; border: 1px solid var(--border-color); margin-bottom: 0.75rem;"></div>
                    
                    <!-- Hidden inputs untuk koordinat -->
                    <input type="hidden" id="latitude_kegiatan" name="latitude_kegiatan" value="">
                    <input type="hidden" id="longitude_kegiatan" name="longitude_kegiatan" value="">
                    
                    <!-- Radius -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                        <div>
                            <label for="radius_meter" style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem; color: var(--text-muted);">Radius (meter)</label>
                            <input type="number" id="radius_meter" name="radius_meter" value="50" min="10" max="500"
                                class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem; color: var(--text-muted);">Latitude</label>
                            <input type="text" id="lat_display" readonly class="form-control"
                                style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.5rem; background: var(--bg-color);" placeholder="Klik peta...">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem; color: var(--text-muted);">Longitude</label>
                            <input type="text" id="lng_display" readonly class="form-control"
                                style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.5rem; background: var(--bg-color);" placeholder="Klik peta...">
                        </div>
                    </div>
                </div>
                <!-- === END: Pilih Lokasi di Peta === -->
                
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
ob_start();
?>
<script>
// =========================================================
// SCRIPT: Peta Interaktif untuk Pilih Lokasi Kegiatan
// =========================================================

// Cek apakah elemen map ada di halaman ini
if (document.getElementById('map')) {

    // Titik default: Kabupaten Bulungan
    const DEFAULT_LAT = 2.836200;
    const DEFAULT_LNG = 117.365300;
    const DEFAULT_ZOOM = 13;

    // Inisialisasi peta
    const map = L.map('map').setView([DEFAULT_LAT, DEFAULT_LNG], DEFAULT_ZOOM);

    // Tambahkan tile layer (OpenStreetMap — gratis, tanpa API key)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Variabel untuk marker dan circle
    let marker = null;
    let circle = null;

    // Fungsi untuk update marker dan circle
    function setLocation(lat, lng) {
        const radius = parseInt(document.getElementById('radius_meter').value) || 50;

        // Update hidden inputs
        document.getElementById('latitude_kegiatan').value = lat.toFixed(8);
        document.getElementById('longitude_kegiatan').value = lng.toFixed(8);

        // Update display
        document.getElementById('lat_display').value = lat.toFixed(8);
        document.getElementById('lng_display').value = lng.toFixed(8);

        // Hapus marker & circle lama
        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);

        // Buat marker baru
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.bindPopup('📍 Lokasi Kegiatan').openPopup();

        // Buat circle radius
        circle = L.circle([lat, lng], {
            radius: radius,
            color: '#3b82f6',
            fillColor: '#3b82f680',
            fillOpacity: 0.2,
            weight: 2
        }).addTo(map);

        // Jika marker di-drag, update lokasi
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            setLocation(pos.lat, pos.lng);
        });
    }

    // Event: Klik pada peta
    map.on('click', function (e) {
        setLocation(e.latlng.lat, e.latlng.lng);
    });

    // Event: Perubahan radius
    document.getElementById('radius_meter').addEventListener('input', function () {
        const lat = parseFloat(document.getElementById('latitude_kegiatan').value);
        const lng = parseFloat(document.getElementById('longitude_kegiatan').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            setLocation(lat, lng);
        }
    });
}
</script>
<?php
$extra_js = ob_get_clean();

$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
