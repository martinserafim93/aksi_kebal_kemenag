<?php ob_start(); ?>

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div style="padding: 1rem; margin-bottom: 1rem; border-radius: 8px; 
                background: <?= $flash['type'] === 'error' ? '#fee2e2' : ($flash['type'] === 'success' ? '#dcfce7' : '#fef3c7') ?>;
                color: <?= $flash['type'] === 'error' ? '#991b1b' : ($flash['type'] === 'success' ? '#166534' : '#92400e') ?>;">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h1 class="card-title">Formulir Absensi</h1>
        <p class="text-muted" style="margin-bottom: 2rem;">Silakan isi formulir di bawah ini untuk mencatat kehadiran Anda.</p>

        <div class="data-list">
            <div class="data-item">
                <span class="data-label">Nama Kegiatan</span>
                <span class="data-value"><?= e($kegiatan['nama_kegiatan']) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Jenis Kegiatan</span>
                <span class="data-value"><?= e($kegiatan['jenis_kegiatan']) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Tanggal</span>
                <span class="data-value"><?= date('d M Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Waktu</span>
                <span class="data-value"><?= date('H:i', strtotime($kegiatan['waktu_mulai'])) ?> - <?= date('H:i', strtotime($kegiatan['waktu_selesai'])) ?></span>
            </div>
            <div class="data-item">
                <span class="data-label">Lokasi</span>
                <span class="data-value"><?= e($kegiatan['lokasi_kegiatan']) ?></span>
            </div>
        </div>

        <form action="<?= url('absensi/submit') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_kegiatan" value="<?= e($kegiatan['id_kegiatan']) ?>">
            <input type="hidden" name="kode_kegiatan" value="<?= e($kegiatan['kode_kegiatan']) ?>">
            <?= csrfField() ?>
            
            <!-- Hidden inputs untuk lokasi GPS pegawai -->
            <input type="hidden" id="latitude_absensi" name="latitude_absensi" value="">
            <input type="hidden" id="longitude_absensi" name="longitude_absensi" value="">
            <input type="hidden" id="jarak_meter" name="jarak_meter" value="">
            <input type="hidden" id="lokasi_valid" name="lokasi_valid" value="">
            
            <div class="form-group">
                <label for="nama_search" class="form-label">Nama Lengkap <span style="color: var(--danger-color)">*</span></label>
                
                <!-- Hidden input yang mengirim NIP ke server (value sebenarnya) -->
                <input type="hidden" name="nip" id="nip" required>
                
                <!-- Input pencarian yang terlihat oleh user -->
                <div class="autocomplete-wrapper" id="autocompleteWrapper">
                    <div class="autocomplete-input-container">
                        <span class="autocomplete-icon">🔍</span>
                        <input 
                            type="text" 
                            id="nama_search" 
                            class="form-control autocomplete-input" 
                            placeholder="Ketik nama pegawai..." 
                            autocomplete="off"
                            required
                        >
                        <!-- Tombol clear (X) muncul saat ada teks -->
                        <button type="button" class="autocomplete-clear" id="clearBtn" title="Hapus" style="display: none;">✕</button>
                    </div>
                    
                    <!-- Dropdown suggestions -->
                    <div class="autocomplete-dropdown" id="autocompleteDropdown"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">NIP</label>
                <input type="text" id="display_nip" class="form-control" readonly placeholder="NIP akan terisi otomatis">
            </div>

            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label">Jabatan</label>
                    <input type="text" id="display_jabatan" class="form-control" readonly placeholder="-">
                </div>
                <div>
                    <label class="form-label">Tim Kerja</label>
                    <input type="text" id="display_tim_kerja" class="form-control" readonly placeholder="-">
                </div>
            </div>

            <div class="form-group">
                <label for="foto" class="form-label">Upload Foto <span style="color: var(--danger-color)">*</span></label>
                <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg, image/png" required onchange="previewImage(event)">
                <small class="text-muted" style="display: block; margin-top: 0.5rem; font-size: 0.85rem;">Format: JPG/PNG, Maksimal: 5MB.</small>
                
                <div id="imagePreviewContainer" style="display: none; margin-top: 1rem;">
                    <p style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">Preview:</p>
                    <img id="imagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 250px; border-radius: 0.75rem; border: 2px dashed var(--border-color); padding: 0.25rem;">
                </div>
            </div>

            <!-- === SECTION: Status Lokasi GPS === -->
            <div id="lokasi-status" class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.95rem;">
                    📍 Verifikasi Lokasi
                </label>
                
                <!-- Loading state -->
                <div id="lokasi-loading" style="padding: 1rem; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 20px; height: 20px; border: 3px solid #3b82f6; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <span style="color: #1e40af;">Mendeteksi lokasi Anda...</span>
                </div>
                
                <!-- Sukses: dalam radius -->
                <div id="lokasi-ok" style="padding: 1rem; background: #f0fdf4; border: 1px solid #86efac; border-radius: 0.5rem; display: none;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #166534;">
                        <i class='bx bxs-check-circle' style="font-size: 1.25rem;"></i>
                        <strong>Lokasi Valid</strong>
                    </div>
                    <p id="lokasi-ok-detail" style="margin: 0.25rem 0 0 1.75rem; font-size: 0.85rem; color: #15803d;"></p>
                </div>

                <!-- Gagal: di luar radius -->
                <div id="lokasi-fail" style="padding: 1rem; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 0.5rem; display: none;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #991b1b;">
                        <i class='bx bxs-x-circle' style="font-size: 1.25rem;"></i>
                        <strong>Lokasi Tidak Sesuai!</strong>
                    </div>
                    <p id="lokasi-fail-detail" style="margin: 0.25rem 0 0 1.75rem; font-size: 0.85rem; color: #b91c1c;"></p>
                    <button type="button" id="btn-retry-lokasi" onclick="detectLocation()" 
                            style="margin-top: 0.75rem; margin-left: 1.75rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.85rem;">
                        <i class='bx bx-refresh'></i> Coba Deteksi Ulang
                    </button>
                </div>

                <!-- Error: GPS mati / tidak support -->
                <div id="lokasi-error" style="padding: 1rem; background: #fffbeb; border: 1px solid #fcd34d; border-radius: 0.5rem; display: none;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400e;">
                        <i class='bx bxs-error' style="font-size: 1.25rem;"></i>
                        <strong>Akses Lokasi Diperlukan</strong>
                    </div>
                    <p id="lokasi-error-detail" style="margin: 0.25rem 0 0 1.75rem; font-size: 0.85rem; color: #a16207;"></p>
                    <button type="button" onclick="detectLocation()"
                            style="margin-top: 0.75rem; margin-left: 1.75rem; padding: 0.5rem 1rem; background: #f59e0b; color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.85rem;">
                        <i class='bx bx-refresh'></i> Coba Lagi
                    </button>
                </div>
            </div>

            <style>
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            </style>
            <!-- === END: Status Lokasi GPS === -->

            <button type="submit" id="btn-submit-absensi" class="btn btn-primary" style="margin-top: 1.5rem;">
                <i class='bx bx-send'></i> Submit Absensi
            </button>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<script>
    // Data pegawai dari PHP dikonversi ke JSON untuk autocomplete
    const pegawaiData = <?= json_encode(array_map(function($p) {
        return [
            'nip' => $p['nip'],
            'nama' => $p['nama_lengkap']
        ];
    }, $pegawaiList)) ?>;

    // ============================================================
    // Autocomplete Logic
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('nama_search');
        const hiddenNip = document.getElementById('nip');
        const dropdown = document.getElementById('autocompleteDropdown');
        const clearBtn = document.getElementById('clearBtn');
        const wrapper = document.getElementById('autocompleteWrapper');

        let activeIndex = -1; // Index item yang di-highlight via keyboard
        let filteredResults = []; // Hasil filter saat ini

        // Event: User mengetik di input
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            activeIndex = -1;
            
            // Tampilkan/sembunyikan tombol clear
            clearBtn.style.display = query.length > 0 ? 'flex' : 'none';
            
            // Reset hidden NIP ketika user mengetik ulang
            hiddenNip.value = '';
            
            if (query.length < 1) {
                closeDropdown();
                return;
            }
            
            // Filter data pegawai
            filteredResults = pegawaiData.filter(function(p) {
                return p.nama.toLowerCase().includes(query);
            });
            
            renderDropdown(filteredResults, query);
        });

        // Render dropdown items
        function renderDropdown(results, query) {
            if (results.length === 0) {
                dropdown.innerHTML = '<div class="autocomplete-empty">Tidak ditemukan pegawai dengan nama tersebut</div>';
                dropdown.classList.add('show');
                return;
            }
            
            let html = '';
            results.forEach(function(item, index) {
                // Highlight teks yang cocok
                const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
                const highlightedName = item.nama.replace(regex, '<mark>$1</mark>');
                
                html += '<div class="autocomplete-item' + (index === activeIndex ? ' active' : '') + '" ' +
                        'data-nip="' + item.nip + '" ' +
                        'data-nama="' + escapeHtml(item.nama) + '" ' +
                        'data-index="' + index + '">' +
                        '<span class="autocomplete-item-name">' + highlightedName + '</span>' +
                        '<span class="autocomplete-item-nip">NIP: ' + item.nip + '</span>' +
                        '</div>';
            });
            
            dropdown.innerHTML = html;
            dropdown.classList.add('show');
            
            // Tambah event click ke setiap item
            dropdown.querySelectorAll('.autocomplete-item').forEach(function(el) {
                el.addEventListener('click', function() {
                    selectItem(this.dataset.nip, this.dataset.nama);
                });
            });
        }

        // Pilih item
        window.selectItem = function(nip, nama) {
            searchInput.value = nama;
            hiddenNip.value = nip;
            clearBtn.style.display = 'flex';
            closeDropdown();
            
            // Trigger fetch data pegawai (NIP, Jabatan, Tim Kerja)
            fetchPegawaiData();
        }

        // Tutup dropdown
        function closeDropdown() {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
            activeIndex = -1;
            filteredResults = [];
        }

        // Tombol clear
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            hiddenNip.value = '';
            clearBtn.style.display = 'none';
            closeDropdown();
            searchInput.focus();
            
            // Reset display fields
            document.getElementById('display_nip').value = '';
            document.getElementById('display_jabatan').value = '';
            document.getElementById('display_tim_kerja').value = '';
        });

        // Keyboard navigation (↑ ↓ Enter Escape)
        searchInput.addEventListener('keydown', function(e) {
            const items = dropdown.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    const item = items[activeIndex];
                    selectItem(item.dataset.nip, item.dataset.nama);
                }
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });

        function updateActiveItem(items) {
            items.forEach(function(el, i) {
                el.classList.toggle('active', i === activeIndex);
            });
            // Scroll into view
            if (items[activeIndex]) {
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                closeDropdown();
            }
        });

        // Utility: Escape regex special characters
        function escapeRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // Utility: Escape HTML
        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    });
    function fetchPegawaiData() {
        const nip = document.getElementById('nip').value;
        const displayNip = document.getElementById('display_nip');
        const displayJabatan = document.getElementById('display_jabatan');
        const displayTimKerja = document.getElementById('display_tim_kerja');

        if (!nip) {
            displayNip.value = '';
            displayJabatan.value = '';
            displayTimKerja.value = '';
            return;
        }

        fetch('<?= url('absensi/getPegawaiData') ?>?nip=' + nip)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    displayNip.value = res.data.nip;
                    displayJabatan.value = res.data.nama_jabatan;
                    displayTimKerja.value = res.data.nama_tim_kerja;
                } else {
                    alert(res.error || 'Terjadi kesalahan mengambil data pegawai.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan pada server.');
            });
    }

    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('imagePreviewContainer');
        const preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            container.style.display = 'none';
        }
    }

