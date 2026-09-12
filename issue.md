# Issue: Standardisasi UI Tombol Upload Files

**Tanggal:** 2026-09-12  
**Prioritas:** Medium  
**Kategori:** UI/UX Consistency  
**Skill Terkait:** Impeccable v4.1.1

---

## 📋 Deskripsi Masalah

Saat ini terdapat **2 tombol upload file** di aplikasi yang menggunakan tampilan default browser (tidak konsisten dengan design system "The Modern Institution"). Implementasi saat ini:

1. **Upload Foto Kehadiran** (`app/views/pegawai/absensi/index.php` line 99)
2. **Upload Bukti Ketidakhadiran** (`app/views/pegawai/absensi/index.php` line 179)

### Masalah yang Ditemukan:

- ❌ Tampilan menggunakan file input default browser (tidak menarik)
- ❌ Tidak konsisten dengan design system emerald (#10b981) yang ada
- ❌ Validasi menggunakan `alert()` native (melanggar AGENTS.md - harus pakai SweetAlert2)
- ❌ Tidak ada visual feedback yang jelas saat file dipilih
- ❌ Tidak ada drag & drop support

---

## 🎯 Target Hasil

Membuat komponen upload file yang:
- ✅ Konsisten dengan design system "The Modern Institution"
- ✅ Menggunakan warna emerald (#10b981) sebagai primary color
- ✅ Menggunakan SweetAlert2 untuk semua alert/notifikasi
- ✅ Menampilkan preview image yang jelas
- ✅ Memberikan feedback visual yang baik
- ✅ Support drag & drop (bonus, opsional)

---

## 🔍 File yang Perlu Dimodifikasi

1. **app/views/pegawai/absensi/index.php** (baris 99 & 179)
2. **public/css/pegawai.css** (tambah styling baru)
3. **app/views/pegawai/absensi/index.php** (JavaScript - ganti alert dengan SweetAlert2)

---

## 📝 Step-by-Step Implementation

### **STEP 1: Baca Design System** ⏱️ 5 menit

Baca file berikut untuk memahami design tokens:
- `DESIGN.md` - Lihat bagian Colors, Typography, Spacing
- `AGENTS.md` - Lihat bagian SweetAlert2 rules

**Key tokens yang akan dipakai:**
```
Primary Color: #10b981 (emerald-500)
Hover Color: #059669 (emerald-600)
Background: #ffffff
Border Radius: 0.75rem (12px)
Font: Poppins
Shadow: 0 2px 8px rgba(0,0,0,0.1)
```

---

### **STEP 2: Backup File Original** ⏱️ 2 menit

Sebelum edit, backup dulu:
```bash
# Di folder project root
cp app/views/pegawai/absensi/index.php app/views/pegawai/absensi/index.php.backup
```

---

### **STEP 3: Buat CSS untuk Custom File Upload** ⏱️ 15 menit

Buka: `public/css/pegawai.css`

**Tambahkan di bagian paling bawah file:**

```css
/* ========================================
   CUSTOM FILE UPLOAD COMPONENT
   ======================================== */

/* Container untuk custom file upload */
.custom-file-upload {
    position: relative;
    width: 100%;
    margin-bottom: 1rem;
}

/* Hide input file default */
.custom-file-upload input[type="file"] {
    position: absolute;
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    z-index: -1;
}

/* Label yang jadi tombol upload */
.custom-file-upload label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
    background: #ffffff;
    border: 2px dashed #d1d5db;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

/* Hover state */
.custom-file-upload label:hover {
    border-color: #10b981;
    background: #f0fdf4;
    color: #10b981;
}

/* Focus state (keyboard navigation) */
.custom-file-upload input[type="file"]:focus + label {
    outline: 2px solid #10b981;
    outline-offset: 2px;
}

/* State ketika file sudah dipilih */
.custom-file-upload.has-file label {
    border-color: #10b981;
    background: #f0fdf4;
    border-style: solid;
}

/* Icon dalam tombol upload */
.custom-file-upload label i {
    font-size: 1.5rem;
}

/* Text info file yang dipilih */
.file-info {
    margin-top: 0.5rem;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #374151;
    display: none;
}

.file-info.active {
    display: block;
}

.file-info strong {
    color: #10b981;
    font-weight: 600;
}

/* Preview container styling yang lebih baik */
.upload-preview {
    margin-top: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 0.75rem;
    display: none;
}

.upload-preview.active {
    display: block;
}

.upload-preview img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Button untuk remove file */
.remove-file-btn {
    margin-top: 0.5rem;
    padding: 0.5rem 1rem;
    background: #ef4444;
    color: #ffffff;
    border: none;
    border-radius: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.remove-file-btn:hover {
    background: #dc2626;
}
```

**Simpan file.**

---

### **STEP 4: Update HTML untuk Upload #1 (Foto Kehadiran)** ⏱️ 10 menit

Buka: `app/views/pegawai/absensi/index.php`

**Cari baris 99 yang isinya:**
```php
<input type="file" name="foto" id="foto" class="form-control" 
       accept="image/jpeg, image/png" onchange="previewImage(event)">
```

**Ganti dengan:**
```php
<div class="custom-file-upload" id="foto-upload-wrapper">
    <input type="file" 
           name="foto" 
           id="foto" 
           accept="image/jpeg, image/png" 
           onchange="handleFotoUpload(event)">
    <label for="foto">
        <i class='bx bx-cloud-upload'></i>
        <span>Klik untuk upload foto atau drag & drop</span>
    </label>
</div>
<div class="file-info" id="foto-info"></div>
```

**Cari bagian preview foto (sekitar baris 102-108)** yang ada:
```php
<div id="preview-foto" style="display: none; margin-top: 10px;">
```

**Ganti dengan:**
```php
<div id="preview-foto" class="upload-preview">
```

**Simpan file.**

---

### **STEP 5: Update HTML untuk Upload #2 (Bukti Ketidakhadiran)** ⏱️ 10 menit

Masih di file yang sama: `app/views/pegawai/absensi/index.php`

**Cari baris 179 yang isinya:**
```php
<input type="file" name="file_bukti" id="file_bukti" class="form-control"
       accept="image/jpeg, image/png, application/pdf" onchange="previewFileBukti(event)">
```

**Ganti dengan:**
```php
<div class="custom-file-upload" id="bukti-upload-wrapper">
    <input type="file" 
           name="file_bukti" 
           id="file_bukti" 
           accept="image/jpeg, image/png, application/pdf" 
           onchange="handleBuktiUpload(event)">
    <label for="file_bukti">
        <i class='bx bx-cloud-upload'></i>
        <span>Klik untuk upload bukti (foto/PDF)</span>
    </label>
</div>
<div class="file-info" id="bukti-info"></div>
```

**Cari bagian preview bukti (sekitar baris 182-188)** yang ada:
```php
<div id="preview-file-bukti" style="display: none; margin-top: 10px;">
```

**Ganti dengan:**
```php
<div id="preview-file-bukti" class="upload-preview">
```

**Simpan file.**

---

### **STEP 6: Update JavaScript - Ganti Alert dengan SweetAlert2** ⏱️ 20 menit

Masih di file yang sama: `app/views/pegawai/absensi/index.php`

**Cari fungsi `previewImage(event)` sekitar baris 460-480**

**Ganti SELURUH fungsi dengan:**
```javascript
function handleFotoUpload(event) {
    const file = event.target.files[0];
    const wrapper = document.getElementById('foto-upload-wrapper');
    const fileInfo = document.getElementById('foto-info');
    const preview = document.getElementById('preview-foto');
    
    if (!file) return;
    
    // Validasi ukuran file (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 2MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB',
            confirmButtonColor: '#10b981'
        });
        event.target.value = '';
        return;
    }
    
    // Validasi tipe file
    const allowedTypes = ['image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Didukung',
            text: 'Hanya file JPG dan PNG yang diperbolehkan',
            confirmButtonColor: '#10b981'
        });
        event.target.value = '';
        return;
    }
    
    // Update UI
    wrapper.classList.add('has-file');
    fileInfo.innerHTML = '<strong>File dipilih:</strong> ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
    fileInfo.classList.add('active');
    
    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview Foto"><p style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">Preview foto kehadiran</p>';
        preview.classList.add('active');
    };
    reader.readAsDataURL(file);
}
```

**Cari fungsi `previewFileBukti(event)` sekitar baris 482-510**

**Ganti SELURUH fungsi dengan:**
```javascript
function handleBuktiUpload(event) {
    const file = event.target.files[0];
    const wrapper = document.getElementById('bukti-upload-wrapper');
    const fileInfo = document.getElementById('bukti-info');
    const preview = document.getElementById('preview-file-bukti');
    
    if (!file) return;
    
    // Validasi ukuran file (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 2MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB',
            confirmButtonColor: '#10b981'
        });
        event.target.value = '';
        return;
    }
    
    // Validasi tipe file
    const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'Format File Tidak Didukung',
            text: 'Hanya file JPG, PNG, dan PDF yang diperbolehkan',
            confirmButtonColor: '#10b981'
        });
        event.target.value = '';
        return;
    }
    
    // Update UI
    wrapper.classList.add('has-file');
    fileInfo.innerHTML = '<strong>File dipilih:</strong> ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
    fileInfo.classList.add('active');
    
    // Preview
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview Bukti"><p style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">Preview bukti ketidakhadiran</p>';
            preview.classList.add('active');
        };
        reader.readAsDataURL(file);
    } else if (file.type === 'application/pdf') {
        preview.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="bx bxs-file-pdf" style="font-size: 3rem; color: #ef4444;"></i><p style="margin-top: 0.5rem; font-weight: 500;">File PDF: ' + file.name + '</p></div>';
        preview.classList.add('active');
    }
}
```

**Simpan file.**

---

### **STEP 7: Testing** ⏱️ 10 menit

1. **Buka halaman absensi pegawai** di browser
2. **Test Upload Foto Kehadiran:**
   - Klik area upload → file picker harus muncul
   - Pilih image JPG/PNG < 2MB → harus muncul preview
   - Pilih file > 2MB → harus muncul SweetAlert2 error (BUKAN alert biasa)
   - Pilih file PDF → harus muncul SweetAlert2 error format tidak didukung

3. **Test Upload Bukti Ketidakhadiran:**
   - Klik area upload → file picker harus muncul
   - Pilih image → harus muncul preview image
   - Pilih PDF → harus muncul icon PDF dengan nama file
   - Pilih file > 2MB → harus muncul SweetAlert2 error

4. **Check Visual:**
   - Tombol upload harus pakai border dashed abu-abu
   - Hover harus berubah warna emerald (#10b981)
   - Setelah file dipilih, border jadi solid emerald
   - Preview image harus ada shadow dan rounded

---

### **STEP 8: Verifikasi dengan Impeccable Skill** ⏱️ 5 menit

Jalankan critique check:
```bash
# Pastikan di folder project root
# Jika ada command impeccable, jalankan:
impeccable critique app/views/pegawai/absensi/index.php
```

Atau manual check:
- ✅ Tidak ada native `alert()` 
- ✅ Semua error pakai SweetAlert2
- ✅ Warna emerald (#10b981) konsisten
- ✅ Border radius 0.75rem sesuai design system
- ✅ Font Poppins terpakai

---

## 🎨 Hasil Akhir yang Diharapkan

**BEFORE:**
```
[Browse... ] <-- Tombol default browser (jelek)
```

**AFTER:**
```
┌─────────────────────────────────────┐
│   ☁️  Klik untuk upload foto atau   │
│       drag & drop                   │  <-- Border dashed abu-abu
└─────────────────────────────────────┘
     ↓ (hover)
┌─────────────────────────────────────┐
│   ☁️  Klik untuk upload foto atau   │
│       drag & drop                   │  <-- Border dashed emerald + bg hijau muda
└─────────────────────────────────────┘
     ↓ (file dipilih)
┌─────────────────────────────────────┐
│   ☁️  Klik untuk upload foto atau   │
│       drag & drop                   │  <-- Border solid emerald + bg hijau muda
└─────────────────────────────────────┘
File dipilih: foto.jpg (245.6 KB)

┌─────────────────────────────────────┐
│  [Preview Image]                    │
│  Preview foto kehadiran             │
└─────────────────────────────────────┘
```

---

## 📚 Referensi

- **Design System:** `DESIGN.md`
- **Agent Rules:** `AGENTS.md` (line 57-69 tentang SweetAlert2)
- **Impeccable Skill:** `.agents/skills/impeccable/skill.md`
- **File Terkait:**
  - `app/views/pegawai/absensi/index.php` (line 99, 179)
  - `public/css/pegawai.css`

---

## ⚠️ Catatan Penting

1. **JANGAN pakai `alert()` native** → Selalu pakai SweetAlert2
2. **Jangan hardcode warna** → Pakai color tokens dari DESIGN.md
3. **Test di berbagai browser** → Chrome, Firefox, Edge
4. **Backup file sebelum edit** → Biar bisa rollback kalau error
5. **Commit dengan message yang jelas:**
   ```
   feat: standardize file upload UI with custom component
   
   - Replace native file input with custom styled component
   - Add emerald color theme consistency
   - Replace alert() with SweetAlert2 (AGENTS.md compliance)
   - Add file info display and better preview styling
   ```

---

## 🚀 Estimasi Waktu

- Junior Programmer: **1-1.5 jam**
- AI Murah (GPT-3.5/Claude Haiku): **20-30 menit**
- Senior Developer: **30 menit**

---

## ✅ Checklist Selesai

Setelah implementasi, pastikan semua ini ✅:

- [ ] CSS custom file upload sudah ditambahkan di `pegawai.css`
- [ ] HTML upload #1 (foto) sudah diupdate dengan custom wrapper
- [ ] HTML upload #2 (bukti) sudah diupdate dengan custom wrapper
- [ ] JavaScript `handleFotoUpload()` sudah dibuat (ganti alert dengan SweetAlert2)
- [ ] JavaScript `handleBuktiUpload()` sudah dibuat (ganti alert dengan SweetAlert2)
- [ ] Testing manual sudah dilakukan (semua skenario)
- [ ] Tidak ada lagi `alert()` native di kode
- [ ] Visual sesuai dengan design system (emerald color, Poppins font, dll)
- [ ] File backup sudah dibuat
- [ ] Commit sudah dilakukan dengan message yang jelas

---

**Status:** 🔴 Open  
**Assigned to:** -  
**Due date:** -  

---

_Generated by Kiro AI - 2026-09-12_
