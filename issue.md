# Issue: Pilihan Lokasi Kegiatan & Peningkatan Upload Foto

---

## Fitur 1 — Toggle Pakai Lokasi / Tanpa Lokasi (Zoom Meeting)

### Latar Belakang

Saat ini form **Tambah Kegiatan** dan **Edit Kegiatan** selalu menampilkan input lokasi
beserta peta Leaflet dan input koordinat GPS. Namun untuk kegiatan daring (Zoom Meeting,
dll.) yang dilaksanakan dari rumah masing-masing, titik koordinat tidak relevan.

Solusi: tambahkan satu kolom `pakai_lokasi TINYINT(1) DEFAULT 1` pada tabel `kegiatan`,
lalu gunakan toggle checkbox di form untuk menampilkan atau menyembunyikan seluruh blok
lokasi secara kondisional.

---

### Tahapan Implementasi

#### Langkah 1 — Migrasi Database

Buat file baru `database/migration_pakai_lokasi.sql`:

```sql
-- MIGRASI: Tambah kolom pakai_lokasi pada tabel kegiatan
-- Jalankan sekali terhadap DB yang sudah ada.
-- Nilai default 1 = kegiatan luring (pakai lokasi).

ALTER TABLE `kegiatan`
    ADD COLUMN `pakai_lokasi` TINYINT(1) NOT NULL DEFAULT 1
        COMMENT '1 = luring (ada koordinat), 0 = daring (Zoom, dll.)'
        AFTER `deskripsi_kegiatan`;
```

Jalankan:
```bash
mysql -u root -p aksi_kebal < database/migration_pakai_lokasi.sql
```

Setelah migrasi berhasil, tambahkan juga kolom ini ke dump utama `database/aksi_kebal.sql`
agar fresh install juga punya kolom tersebut.

---

#### Langkah 2 — Model (`app/models/KegiatanModel.php`)

Di `create()` (baris 161–181), tambahkan ke INSERT query:

```php
// Tambah di INSERT:
// kolom:  ... radius_meter, pakai_lokasi, status_kegiatan)
// value:  ... :radius, :pakai_lokasi, 'Draft')

$this->db->bind(':pakai_lokasi', isset($data['pakai_lokasi']) ? 1 : 0, PDO::PARAM_INT);
```

Di `update()` (baris 183–212), tambahkan ke SET clause:

```php
// Tambah di SET:
// pakai_lokasi = :pakai_lokasi,

$this->db->bind(':pakai_lokasi', isset($data['pakai_lokasi']) ? 1 : 0, PDO::PARAM_INT);
```

---

#### Langkah 3 — Controller (`app/controllers/AdminController.php`)

Cari method `kegiatan_create` (baris 1064). Controller sudah menggunakan helper
`input()` untuk semua field — ikuti pola yang sama. Sisipkan tepat **setelah** blok
array `$data` didefinisikan (setelah baris `'radius_meter' => input('radius_meter') ?: 50`):

```php
// Checkbox yang tidak dicentang tidak mengirimkan key sama sekali ke $_POST.
// Karena itu kita periksa dengan isset(), bukan input() yang mengembalikan null/empty
// untuk keduanya (key tidak ada maupun value kosong).
$data['pakai_lokasi'] = isset($_POST['pakai_lokasi']) ? 1 : 0;

// Jika tidak pakai lokasi, kosongkan koordinat agar tidak tersimpan
if (!$data['pakai_lokasi']) {
    $data['latitude_kegiatan']  = null;
    $data['longitude_kegiatan'] = null;
    $data['radius_meter']       = null;
    $data['lokasi_kegiatan']    = null;
}
```

Lakukan hal yang sama di method `kegiatan_update` yang ada di bawahnya.

> **logAktivitas:** Method `kegiatan_create` (baris 1094) dan update (baris 1156)
> sudah memanggil `$this->logAktivitas(...)` — **tidak perlu ditambah lagi**.
> Perubahan `pakai_lokasi` tercatat otomatis bersama setiap save/update kegiatan.

---

#### Langkah 4 — View: Form Tambah (`app/views/admin/kegiatan/create.php`)

**4a.** Tambahkan toggle checkbox di atas blok lokasi (setelah baris 59):

