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

            <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">
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
</script>
<?php 
$extra_js = ob_get_clean();
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
