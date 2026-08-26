<?php ob_start(); ?>

<div class="card" >
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
                    <label for="nama_kegiatan" class="form-label">Nama Kegiatan <span style="color: red;">*</span></label>
                    <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control"  required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan <span style="color: red;">*</span></label>
                    <input list="jenis_kegiatan_options" id="jenis_kegiatan" name="jenis_kegiatan" class="form-control"  required placeholder="Pilih atau ketik jenis kegiatan...">
                    <datalist id="jenis_kegiatan_options">
                        <option value="Kerja Bakti">
                        <option value="Doa Bersama">
                        <option value="Apel">
                        <option value="Rapat">
                        <option value="Sosialisasi">
                    </datalist>
                </div>

                <div class="form-group">
                    <label for="tanggal_kegiatan" class="form-label">Tanggal Kegiatan <span style="color: red;">*</span></label>
                    <input type="date" id="tanggal_kegiatan" name="tanggal_kegiatan" class="form-control"  required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="waktu_mulai" class="form-label">Waktu Mulai <span style="color: red;">*</span></label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control"  required>
                </div>

                <div class="form-group">
                    <label for="waktu_selesai" class="form-label">Waktu Selesai <span style="color: red;">*</span></label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control"  required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="lokasi_kegiatan" class="form-label">Lokasi</label>
                    <input type="text" id="lokasi_kegiatan" name="lokasi_kegiatan" class="form-control" >
                </div>

                <!-- === Cari Lokasi via URL Google Maps === -->
                <div class="form-group">
                    <label for="url_lokasi" class="form-label">
                        <i class='bx bx-link'></i> URL Google Maps
                        <span style="color: var(--text-muted); font-weight: 400;">(opsional)</span>
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: stretch;">
                        <input type="url" id="url_lokasi" class="form-control" style="flex: 1;"
                               placeholder="Tempel link, cth: https://maps.app.goo.gl/...">
                        <button type="button" id="btnCariLokasi" class="btn btn-gradient-success" style="white-space: nowrap;">
                            <i class='bx bx-search-alt'></i> <span id="btnCariLokasiText">Cari Lokasi</span>
                        </button>
                    </div>
                    <p id="cariLokasiFeedback" style="margin: 0.5rem 0 0 0; font-size: 0.85rem; display: none;"></p>
                    <p style="margin: 0.5rem 0 0 0; color: var(--text-muted); font-size: 0.8rem;">
                        Tempel link lokasi dari Google Maps lalu klik <strong>Cari Lokasi</strong>. Koordinat &amp; marker akan terisi otomatis. Anda tetap bisa menggeser marker secara manual.
                    </p>
                </div>

                <!-- === SECTION: Pilih Lokasi di Peta === -->
                <div class="form-group" style="margin-top: 0.5rem;">
                    <label class="form-label">
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
                            <label for="radius_meter" class="form-label">Radius (meter)</label>
                            <input type="number" id="radius_meter" name="radius_meter" value="50" min="10" max="500"
                                class="form-control" >
                        </div>
                        <div>
                            <label class="form-label">Latitude</label>
                            <input type="text" id="lat_display" readonly class="form-control"
                                 placeholder="Klik peta...">
                        </div>
                        <div>
                            <label class="form-label">Longitude</label>
                            <input type="text" id="lng_display" readonly class="form-control"
                                 placeholder="Klik peta...">
                        </div>
                    </div>
                </div>
                <!-- === END: Pilih Lokasi di Peta === -->
                
                <div class="form-group">
                    <label for="deskripsi_kegiatan" class="form-label">Deskripsi</label>
                    <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="4" class="form-control" ></textarea>
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

    // === Tombol "Cari Lokasi" dari URL Google Maps ===
    const btnCari = document.getElementById('btnCariLokasi');
    if (btnCari) {
        const urlInput = document.getElementById('url_lokasi');
        const feedback = document.getElementById('cariLokasiFeedback');
        const btnText  = document.getElementById('btnCariLokasiText');

        function showFeedback(msg, ok) {
            feedback.style.display = 'block';
            feedback.style.color = ok ? '#10b981' : '#ef4444';
            feedback.textContent = (ok ? '✔ ' : '⚠ ') + msg;
        }

        function cariLokasi() {
            const url = urlInput.value.trim();
            if (!url) { showFeedback('Tempel URL Google Maps terlebih dahulu.', false); return; }

            // Jalur cepat: kalau user menempel "lat,lng" langsung, proses tanpa server
            const direct = url.match(/^\s*(-?\d{1,3}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)\s*$/);
            if (direct) {
                const lat = parseFloat(direct[1]), lng = parseFloat(direct[2]);
                setLocation(lat, lng); map.setView([lat, lng], 17);
                showFeedback('Koordinat ditemukan: ' + lat.toFixed(6) + ', ' + lng.toFixed(6), true);
                return;
            }

            // Status loading
            btnCari.disabled = true;
            const oldText = btnText.textContent;
            btnText.textContent = 'Mencari...';
            feedback.style.display = 'none';

            const token = document.querySelector('input[name="csrf_token"]').value;
            const body = new URLSearchParams({ url: url, csrf_token: token });

            fetch('<?= url('admin/kegiatan-resolve-lokasi') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: body
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    const lat = parseFloat(data.latitude), lng = parseFloat(data.longitude);
                    setLocation(lat, lng);
                    map.setView([lat, lng], 17);
                    showFeedback('Koordinat ditemukan: ' + lat.toFixed(6) + ', ' + lng.toFixed(6), true);
                } else {
                    showFeedback(data.message || 'Lokasi tidak ditemukan.', false);
                }
            })
            .catch(function () { showFeedback('Gagal menghubungi server. Coba lagi.', false); })
            .finally(function () { btnCari.disabled = false; btnText.textContent = oldText; });
        }

        btnCari.addEventListener('click', cariLokasi);
        urlInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); cariLokasi(); }  // Enter = cari
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