```html
<!-- === Toggle Lokasi === -->
<div class="form-group" style="margin-bottom: 1rem;">
    <label class="form-label" style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
        <input type="checkbox" id="toggle_lokasi" name="pakai_lokasi" value="1"
               checked style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);">
        <span>Kegiatan ini <strong>memakai lokasi fisik</strong> (luring)</span>
    </label>
    <p style="margin: 0.35rem 0 0 1.85rem; color: var(--text-muted); font-size: 0.8rem;">
        Nonaktifkan jika kegiatan dilaksanakan daring (Zoom Meeting, Google Meet, dll.)
        — koordinat GPS tidak diperlukan.
    </p>
</div>

<!-- Bungkus SEMUA elemen lokasi yang sudah ada ke dalam div ini -->
<div id="blok-lokasi">
    <!-- input lokasi_kegiatan, URL Google Maps, div#map, hidden lat/lng, grid radius -->
</div>
```

**4b.** Tambahkan script toggle di blok `<script>` yang sudah ada (tepat sebelum `if (document.getElementById('map')) {`):

```javascript
// === Toggle Lokasi ===
const toggleLokasi = document.getElementById('toggle_lokasi');
const blokLokasi   = document.getElementById('blok-lokasi');

function applyToggleLokasi() {
    if (toggleLokasi.checked) {
        blokLokasi.style.display = '';
    } else {
        blokLokasi.style.display = 'none';
        // Kosongkan koordinat agar tidak tersubmit
        document.getElementById('latitude_kegiatan').value  = '';
        document.getElementById('longitude_kegiatan').value = '';
    }
}

toggleLokasi.addEventListener('change', applyToggleLokasi);
applyToggleLokasi(); // jalankan sekali saat load
```

---

#### Langkah 5 — View: Form Edit (`app/views/admin/kegiatan/edit.php`)

Sama persis dengan Langkah 4. Satu-satunya perbedaan: checkbox di-pre-fill dari DB:

```html
<input type="checkbox" id="toggle_lokasi" name="pakai_lokasi" value="1"
       <?= !empty($kegiatan['pakai_lokasi']) ? 'checked' : '' ?>
       style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);">
```

Script JS identik dengan Langkah 4b.

---

#### Langkah 6 — View: Formulir Absensi Pegawai (`app/views/pegawai/absensi/index.php`)

Kondisikan blok **Verifikasi Lokasi** (`#lokasi-status`) dengan PHP:

```php
<?php if (!empty($kegiatan['pakai_lokasi'])): ?>
    <div id="lokasi-status" class="lokasi-status-container">
        <!-- semua konten #lokasi-loading, #lokasi-ok, #lokasi-fail, #lokasi-error -->
    </div>
<?php endif; ?>
```

Tambahkan guard pada fungsi `detectLocation()` di JS:

```javascript
<?php if (empty($kegiatan['pakai_lokasi'])): ?>
// Kegiatan daring — skip deteksi lokasi
function detectLocation() { /* noop */ }
<?php else: ?>
function detectLocation() {
    // ... kode asli yang sudah ada ...
}
<?php endif; ?>
```

> **Catatan:** Validasi jarak di `AbsensiController::submit()` (baris 148–175) sudah
> otomatis terlewati karena guard `!empty($kegiatan['latitude_kegiatan'])` yang sudah ada —
> **controller absensi tidak perlu diubah sama sekali**.

---

#### Ringkasan File yang Diubah — Fitur 1

| File | Jenis Perubahan |
|---|---|
| `database/migration_pakai_lokasi.sql` | **Baru** — DDL satu kolom |
| `database/aksi_kebal.sql` | Tambah kolom di CREATE TABLE (satu baris) |
| `app/models/KegiatanModel.php` | Tambah bind `:pakai_lokasi` di `create()` & `update()` |
| `app/controllers/AdminController.php` | Ambil `$_POST['pakai_lokasi']` & null-kan koordinat jika daring |
| `app/views/admin/kegiatan/create.php` | Toggle checkbox + wrapper `#blok-lokasi` + JS |
| `app/views/admin/kegiatan/edit.php` | Sama, checkbox di-pre-fill dari DB |
| `app/views/pegawai/absensi/index.php` | Kondisikan blok GPS dengan `pakai_lokasi` |

---

---

## Fitur 2 — Upload Foto Kehadiran hingga 10 MB (Kompresi Otomatis < 1 MB)

