# 📋 Issues Perbaikan Fitur — AKSI KEBAL

Dokumen ini berisi 2 issue perbaikan fitur beserta tahapan implementasi yang detail dan mudah diikuti.

---

## Issue #1: Redesign Halaman Login Admin

### 🎯 Tujuan
Mengubah tampilan halaman login admin dari tema **glassmorphism** (dark, transparan, floating orbs) menjadi tema **light/clean** yang **konsisten dengan dashboard admin** — yaitu menggunakan warna hijau (`#10b981`), background putih/abu terang, dan font Inter yang sama.

### 📁 File yang Perlu Diubah

| No | File | Aksi |
|----|------|------|
| 1 | `public/assets/css/admin-auth.css` | **REWRITE** — Ganti seluruh CSS |
| 2 | `app/views/admin/login.php` | **MODIFY** — Sederhanakan HTML |

### 🔍 Referensi Tema Dashboard

Lihat file berikut sebagai **acuan warna & gaya** yang harus diikuti:

- `public/assets/css/admin-layout.css` — Variabel warna utama
- `app/views/admin/dashboard.php` — Contoh card & stat styling

> **PENTING:** Warna utama dashboard adalah **hijau** `--primary: #10b981`, dengan background `#f8fafc` dan sidebar `#1e293b`. Login harus mengikuti palet ini, **bukan** palet indigo/teal dari glassmorphism.

---

### 📝 Tahapan Implementasi

#### Tahap 1: Modifikasi HTML — `app/views/admin/login.php`

**Apa yang harus dilakukan:**

1. **Hapus elemen background glassmorphism** (baris 12-14):
   ```diff
   -    <!-- Animated Background -->
   -    <div class="auth-bg"></div>
   -    <div class="auth-grid"></div>
   ```
   Elemen `auth-bg` dan `auth-grid` adalah dekorasi glassmorphism (gradient animasi, floating orbs, grid pattern). Hapus seluruhnya.

2. **Ganti class body** agar lebih generik (opsional, tetap pakai `auth-page`).

3. **Selebihnya struktur HTML bisa dipertahankan** — yang paling penting adalah perubahan CSS-nya.

> **TIP:** Struktur HTML (`auth-container`, `auth-card`, `auth-brand`, `auth-form`, dll.) sudah bagus. Yang perlu diubah mayoritas ada di CSS.

---

#### Tahap 2: Rewrite CSS — `public/assets/css/admin-auth.css`

**Prinsip desain baru:**

| Aspek | Sebelum (Glassmorphism) | Sesudah (Tema Dashboard) |
|-------|-------------------------|--------------------------|
| Background body | Dark gradient animasi + floating orbs | Solid `#f8fafc` atau gradient sangat halus `linear-gradient(135deg, #f0fdf4, #e0f2fe)` |
| Card login | `rgba(255,255,255,0.08)` + `backdrop-filter: blur()` | Solid `#ffffff` + `box-shadow` + `border: 1px solid #e2e8f0` |
| Warna teks | Light (`#f1f5f9`) di atas dark bg | Dark (`#334155`) di atas light bg |
| Aksen warna | Indigo `#6366f1` + Teal `#14b8a6` | **Hijau** `#10b981` (sama dengan dashboard) |
| Input fields | Background transparan `rgba(255,255,255,0.05)` | Background `#f8fafc` dengan border `#e2e8f0` |
| Tombol login | Gradient indigo | Gradient hijau `#10b981 → #059669` |
| Accent line (top card) | Gradient indigo-teal shimmer | Garis solid hijau atau gradient hijau halus |

**Langkah-langkah detail:**

1. **Pertahankan import Google Fonts Inter** (baris 11) — jangan dihapus.

2. **Ganti CSS Variables** (baris 16-77):
   ```css
   :root {
       /* Brand — samakan dengan dashboard */
       --primary: #10b981;
       --primary-hover: #059669;
       --primary-light: #d1fae5;

       /* Neutrals — samakan dengan dashboard */
       --text-main: #334155;
       --text-muted: #64748b;
       --border-color: #e2e8f0;
       --content-bg: #f8fafc;
       --card-bg: #ffffff;

       /* Semantic */
       --success: #10b981;
       --error: #ef4444;
       --warning: #f59e0b;

       /* Typography */
       --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

       /* Shadows — samakan dengan dashboard */
       --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
       --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
       --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);

       /* Transitions */
       --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
       --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);

       /* Border Radius */
       --radius-sm: 0.5rem;
       --radius-md: 0.75rem;
       --radius-lg: 1rem;
       --radius-xl: 1.25rem;
   }
   ```

