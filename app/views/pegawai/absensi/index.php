<?php ob_start(); ?>

<link rel="stylesheet" href="<?= asset('css/absensi.css') ?>?v=<?= time() ?>">

<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div class="abs-flash abs-flash-<?= $flash['type'] ?>">
        <i class='bx <?= $flash['type'] === 'error' ? 'bxs-error-circle' : ($flash['type'] === 'success' ? 'bxs-check-circle' : 'bxs-info-circle') ?>' style="font-size: 1.25rem;"></i>
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card abs-card stagger-1">
    <div class="card-body">
        <h1 class="card-title">Formulir Absensi</h1>
        <p class="text-muted" style="margin-bottom: 2rem;">Silakan isi formulir di bawah ini untuk mencatat kehadiran Anda.</p>

        <div class="data-list stagger-2">
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

        <form action="<?= url('absensi/submit') ?>" method="POST" enctype="multipart/form-data" class="stagger-3">
            <input type="hidden" name="id_kegiatan" value="<?= e($kegiatan['id_kegiatan']) ?>">
            <input type="hidden" name="kode_kegiatan" value="<?= e($kegiatan['kode_kegiatan']) ?>">
            <?= csrfField() ?>
            
            <!-- Hidden inputs untuk lokasi GPS pegawai -->
            <input type="hidden" id="latitude_absensi" name="latitude_absensi" value="">
            <input type="hidden" id="longitude_absensi" name="longitude_absensi" value="">
            <input type="hidden" id="jarak_meter" name="jarak_meter" value="">
            <input type="hidden" id="lokasi_valid" name="lokasi_valid" value="">
            
            <div class="form-group autocomplete-form-group stagger-4">
                <label for="nama_search" class="form-label">Nama Lengkap <span style="color: var(--danger-color)">*</span></label>
                
                <input type="hidden" name="nip" id="nip" required>
                
                <div class="autocomplete-wrapper" id="autocompleteWrapper">
                    <div class="autocomplete-input-container">
                        <span class="autocomplete-icon-box"><i class='bx bx-search'></i></span>
                        <input 
                            type="text" 
                            id="nama_search" 
                            class="form-control autocomplete-input" 
                            placeholder="Ketik nama pegawai..." 
                            autocomplete="off"
                            required
                        >
                        <button type="button" class="autocomplete-clear-btn" id="clearBtn" title="Hapus" style="display: none;">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                    <div class="autocomplete-dropdown" id="autocompleteDropdown"></div>
                </div>
            </div>

            <div class="form-group stagger-4">
                <label class="form-label">NIP</label>
                <input type="text" id="display_nip" class="form-control" readonly placeholder="NIP akan terisi otomatis">
            </div>

            <div class="form-group stagger-4" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label class="form-label">Jabatan</label>
                    <input type="text" id="display_jabatan" class="form-control" readonly placeholder="-">
                </div>
                <div>
                    <label class="form-label">Tim Kerja</label>
                    <input type="text" id="display_tim_kerja" class="form-control" readonly placeholder="-">
                </div>
            </div>

            <div class="form-group stagger-5">
                <label for="status_kehadiran" class="form-label">
                    Status Kehadiran <span style="color: var(--danger-color)">*</span>
                </label>
                <select name="status_kehadiran" id="status_kehadiran" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Status Kehadiran --</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Tidak Hadir">Tidak Hadir</option>
                </select>
            </div>

            <!-- === SECTION: Form Hadir === -->
            <div id="section-hadir" class="fade-section" style="display: none;">
                <div class="form-group">
                    <label for="foto" class="form-label">Upload Foto Kehadiran <span style="color: var(--danger-color)">*</span></label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/jpeg, image/png" onchange="previewImage(event)">
                    <small class="text-muted" style="display: block; margin-top: 0.5rem; font-size: 0.85rem;">Format: JPG/PNG, Maksimal: 5MB.</small>
                    
                    <div id="imagePreviewContainer" class="preview-container" style="display: none;">
                        <span class="preview-label">Preview:</span>
                        <img id="imagePreview" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="Preview" class="preview-image">
                    </div>
                </div>

                <div id="lokasi-status" class="lokasi-status-container">
                    <label class="lokasi-status-title">
                        <i class='bx bx-map-pin'></i> Verifikasi Lokasi
                    </label>
                    
                    <div id="lokasi-loading" class="lokasi-alert lokasi-loading">
                        <div class="lokasi-alert-header">
                            <div class="abs-spinner"></div>
                            <span>Mendeteksi lokasi Anda...</span>
                        </div>
                    </div>
                    
                    <div id="lokasi-ok" class="lokasi-alert lokasi-ok" style="display: none;">
                        <div class="lokasi-alert-header">
                            <i class='bx bxs-check-circle' style="font-size: 1.25rem;"></i>
                            <span>Lokasi Valid</span>
                        </div>
                        <p id="lokasi-ok-detail" class="lokasi-alert-desc"></p>
                    </div>

                    <div id="lokasi-fail" class="lokasi-alert lokasi-fail" style="display: none;">
                        <div class="lokasi-alert-header">
                            <i class='bx bxs-x-circle' style="font-size: 1.25rem;"></i>
                            <span>Lokasi Tidak Sesuai!</span>
                        </div>
                        <p id="lokasi-fail-detail" class="lokasi-alert-desc"></p>
                        <div class="lokasi-alert-action">
                            <button type="button" id="btn-retry-lokasi" class="btn-alert btn-retry-fail" onclick="detectLocation()">
                                <i class='bx bx-refresh'></i> Coba Deteksi Ulang
                            </button>
                        </div>
                    </div>

                    <div id="lokasi-error" class="lokasi-alert lokasi-error" style="display: none;">
                        <div class="lokasi-alert-header">
                            <i class='bx bxs-error' style="font-size: 1.25rem;"></i>
                            <span>Akses Lokasi Diperlukan</span>
                        </div>
                        <p id="lokasi-error-detail" class="lokasi-alert-desc"></p>
                        <div class="lokasi-alert-action">
                            <button type="button" class="btn-alert btn-retry-error" onclick="detectLocation()">
                                <i class='bx bx-refresh'></i> Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === SECTION: Form Tidak Hadir === -->
            <div id="section-tidak-hadir" class="fade-section" style="display: none;">
                <div class="form-group">
                    <label for="file_bukti" class="form-label">
                        Upload Bukti Ketidakhadiran <span style="color: var(--danger-color)">*</span>
                    </label>
                    <input type="file" name="file_bukti" id="file_bukti" class="form-control" 
                           accept="image/jpeg, image/png, application/pdf" 
                           onchange="previewFileBukti(event)">
                    <small class="text-muted" style="display: block; margin-top: 0.5rem; font-size: 0.85rem;">
                        Format: JPG/PNG/PDF, Maksimal: 5MB.<br>
                        Contoh: Surat izin, surat sakit, atau bukti lainnya.
                    </small>
                    
                    <div id="buktiPreviewContainer" class="preview-container" style="display: none;">
                        <span class="preview-label">Preview:</span>
                        <img id="buktiImagePreview" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt="Preview Bukti" class="preview-image">
                    </div>
                    
                    <div id="buktiPdfInfo" class="preview-pdf" style="display: none; margin-top: 1rem;">
                        <i class='bx bxs-file-pdf preview-pdf-icon'></i>
                        <div>
                            <p class="preview-pdf-name" id="buktiPdfName">-</p>
                            <span class="preview-pdf-size" id="buktiPdfSize">-</span>
                        </div>
                    </div>
                </div>

                <div class="lokasi-alert lokasi-error" style="margin-bottom: 1rem;">
                    <div class="lokasi-alert-header" style="font-size: 0.9rem;">
                        <i class='bx bxs-info-circle'></i>
                        <span>Lokasi GPS <strong>tidak diwajibkan</strong> karena Anda tidak hadir.</span>
                    </div>
                </div>
            </div>

            <button type="submit" id="btn-submit-absensi" class="btn btn-primary stagger-5" style="margin-top: 1.5rem;">
                <i class='bx bx-send'></i> Submit Absensi
            </button>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<script>
    const pegawaiData = <?= json_encode(array_map(function($p) {
        return [
            'nip' => $p['nip'],
            'nama' => $p['nama_lengkap']
        ];
    }, $pegawaiList)) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('nama_search');
        const hiddenNip = document.getElementById('nip');
        const dropdown = document.getElementById('autocompleteDropdown');
        const clearBtn = document.getElementById('clearBtn');
        const wrapper = document.getElementById('autocompleteWrapper');

        let activeIndex = -1;
        let filteredResults = [];

        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            activeIndex = -1;
            
            clearBtn.style.display = query.length > 0 ? 'flex' : 'none';
            hiddenNip.value = '';
            
            if (query.length < 1) {
                closeDropdown();
                return;
            }
            
            filteredResults = pegawaiData.filter(function(p) {
                return p.nama.toLowerCase().includes(query);
            });
            
            renderDropdown(filteredResults, query);
        });

        function renderDropdown(results, query) {
            if (results.length === 0) {
                dropdown.innerHTML = '<div class="autocomplete-empty">Tidak ditemukan pegawai dengan nama tersebut</div>';
                dropdown.classList.add('show');
                return;
            }
            
            let html = '';
            results.forEach(function(item, index) {
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
            
            dropdown.querySelectorAll('.autocomplete-item').forEach(function(el) {
                el.addEventListener('click', function() {
                    selectItem(this.dataset.nip, this.dataset.nama);
                });
            });
        }

        window.selectItem = function(nip, nama) {
            searchInput.value = nama;
            hiddenNip.value = nip;
            clearBtn.style.display = 'flex';
            closeDropdown();
            fetchPegawaiData();
        }

        function closeDropdown() {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
            activeIndex = -1;
            filteredResults = [];
        }

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            hiddenNip.value = '';
            clearBtn.style.display = 'none';
            closeDropdown();
            searchInput.focus();
            
            document.getElementById('display_nip').value = '';
            document.getElementById('display_jabatan').value = '';
            document.getElementById('display_tim_kerja').value = '';
        });

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
            if (items[activeIndex]) {
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            }
        }

        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                closeDropdown();
            }
        });

        function escapeRegex(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

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

    document.getElementById('status_kehadiran').addEventListener('change', function() {
        const status = this.value;
        const sectionHadir = document.getElementById('section-hadir');
        const sectionTidakHadir = document.getElementById('section-tidak-hadir');
        const btnSubmit = document.getElementById('btn-submit-absensi');
        
        // Animasi fade out cepat sebelum ditukar
        sectionHadir.classList.remove('show');
        sectionTidakHadir.classList.remove('show');
        
        setTimeout(() => {
            sectionHadir.style.display = 'none';
            sectionTidakHadir.style.display = 'none';
            
            if (status === 'Hadir') {
                sectionHadir.style.display = 'block';
                document.getElementById('lokasi-status').style.display = 'block';
                
                // Allow reflow
                void sectionHadir.offsetWidth;
                sectionHadir.classList.add('show');
                
                document.getElementById('foto').setAttribute('required', '');
                document.getElementById('file_bukti').removeAttribute('required');
                
                detectLocation();
                
            } else if (status === 'Tidak Hadir') {
                sectionTidakHadir.style.display = 'block';
                
                // Allow reflow
                void sectionTidakHadir.offsetWidth;
                sectionTidakHadir.classList.add('show');
                
                document.getElementById('file_bukti').setAttribute('required', '');
                document.getElementById('foto').removeAttribute('required');
                
                btnSubmit.disabled = false;
            }
        }, 50);
    });

    function previewFileBukti(event) {
        const input = event.target;
        const file = input.files[0];
        
        const imgContainer = document.getElementById('buktiPreviewContainer');
        const imgPreview = document.getElementById('buktiImagePreview');
        const pdfContainer = document.getElementById('buktiPdfInfo');
        const pdfName = document.getElementById('buktiPdfName');
        const pdfSize = document.getElementById('buktiPdfSize');
        
        imgContainer.style.display = 'none';
        pdfContainer.style.display = 'none';
        
        if (!file) return;
        
        if (file.size > 5 * 1024 * 1024) {
            alert('⚠️ Ukuran file maksimal 5MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + ' MB');
            input.value = '';
            return;
        }
        
        if (file.type === 'application/pdf') {
            pdfName.textContent = file.name;
            pdfSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
            pdfContainer.style.display = 'flex';
        } else if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                imgContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

// GPS Validation
const KEGIATAN_LAT = <?= json_encode($kegiatan['latitude_kegiatan'] ?? null) ?>;
const KEGIATAN_LNG = <?= json_encode($kegiatan['longitude_kegiatan'] ?? null) ?>;
const KEGIATAN_RADIUS = <?= json_encode($kegiatan['radius_meter'] ?? 50) ?>;

const HAS_LOCATION = (KEGIATAN_LAT !== null && KEGIATAN_LNG !== null);

const elLoading = document.getElementById('lokasi-loading');
const elOk      = document.getElementById('lokasi-ok');
const elFail    = document.getElementById('lokasi-fail');
const elError   = document.getElementById('lokasi-error');
const btnSubmit = document.getElementById('btn-submit-absensi');

function hitungJarak(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const toRad = (deg) => deg * (Math.PI / 180);
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLng / 2) * Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function hideAllStatus() {
    elLoading.style.display = 'none';
    elOk.style.display      = 'none';
    elFail.style.display     = 'none';
    elError.style.display    = 'none';
}

function detectLocation() {
    if (!HAS_LOCATION) {
        document.getElementById('lokasi-status').style.display = 'none';
        return;
    }

    hideAllStatus();
    elLoading.style.display = 'flex';
    btnSubmit.disabled = true;

    if (!navigator.geolocation) {
        hideAllStatus();
        elError.style.display = 'flex';
        document.getElementById('lokasi-error-detail').textContent = 'Browser Anda tidak mendukung GPS.';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function (position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            const jarak = hitungJarak(userLat, userLng, KEGIATAN_LAT, KEGIATAN_LNG);
            const jarakBulat = Math.round(jarak * 100) / 100;

            document.getElementById('latitude_absensi').value  = userLat.toFixed(8);
            document.getElementById('longitude_absensi').value = userLng.toFixed(8);
            document.getElementById('jarak_meter').value       = jarakBulat;

            hideAllStatus();

            if (jarak <= KEGIATAN_RADIUS) {
                document.getElementById('lokasi_valid').value = '1';
                elOk.style.display = 'flex';
                document.getElementById('lokasi-ok-detail').textContent = 'Anda berada ' + jarakBulat + ' meter dari lokasi kegiatan (radius: ' + KEGIATAN_RADIUS + ' m).';
                btnSubmit.disabled = false;
            } else {
                document.getElementById('lokasi_valid').value = '0';
                elFail.style.display = 'flex';
                document.getElementById('lokasi-fail-detail').textContent = 'Anda berada ' + jarakBulat + ' meter dari lokasi. Maksimal radius: ' + KEGIATAN_RADIUS + ' m.';
                btnSubmit.disabled = true;
            }
        },
        function (error) {
            hideAllStatus();
            elError.style.display = 'flex';
            
            let pesan = '';
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    pesan = 'Anda menolak akses lokasi. Aktifkan GPS dan izinkan browser.'; break;
                case error.POSITION_UNAVAILABLE:
                    pesan = 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.'; break;
                case error.TIMEOUT:
                    pesan = 'Waktu deteksi habis. Pastikan sinyal GPS baik.'; break;
                default:
                    pesan = 'Terjadi kesalahan saat mendeteksi lokasi.';
            }
            document.getElementById('lokasi-error-detail').textContent = pesan;
            btnSubmit.disabled = true;
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

document.querySelector('form').addEventListener('submit', function(e) {
    const nip = document.getElementById('nip').value;
    if (!nip) {
        e.preventDefault();
        alert('⚠️ Silakan pilih Nama Pegawai dari daftar pilihan.');
        return false;
    }

    const statusKehadiran = document.getElementById('status_kehadiran').value;
    if (!statusKehadiran) {
        e.preventDefault();
        alert('⚠️ Silakan pilih Status Kehadiran.');
        return false;
    }
    
    if (statusKehadiran === 'Hadir') {
        if (HAS_LOCATION) {
            const lokasiValid = document.getElementById('lokasi_valid').value;
            if (lokasiValid !== '1') {
                e.preventDefault();
                alert('⚠️ Lokasi Anda belum terverifikasi atau di luar radius.');
                return false;
            }
        }
        
        const fotoInput = document.getElementById('foto');
        if (!fotoInput.files || fotoInput.files.length === 0) {
            e.preventDefault();
            alert('⚠️ Foto kehadiran wajib diunggah.');
            return false;
        }
    }
    
    if (statusKehadiran === 'Tidak Hadir') {
        const fileBukti = document.getElementById('file_bukti');
        if (!fileBukti.files || fileBukti.files.length === 0) {
            e.preventDefault();
            alert('⚠️ File bukti wajib diunggah.');
            return false;
        }
    }
});
</script>
<?php 
$extra_js = ob_get_clean();
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