### Latar Belakang

Batas foto kehadiran saat ini:

| Lapisan | Nilai saat ini | Lokasi kode |
|---|---|---|
| JS client (`handleFotoUpload`) | 2 MB | `index.php` baris 403 |
| PHP backend (`submit()`) | 5 MB | `AbsensiController.php` baris 204 |
| Kompresi GD | Quality 75, resize max 1920px | baris 221–298 |

Pegawai sering mengambil foto langsung dari kamera HP (4–8 MB). Permintaan: naikkan batas
upload ke **10 MB**, lalu **kompres otomatis di backend menjadi < 1 MB** menggunakan GD
(sudah tersedia, tidak ada dependency baru).

---

### Tahapan Implementasi

#### Langkah 1 — Konfigurasi Server PHP (`php.ini`)

Edit `php.ini`:

```ini
upload_max_filesize = 12M
post_max_size       = 13M
```

> **XAMPP:** Edit `C:\xampp\php\php.ini`, kemudian **restart Apache**.
> **Laragon:** Edit `C:\laragon\bin\php\php-x.x.x\php.ini`, restart service.
>
> `post_max_size` harus **lebih besar** dari `upload_max_filesize`.

Verifikasi: akses `phpinfo()` di browser dan cari `upload_max_filesize`.

---

#### Langkah 2 — JS Client: Naikkan Batas Validasi

Di `app/views/pegawai/absensi/index.php`, fungsi `handleFotoUpload` (baris 394):

```diff
- if (file.size > 2 * 1024 * 1024) {
+ if (file.size > 10 * 1024 * 1024) {
```

```diff
- text: 'Ukuran file maksimal 2MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB',
+ text: 'Ukuran file maksimal 10MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB',
```

Ubah teks petunjuk di bawah input file (baris 111–112):

```diff
- <small ...>Format: JPG/PNG, Maksimal: 2MB.</small>
+ <small ...>Format: JPG/PNG, Maksimal: 10MB. Foto dikompres otomatis oleh server.</small>
```

---

#### Langkah 3 — PHP Backend: Naikkan Batas & Kompresi Adaptif

**3a.** Naikkan batas validasi PHP di `app/controllers/AbsensiController.php` baris 204:

```diff
- if ($file_size > 5 * 1024 * 1024) {
-     setFlash('error', 'Ukuran foto maksimal 5MB.');
+ if ($file_size > 10 * 1024 * 1024) {
+     setFlash('error', 'Ukuran foto maksimal 10MB.');
```

**3b.** Ganti keseluruhan blok `try { ... } catch` kompresi GD (baris 222–298) dengan
versi loop adaptif berikut:

```php
// Proses Kompresi GD — hasil wajib < 1 MB
try {
    // 1. Baca gambar ke resource GD
    if ($file_ext === 'png') {
        $source_image = @imagecreatefrompng($tmp_name);
    } else {
        $source_image = @imagecreatefromjpeg($tmp_name);
    }

    if (!$source_image) {
        throw new Exception('Gagal membaca file gambar.');
    }

    // 2. Fix orientasi EXIF (foto portrait dari HP)
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($tmp_name);
        if ($exif && isset($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3: $source_image = imagerotate($source_image, 180, 0);  break;
                case 6: $source_image = imagerotate($source_image, -90, 0); break;
                case 8: $source_image = imagerotate($source_image, 90, 0);  break;
            }
        }
    }

    // 3. Resize max 1920px (proporsi dipertahankan)
    $width   = imagesx($source_image);
    $height  = imagesy($source_image);
    $max_dim = 1920;

    if ($width > $max_dim || $height > $max_dim) {
        if ($width > $height) {
            $new_width  = $max_dim;
            $new_height = (int)($height * ($max_dim / $width));
        } else {
            $new_height = $max_dim;
            $new_width  = (int)($width * ($max_dim / $height));
        }

        $resized = imagecreatetruecolor($new_width, $new_height);
        $white   = imagecolorallocate($resized, 255, 255, 255);
        imagefill($resized, 0, 0, $white);
        imagecopyresampled($resized, $source_image, 0, 0, 0, 0,
                           $new_width, $new_height, $width, $height);
        imagedestroy($source_image);
        $source_image = $resized;
    } elseif ($file_ext === 'png') {
        // PNG transparan: beri background putih sebelum convert ke JPEG
        $bg    = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white);
        imagecopy($bg, $source_image, 0, 0, 0, 0, $width, $height);
        imagedestroy($source_image);
        $source_image = $bg;
    }

    // 4. Kompresi adaptif: mulai quality 75, turunkan 10 per iterasi sampai < 1 MB
    $target_max_bytes = 1 * 1024 * 1024; // 1 MB
    $quality          = 75;
    $saved            = false;

    while ($quality >= 30) {
        // Encode ke buffer memori dulu (tanpa menyentuh disk)
        ob_start();
        imagejpeg($source_image, null, $quality);
        $image_data = ob_get_clean();

        if (strlen($image_data) <= $target_max_bytes) {
            // Ukuran sudah oke, simpan ke disk
            if (file_put_contents($target_file, $image_data) === false) {
                throw new Exception('Gagal menyimpan file gambar hasil kompresi.');
            }
            $saved = true;
            break;
        }

        $quality -= 10; // Coba quality lebih rendah
    }

    if (!$saved) {
        // Worst case: paksa simpan dengan quality 20
        ob_start();
        imagejpeg($source_image, null, 20);
        $image_data = ob_get_clean();
        if (file_put_contents($target_file, $image_data) === false) {
            throw new Exception('Gagal menyimpan file gambar hasil kompresi.');
        }
    }

    imagedestroy($source_image);

} catch (Exception $e) {
    setFlash('error', 'Kompresi foto gagal: ' . $e->getMessage());
    $this->redirect('absensi?kegiatan=' . $redirect_kegiatan);
    return;
}
```