// =========================================================
// SCRIPT: Deteksi Lokasi GPS dan Validasi Jarak
// =========================================================

// Data lokasi kegiatan dari server (embed via PHP)
const KEGIATAN_LAT = <?= json_encode($kegiatan['latitude_kegiatan'] ?? null) ?>;
const KEGIATAN_LNG = <?= json_encode($kegiatan['longitude_kegiatan'] ?? null) ?>;
const KEGIATAN_RADIUS = <?= json_encode($kegiatan['radius_meter'] ?? 50) ?>;

// Apakah kegiatan ini punya validasi lokasi?
const HAS_LOCATION = (KEGIATAN_LAT !== null && KEGIATAN_LNG !== null);

// Elemen UI
const elLoading = document.getElementById('lokasi-loading');
const elOk      = document.getElementById('lokasi-ok');
const elFail    = document.getElementById('lokasi-fail');
const elError   = document.getElementById('lokasi-error');
const btnSubmit = document.getElementById('btn-submit-absensi');

/**
 * Hitung jarak antara 2 titik koordinat (Haversine Formula)
 * @return {number} Jarak dalam meter
 */
function hitungJarak(lat1, lng1, lat2, lng2) {
    const R = 6371000; // Radius bumi dalam meter
    const toRad = (deg) => deg * (Math.PI / 180);
    
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLng / 2) * Math.sin(dLng / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    
    return R * c; // Hasil dalam meter
}