3. **Ubah `body.auth-page`** — ganti background:
   ```css
   body.auth-page {
       font-family: var(--font-family);
       min-height: 100vh;
       display: flex;
       align-items: center;
       justify-content: center;
       padding: 1.5rem;
       /* Background light, bukan dark */
       background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #e0f2fe 100%);
       color: var(--text-main);
   }
   ```

4. **Hapus seluruh section CSS berikut** (karena tidak lagi dipakai):
   - `Animated Gradient Background` — `.auth-bg`, `.auth-bg::before`, `.auth-bg::after`, `@keyframes gradientShift`, `@keyframes float`
   - `Grid pattern overlay` — `.auth-grid`
   - `Dashboard Placeholder Styles` — seluruh blok dari baris 563-684

5. **Ubah `.auth-card`** — ganti glassmorphism ke solid:
   ```css
   .auth-card {
       background: var(--card-bg);
       border: 1px solid var(--border-color);
       border-radius: var(--radius-xl);
       padding: 2.5rem 2rem;
       box-shadow: var(--shadow-lg);
       position: relative;
       overflow: hidden;
       /* HAPUS backdrop-filter */
   }
   ```

6. **Ubah accent line** (`.auth-card::before`):
   ```css
   .auth-card::before {
       content: '';
       position: absolute;
       top: 0; left: 0; right: 0;
       height: 4px;
       background: linear-gradient(90deg, var(--primary), var(--primary-hover));
       /* Hapus animasi shimmer, cukup solid gradient */
   }
   ```

7. **Ubah `.auth-brand`** — teks jadi gelap:
   ```css
   .auth-brand h1 {
       font-size: 1.625rem;
       font-weight: 800;
       letter-spacing: -0.02em;
       color: var(--text-main); /* Bukan gradient putih */
       line-height: 1.2;
   }

   .auth-brand p {
       font-size: 0.8125rem;
       color: var(--text-muted);
       margin-top: 0.375rem;
   }
   ```

8. **Ubah `.auth-brand-icon`** — ganti ke hijau:
   ```css
   .auth-brand-icon {
       /* ... ukuran tetap ... */
       background: linear-gradient(135deg, var(--primary), var(--primary-hover));
       box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
   }
   ```

9. **Ubah `.form-input`** — light theme:
   ```css
   .form-input {
       width: 100%;
       padding: 0.75rem 0.875rem 0.75rem 2.75rem;
       background: #f8fafc;
       border: 1px solid var(--border-color);
       border-radius: var(--radius-md);
       color: var(--text-main);
       font-family: var(--font-family);
       font-size: 0.875rem;
       transition: all var(--transition-fast);
       outline: none;
   }

   .form-input:focus {
       border-color: var(--primary);
       background: #ffffff;
       box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
   }

   .form-input::placeholder {
       color: var(--text-muted);
   }
   ```

10. **Ubah `.btn-login`** — gradient hijau:
    ```css
    .btn-login {
        width: 100%;
        padding: 0.8125rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-hover));
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-login:hover {
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        transform: translateY(-1px);
    }
    ```

11. **Ubah `.auth-alert-*`** — sesuaikan warna agar terlihat pada background light:
    ```css
    .auth-alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .auth-alert-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .auth-alert-warning {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #92400e;
    }
    ```

12. **Ubah `.auth-footer`** — warna teks gelap:
    ```css
    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    ```

13. **Pertahankan animasi `slideUp`** pada `.auth-container` — ini tetap bagus dan tidak terkait glassmorphism.

14. **Pertahankan responsive styles** — hanya sesuaikan warna jika ada referensi ke dark theme.

> **CATATAN:** Animasi `@keyframes slideUp` dan `@keyframes spin` tetap dipertahankan karena memberikan feel modern tanpa bergantung pada glassmorphism.

---

#### Tahap 3: Verifikasi

- [ ] Buka halaman login admin di browser
- [ ] Pastikan background light, bukan dark
- [ ] Pastikan card putih solid, bukan transparan
- [ ] Pastikan warna tombol & aksen hijau (bukan indigo)
- [ ] Pastikan form input memiliki border jelas pada background light
- [ ] Pastikan flash messages terbaca jelas
- [ ] Test responsive di mobile (< 480px)
- [ ] Pastikan toggle password masih berfungsi
- [ ] Pastikan loading state tombol masih berfungsi