**Mengapa loop ini aman dan tidak membebani server:**

| Aspek | Penjelasan |
|---|---|
| I/O disk | Hanya **satu kali tulis** (`file_put_contents`); semua iterasi di memori RAM |
| Iterasi | Maksimum 6 kali (75→65→55→45→35→20); tiap pass < 50ms untuk foto 10 MP |
| Dependency | Nol — GD sudah aktif di proyek (disebutkan di AGENTS.md) |
| Lebih baik dari | `exec('convert ...')`, FPDF, atau Ghostscript yang butuh binary eksternal |

---

#### Ringkasan File yang Diubah — Fitur 2

| File | Jenis Perubahan |
|---|---|
| `php.ini` | Naikkan `upload_max_filesize = 12M`, `post_max_size = 13M` |
| `app/views/pegawai/absensi/index.php` | Ubah batas validasi JS 2 MB → 10 MB + teks petunjuk |
| `app/controllers/AbsensiController.php` | Ubah batas PHP 5 MB → 10 MB + ganti blok kompresi GD dengan loop adaptif |

---

---

## Checklist QA Sebelum Merge

### Fitur 1 — Toggle Lokasi

- [ ] **Tambah kegiatan luring:** form lokasi tampil, koordinat tersimpan → absensi pegawai memvalidasi GPS seperti biasa.
- [ ] **Tambah kegiatan daring:** form lokasi tersembunyi, koordinat NULL tersimpan → pegawai tidak diminta GPS saat absensi.
- [ ] **Edit luring → daring:** checkbox di-uncheck, blok lokasi hilang, koordinat tersimpan NULL.
- [ ] **Edit daring → luring:** checkbox dicentang, blok lokasi muncul, admin bisa memilih titik peta.
- [ ] **View absensi pegawai (daring):** blok "Verifikasi Lokasi" tidak muncul sama sekali.
- [ ] **Fresh install:** `aksi_kebal.sql` sudah punya kolom `pakai_lokasi` sehingga tidak error.

### Fitur 2 — Upload Foto

- [ ] **Upload foto 8 MB:** tidak ditolak client, tersimpan ke disk < 1 MB.
- [ ] **Upload foto 11 MB:** ditolak di client dengan pesan "Ukuran file maksimal 10MB."
- [ ] **Upload foto 1 MB:** tersimpan langsung dengan quality 75 (satu iterasi saja).
- [ ] **Upload file bukan gambar (PDF, exe):** tetap ditolak oleh validasi MIME.
- [ ] **Orientasi EXIF foto portrait HP:** tidak terbalik setelah kompresi.
- [ ] **Cek php.ini efektif:** `phpinfo()` menunjukkan `upload_max_filesize = 12M`.