/**
 * Sembunyikan semua status box
 */
function hideAllStatus() {
    elLoading.style.display = 'none';
    elOk.style.display      = 'none';
    elFail.style.display     = 'none';
    elError.style.display    = 'none';
}

/**
 * Deteksi lokasi GPS pegawai
 */
function detectLocation() {
    // Jika kegiatan tidak punya koordinat, skip validasi lokasi
    if (!HAS_LOCATION) {
        document.getElementById('lokasi-status').style.display = 'none';
        return;
    }

    // Tampilkan loading
    hideAllStatus();
    elLoading.style.display = 'flex';
    btnSubmit.disabled = true;

    // Cek apakah browser support Geolocation
    if (!navigator.geolocation) {
        hideAllStatus();
        elError.style.display = 'block';
        document.getElementById('lokasi-error-detail').textContent = 
            'Browser Anda tidak mendukung GPS. Gunakan browser modern (Chrome/Safari).';
        return;
    }

    // Minta lokasi GPS
    navigator.geolocation.getCurrentPosition(
        // SUCCESS: Lokasi berhasil didapat
        function (position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            // Hitung jarak
            const jarak = hitungJarak(userLat, userLng, KEGIATAN_LAT, KEGIATAN_LNG);
            const jarakBulat = Math.round(jarak * 100) / 100; // 2 desimal

            // Simpan ke hidden inputs
            document.getElementById('latitude_absensi').value  = userLat.toFixed(8);
            document.getElementById('longitude_absensi').value = userLng.toFixed(8);
            document.getElementById('jarak_meter').value       = jarakBulat;

            hideAllStatus();

            if (jarak <= KEGIATAN_RADIUS) {
                // ✅ DALAM RADIUS — boleh submit
                document.getElementById('lokasi_valid').value = '1';
                elOk.style.display = 'block';
                document.getElementById('lokasi-ok-detail').textContent = 
                    'Anda berada ' + jarakBulat + ' meter dari lokasi kegiatan (radius: ' + KEGIATAN_RADIUS + ' m).';
                btnSubmit.disabled = false;
            } else {
                // ❌ DI LUAR RADIUS — tidak boleh submit
                document.getElementById('lokasi_valid').value = '0';
                elFail.style.display = 'block';
                document.getElementById('lokasi-fail-detail').textContent = 
                    'Anda berada ' + jarakBulat + ' meter dari lokasi kegiatan. ' +
                    'Maksimal radius: ' + KEGIATAN_RADIUS + ' meter. ' +
                    'Silakan pindah ke lokasi kegiatan dan coba lagi.';
                btnSubmit.disabled = true;
            }
        },
        // ERROR: Gagal mendapatkan lokasi
        function (error) {
            hideAllStatus();
            elError.style.display = 'block';
            
            let pesan = '';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    pesan = 'Anda menolak akses lokasi. Silakan aktifkan GPS dan izinkan akses lokasi di pengaturan browser Anda, lalu coba lagi.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    pesan = 'Informasi lokasi tidak tersedia. Pastikan GPS perangkat Anda aktif.';
                    break;
                case error.TIMEOUT:
                    pesan = 'Waktu deteksi lokasi habis. Pastikan Anda berada di area dengan sinyal GPS yang baik.';
                    break;
                default:
                    pesan = 'Terjadi kesalahan saat mendeteksi lokasi. Silakan coba lagi.';
            }
            
            document.getElementById('lokasi-error-detail').textContent = pesan;
            btnSubmit.disabled = true;
        },
        // OPTIONS
        {
            enableHighAccuracy: true,  // Gunakan GPS presisi tinggi
            timeout: 15000,            // Timeout 15 detik
            maximumAge: 0              // Jangan pakai cache, selalu minta lokasi baru
        }
    );
}

// Jalankan deteksi lokasi otomatis saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    detectLocation();
});

// Cegah submit jika lokasi belum valid
document.querySelector('form').addEventListener('submit', function(e) {
    if (HAS_LOCATION) {
        const lokasiValid = document.getElementById('lokasi_valid').value;
        if (lokasiValid !== '1') {
            e.preventDefault();
            alert('⚠️ Lokasi Anda belum terverifikasi atau di luar radius kegiatan.\n\nSilakan pastikan Anda berada di lokasi kegiatan dan klik "Coba Deteksi Ulang".');
            return false;
        }
    }
});
</script>
<?php 
$extra_js = ob_get_clean();
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