---
---

## Issue #2: Ganti `<select>` Menjadi Autocomplete pada Halaman Absensi

### 🎯 Tujuan
Mengganti elemen `<select>` (dropdown list) untuk field "Nama Lengkap" peserta dengan **custom autocomplete/search input**, sehingga ketika peserta banyak (ratusan/ribuan), user bisa mengetik nama dan mendapatkan suggestion — bukan scroll dropdown panjang.

### 📁 File yang Perlu Diubah

| No | File | Aksi |
|----|------|------|
| 1 | `app/views/pegawai/absensi/index.php` | **MODIFY** — Ganti `<select>` dengan input + autocomplete |
| 2 | `public/assets/css/pegawai.css` | **MODIFY** — Tambah styles autocomplete |

### 🔍 Konteks Kode Saat Ini

Saat ini di `app/views/pegawai/absensi/index.php` (baris 43-51):

```php
<div class="form-group">
    <label for="nip" class="form-label">Nama Lengkap <span style="color: var(--danger-color)">*</span></label>
    <select name="nip" id="nip" class="form-control" required onchange="fetchPegawaiData()">
        <option value="">-- Pilih Nama Pegawai --</option>
        <?php foreach ($pegawaiList as $pegawai): ?>
            <option value="<?= e($pegawai['nip']) ?>"><?= e($pegawai['nama_lengkap']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
```

**Masalah:** Jika `$pegawaiList` berisi ratusan pegawai, dropdown `<select>` menjadi sangat panjang dan sulit digunakan.

**Solusi:** Gunakan **custom autocomplete** berbasis JavaScript murni (tanpa library eksternal) dengan data dari PHP yang sudah ada.

> **PENTING:** Jangan gunakan library eksternal (seperti Select2, Choices.js, dll). Cukup vanilla JavaScript agar tidak menambah dependency. Data pegawai sudah di-pass dari controller via `$pegawaiList`, jadi kita bisa membangun autocomplete dari data tersebut.

---

### 📝 Tahapan Implementasi

#### Tahap 1: Modifikasi HTML — `app/views/pegawai/absensi/index.php`

**1. Ganti blok `<select>` (baris 43-51) dengan custom autocomplete:**

```php
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
```

**2. Tambah data pegawai sebagai JSON di JavaScript (ganti bagian `<script>` di bawah):**

Di dalam blok `<script>` (setelah baris 88), tambahkan variabel data pegawai:

```php
<script>
    // Data pegawai dari PHP dikonversi ke JSON untuk autocomplete
    const pegawaiData = <?= json_encode(array_map(function($p) {
        return [
            'nip' => $p['nip'],
            'nama' => $p['nama_lengkap']
        ];
    }, $pegawaiList)) ?>;
</script>
```

**3. Tambah logika autocomplete (JavaScript vanilla):**

```javascript
// ============================================================
// Autocomplete Logic
// ============================================================
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
function selectItem(nip, nama) {
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
```

**4. Sesuaikan fungsi `fetchPegawaiData()`** (baris 89-117):

Fungsi ini sudah ada dan benar. Yang perlu dipastikan hanyalah: saat autocomplete memilih item, `document.getElementById('nip').value` sudah terisi NIP yang benar sebelum `fetchPegawaiData()` dipanggil.

> **CATATAN:** Fungsi `fetchPegawaiData()` yang ada saat ini sudah membaca `document.getElementById('nip').value`. Karena kita mengganti `<select id="nip">` menjadi `<input type="hidden" id="nip">`, fungsi ini tetap berfungsi tanpa perlu diubah.

---

#### Tahap 2: Tambah CSS Autocomplete — `public/assets/css/pegawai.css`

Tambahkan CSS berikut **di akhir file** `pegawai.css`:

```css
/* ============================================================
   Autocomplete Component
   ============================================================ */
.autocomplete-wrapper {
    position: relative;
}

.autocomplete-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.autocomplete-icon {
    position: absolute;
    left: 1rem;
    font-size: 1rem;
    pointer-events: none;
    z-index: 1;
    opacity: 0.5;
}

.autocomplete-input {
    padding-left: 2.75rem !important;
    padding-right: 2.5rem !important;
}

.autocomplete-clear {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    font-size: 1rem;
    color: var(--text-muted);
    cursor: pointer;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.autocomplete-clear:hover {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

/* Dropdown */
.autocomplete-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    box-shadow: var(--shadow-lg);
    max-height: 240px;
    overflow-y: auto;
    z-index: 100;
    
    /* Hidden by default */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.autocomplete-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Scrollbar styling */
.autocomplete-dropdown::-webkit-scrollbar {
    width: 6px;
}

.autocomplete-dropdown::-webkit-scrollbar-track {
    background: transparent;
}

.autocomplete-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

/* Item */
.autocomplete-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item:hover,
.autocomplete-item.active {
    background-color: #f0fdf4;
}

.autocomplete-item.active {
    background-color: var(--primary-light);
}

.autocomplete-item-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-main);
}

.autocomplete-item-name mark {
    background: rgba(16, 185, 129, 0.2);
    color: var(--primary-dark);
    padding: 0 2px;
    border-radius: 2px;
}

.autocomplete-item-nip {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 500;
}

/* Empty state */
.autocomplete-empty {
    padding: 1.25rem 1rem;
    text-align: center;
    color: var(--text-muted);
    font-size: 0.875rem;
    font-style: italic;
}
```

---

#### Tahap 3: Validasi Form

Karena `<select>` sekarang diganti menjadi `<input type="hidden">`, pastikan form validasi masih berjalan:

1. `<input type="hidden" name="nip" id="nip" required>` — atribut `required` pada hidden input **TIDAK** akan dicek browser secara default.
2. **Solusi:** Tambahkan validasi JavaScript sebelum submit:

```javascript
// Validasi sebelum submit
document.querySelector('form').addEventListener('submit', function(e) {
    if (!hiddenNip.value) {
        e.preventDefault();
        searchInput.focus();
        
        // Tampilkan pesan error
        searchInput.style.borderColor = 'var(--danger-color)';
        searchInput.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
        
        // Buat/tampilkan pesan error di bawah input
        let errorMsg = document.getElementById('autocomplete-error');
        if (!errorMsg) {
            errorMsg = document.createElement('small');
            errorMsg.id = 'autocomplete-error';
            errorMsg.style.cssText = 'color: var(--danger-color); display: block; margin-top: 0.5rem; font-size: 0.85rem;';
            errorMsg.textContent = 'Silakan pilih nama pegawai dari daftar.';
            wrapper.parentNode.appendChild(errorMsg);
        }
        
        // Hilangkan error setelah user mengetik
        searchInput.addEventListener('input', function removeError() {
            searchInput.style.borderColor = '';
            searchInput.style.boxShadow = '';
            if (errorMsg) errorMsg.remove();
            searchInput.removeEventListener('input', removeError);
        }, { once: true });
        
        return;
    }
});
```

---

#### Tahap 4: Verifikasi

- [ ] Buka halaman absensi di browser (contoh: `/?url=absensi?kegiatan=1`)
- [ ] Ketik 2-3 huruf nama pegawai → pastikan dropdown suggestion muncul
- [ ] Klik salah satu item → pastikan nama terisi, NIP/Jabatan/Tim Kerja otomatis terisi
- [ ] Ketik nama yang tidak ada → pastikan muncul "Tidak ditemukan"
- [ ] Tekan tombol ✕ (clear) → pastikan semua field tereset
- [ ] Test keyboard navigation: tekan ↓ ↑ Enter Escape
- [ ] Test submit tanpa memilih pegawai → pastikan muncul error
- [ ] Test submit setelah memilih pegawai → pastikan berhasil
- [ ] Test responsive di mobile
- [ ] Pastikan highlight teks yang cocok terlihat (warna hijau muda)

---

## 📊 Ringkasan Perubahan

| Issue | File Diubah | Estimasi Waktu | Kesulitan |
|-------|------------|----------------|-----------|
| #1 Login Redesign | `admin-auth.css`, `login.php` | 1-2 jam | ⭐⭐ Mudah |
| #2 Autocomplete Absensi | `absensi/index.php`, `pegawai.css` | 2-3 jam | ⭐⭐⭐ Sedang |

> **TIP untuk junior programmer:** Kerjakan Issue #1 terlebih dahulu karena lebih sederhana (hanya CSS + sedikit HTML). Setelah paham alurnya, lanjut ke Issue #2 yang melibatkan JavaScript.
