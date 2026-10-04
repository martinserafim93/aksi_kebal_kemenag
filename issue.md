# Issue: Laporan Kehadiran Absensi (PDF & CSV) — Tambah Kolom "Keterangan" + Samakan Ekspor CSV dengan PDF

**Modul:** Admin → Absensi → Detail Kegiatan → Ekspor PDF / Ekspor CSV
**Tipe:** Enhancement
**File yang diubah:** 4 file — `AbsensiModel.php`, `AdminController.php`, `pdf_export.php`, `detail.php`
**Estimasi:** ± 45 menit | **Risiko:** Rendah–Sedang (tidak ada perubahan DB/route; ada refactor kecil di controller)

---

## 1. Deskripsi

### 1.1 Kolom Keterangan
Laporan PDF kini hanya punya 5 kolom: `No | NIP | Nama Pegawai | Waktu Submit | Status Kehadiran`.
Admin perlu melihat **alasan** pegawai berstatus *Tidak Hadir* langsung di dokumen. Tambahkan kolom **Keterangan** untuk filter:

| Filter (`?filter=`) | Label laporan | Kolom Keterangan |
|---|---|---|
| `tidak_hadir` | Pegawai Tidak Hadir | ✅ Ditampilkan |
| `semua` (default) | Semua Pegawai | ✅ Ditampilkan |
| `hadir` | Pegawai Hadir | ❌ Tidak ada |
| `tidak_absen` | Pegawai Tidak Absen | ❌ Tidak ada |

Isi kolom Keterangan per baris:

| Status baris | Isi |
|---|---|
| `Tidak Hadir` | `absensi.alasan_tidak_hadir`; jika kosong → `-` |
| `Hadir` | `-` |
| `Tidak Melakukan Absensi` (hanya di filter `semua`) | `-` |

### 1.2 Ekspor CSV belum setara dengan PDF
Temuan di kode saat ini (`AdminController::absensi_export`):

| Aspek | PDF | CSV (sekarang) |
|---|---|---|
| Filter (Semua / Hadir / Tidak Hadir / Tidak Absen) | ✅ 4 pilihan di dropdown | ❌ Tidak ada; satu tombol saja |
| Pegawai yang **belum mengisi absen** | ✅ ikut di filter `semua` & `tidak_absen` | ❌ Tidak pernah muncul (hanya baris tabel `absensi`) |
| Kolom Keterangan | ✅ (setelah issue ini) | ❌ |
| Waktu Submit kosong | `-` | ❌ akan jadi `01 Jan 1970` jika baris sintetis dipakai |

**Target:** CSV memakai **sumber data & aturan filter yang sama persis** dengan PDF, punya dropdown filter yang sama, dan kolom Keterangan dengan aturan yang sama.

## 2. Hasil Penelusuran (jangan diulang)

1. Tombol ekspor ada di [detail.php](app/views/admin/absensi/detail.php) baris ±84–105. PDF = dropdown 4 filter (`admin/absensi-export-pdf/{kode}?filter=...`), CSV = satu link `admin/absensi-export/{kode}`. Skrip penutup dropdown di baris ±292.
2. [`AdminController::absensi_export_pdf`](app/controllers/AdminController.php#L1423): menyusun data berdasarkan `$filter` (blok `switch`, ±1456–1485), termasuk baris sintetis "Tidak Melakukan Absensi" untuk pegawai yang belum absen. Logika ini **belum bisa dipakai ulang** oleh CSV.
3. [`AdminController::absensi_export`](app/controllers/AdminController.php#L1371) (CSV): hanya `getAllFilteredForExport()`, tanpa filter.
4. `getAllFilteredForExport()` memakai `SELECT a.*` ([AbsensiModel.php:56](app/models/AbsensiModel.php#L56)) → `alasan_tidak_hadir` sudah terambil. Baris sintetis tidak punya key itu → wajib `?? ''` / diisi `null`.
5. Baris sintetis juga **tidak punya** `nama_kegiatan`, `jenis_kegiatan`, `tanggal_kegiatan`, `waktu_mulai`, `waktu_selesai`, `lokasi_kegiatan`. CSV lama membaca field-field itu dari `$row` → akan *undefined index*. Solusi: ambil dari `$kegiatan` (sudah dimuat controller, sama untuk semua baris).

### Keputusan desain (Ponytail)
- **Jangan** menaruh helper `private function` di `AdminController`. Router ([core/App.php](core/App.php#L46)) memakai `method_exists()` lalu `call_user_func_array()`; method private tetap lolos `method_exists` dan bisa dipicu lewat URL → error. Pindahkan logika bersama ke **`AbsensiModel`** (model tidak ter-routing).
- Satu method model baru `getDataLaporan()` menggantikan blok `switch` di PDF dan dipakai juga oleh CSV. Tidak ada query SQL baru.
- Jangan tambah library, route, atau kolom DB.

> **Catatan graphify:** `graphify-out/` bertanggal 2026-09-25 (bisa usang); dipakai hanya untuk orientasi `AdminController → AbsensiModel`. Semua klaim di atas sudah diverifikasi langsung ke source.

## 3. Aturan yang Wajib Dipatuhi

- Admin action: `Middleware::authAdmin()` sudah ada di kedua method — **jangan dihapus**.
- Model: pakai placeholder bernama + `bind()` (tidak ada SQL baru di issue ini; jangan menyisipkan variabel ke SQL).
- **Whitelist `$filter`** sebelum dipakai (log, nama file, view). Nilai di luar `hadir|tidak_hadir|tidak_absen` dianggap `semua`.
- PDF: `e()` untuk semua output. Ikuti DESIGN.md bagian *Print / Laporan (PDF)* — Times New Roman, teks `#000`, header tabel `#d1fae5`/`#334155`, `print-color-adjust: exact`. **Jangan** pakai Figtree/Poppins, warna, shadow, atau radius baru di view cetak. Jangan pakai FPDF.
- CSV: alasan adalah input bebas dari pegawai → cegah **CSV/formula injection** (awalan `= + - @` tab CR diberi tanda petik `'`).
- UI dropdown CSV: **tiru persis gaya dropdown PDF** (komponen yang sudah ada), jangan membuat gaya baru. Native `alert()` dilarang.
- Jangan ubah file selain 4 file di atas.

## 4. Tahapan Implementasi

Kerjakan **berurutan**. Cek `php -l <file>` setelah mengedit tiap file PHP.

### Langkah 1 — Model: tambah `getDataLaporan()`

File: [`app/models/AbsensiModel.php`](app/models/AbsensiModel.php). Tambahkan method berikut **tepat setelah** method `getPegawaiTidakAbsen()` (± baris 111, sebelum docblock `getStatistikLengkap`):

```php
    /**
     * Data laporan kehadiran per kegiatan sesuai filter (dipakai ekspor PDF & CSV).
     *
     * @param string $filter 'semua' | 'hadir' | 'tidak_hadir' | 'tidak_absen'
     * @return array Baris absensi; pegawai belum absen ditambahkan sebagai baris
     *               dengan status 'Tidak Melakukan Absensi'
     */
    public function getDataLaporan(int $id_kegiatan, string $filter = 'semua'): array
    {
        $tidakAbsen = array_map(fn($p) => [
            'nip'                => $p['nip'],
            'nama_lengkap'       => $p['nama_lengkap'],
            'status_kehadiran'   => 'Tidak Melakukan Absensi',
            'alasan_tidak_hadir' => null,
            'created_at'         => null,
        ], $filter === 'hadir' || $filter === 'tidak_hadir' ? [] : $this->getPegawaiTidakAbsen($id_kegiatan));

        if ($filter === 'tidak_absen') {
            return $tidakAbsen;
        }

        $absensi = $this->getAllFilteredForExport(['kegiatan' => $id_kegiatan]);

        if ($filter === 'hadir' || $filter === 'tidak_hadir') {
            $status = $filter === 'hadir' ? 'Hadir' : 'Tidak Hadir';
            return array_values(array_filter($absensi, fn($r) => $r['status_kehadiran'] === $status));
        }

        return array_merge($absensi, $tidakAbsen); // semua
    }
```

Perilaku harus identik dengan `switch` lama di PDF (urutan: absensi dulu, lalu pegawai belum absen).

### Langkah 2 — Controller PDF: pakai `getDataLaporan()`

File: [`AdminController.php`](app/controllers/AdminController.php), method `absensi_export_pdf` (± baris 1444–1485).

**Ganti** blok dari `$model = $this->model('AbsensiModel');` s.d. akhir `switch` (`}` penutup switch, ± baris 1485) menjadi:

```php
        $model = $this->model('AbsensiModel');
        $filter = query('filter', 'semua');
        if (!in_array($filter, ['hadir', 'tidak_hadir', 'tidak_absen'], true)) {
            $filter = 'semua';
        }

        $this->logAktivitas('ekspor', 'absensi', 'Mengekspor laporan absensi (PDF, filter: ' . $filter . ') kegiatan: ' . $kegiatan['nama_kegiatan'] . '.');

        $absensi = $model->getDataLaporan($kegiatan['id_kegiatan'], $filter);
```

Biarkan semua kode **setelahnya** (`$statistik = ...`, penandatangan, `$this->view(...)`) tidak berubah. Variabel `$semuaAbsensi` dan `$pegawaiTidakAbsen` tidak dipakai lagi — hapus bersama blok lama.

### Langkah 3 — View PDF: kolom Keterangan

File: [`app/views/admin/absensi/pdf_export.php`](app/views/admin/absensi/pdf_export.php).

**3a. CSS.** Di `<style>`, **setelah** blok `.text-center { ... }` (± baris 88), tambahkan:

```css
        .col-keterangan {
            text-align: left;
            overflow-wrap: anywhere;
            word-break: break-word;
        }
```

**3b. Flag kolom.** Tepat sebelum `<table class="data-table">` (± baris 199), tambahkan:

```php
    <?php
        // Kolom Keterangan hanya untuk laporan "Tidak Hadir" dan "Semua Pegawai"
        $showKeterangan = !in_array($filter ?? 'semua', ['hadir', 'tidak_absen'], true);
        $colspan = $showKeterangan ? 6 : 5;
    ?>
```

**3c. `<thead>`** (± baris 201–209) ganti menjadi:

```php
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="<?= $showKeterangan ? '15%' : '20%' ?>">NIP</th>
                <th width="<?= $showKeterangan ? '25%' : '35%' ?>">Nama Pegawai</th>
                <th width="<?= $showKeterangan ? '15%' : '20%' ?>">Waktu Submit</th>
                <th width="<?= $showKeterangan ? '15%' : '20%' ?>">Status Kehadiran</th>
                <?php if ($showKeterangan): ?>
                    <th width="25%">Keterangan</th>
                <?php endif; ?>
            </tr>
        </thead>
```

Total lebar harus 100%: dengan Keterangan `5+15+25+15+15+25`; tanpa `5+20+35+20+20`.

**3d. Baris kosong** (± baris 213): ubah `colspan="5"` menjadi `colspan="<?= $colspan ?>"`.

**3e. Sel di tiap baris.** Di `foreach`, **setelah** `<td>` Status Kehadiran (± baris 222) tambahkan:

```php
                        <?php if ($showKeterangan): ?>
                            <?php $alasan = trim((string) ($row['alasan_tidak_hadir'] ?? '')); ?>
                            <td class="col-keterangan"><?= ($row['status_kehadiran'] === 'Tidak Hadir' && $alasan !== '') ? e($alasan) : '-' ?></td>
                        <?php endif; ?>
```

Jangan ubah `page-break-inside`, `thead`, atau `@page` yang sudah ada (A4 portrait).

### Langkah 4 — Controller CSV: filter + Keterangan

File: [`AdminController.php`](app/controllers/AdminController.php), method `absensi_export` (± baris 1371–1422).

**Ganti seluruh isi setelah blok pengecekan `if (!$kegiatan) {...}`** (yaitu dari `$model = $this->model('AbsensiModel');` s.d. `exit;`) menjadi:

```php
        $model = $this->model('AbsensiModel');
        $filter = query('filter', 'semua');
        if (!in_array($filter, ['hadir', 'tidak_hadir', 'tidak_absen'], true)) {
            $filter = 'semua';
        }
        $absensi = $model->getDataLaporan($kegiatan['id_kegiatan'], $filter);
        $showKeterangan = $filter === 'tidak_hadir' || $filter === 'semua'; // sama dengan PDF

        $this->logAktivitas('ekspor', 'absensi', 'Mengekspor laporan absensi (CSV, filter: ' . $filter . ') kegiatan: ' . $kegiatan['nama_kegiatan'] . '.');

        $filename = "Laporan_Kehadiran_Pegawai_" . $filter . "_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        $header = ['No', 'NIP', 'Nama Pegawai', 'Kegiatan', 'Jenis Kegiatan', 'Tanggal', 'Waktu', 'Lokasi', 'Status Kehadiran', 'Waktu Submit'];
        if ($showKeterangan) {
            $header[] = 'Keterangan';
        }
        fputcsv($output, $header);

        // Data kegiatan sama untuk semua baris -> ambil dari $kegiatan (baris "belum absen" tidak punya field ini)
        $tanggal = date('d M Y', strtotime($kegiatan['tanggal_kegiatan']));
        $waktu = date('H:i', strtotime($kegiatan['waktu_mulai'])) . ' - ' . date('H:i', strtotime($kegiatan['waktu_selesai']));

        $no = 1;
        foreach ($absensi as $row) {
            $baris = [
                $no++,
                $row['nip'],
                $row['nama_lengkap'],
                $kegiatan['nama_kegiatan'],
                $kegiatan['jenis_kegiatan'],
                $tanggal,
                $waktu,
                $kegiatan['lokasi_kegiatan'],
                $row['status_kehadiran'],
                !empty($row['created_at']) ? date('d M Y, H:i', strtotime($row['created_at'])) : '-'
            ];
            if ($showKeterangan) {
                $alasan = trim((string) ($row['alasan_tidak_hadir'] ?? ''));
                if ($row['status_kehadiran'] !== 'Tidak Hadir' || $alasan === '') {
                    $baris[] = '-';
                } else {
                    // Cegah formula injection di Excel/Sheets
                    $baris[] = preg_match('/^[=+\-@\t\r]/', $alasan) ? "'" . $alasan : $alasan;
                }
            }
            fputcsv($output, $baris);
        }
        fclose($output);
        exit;
```

Catatan penting:
- Tanda `-` placeholder **tidak** boleh diberi awalan `'` (sebab itu pengecekan injection hanya pada `$alasan`, bukan hasil akhir).
- Urutan kolom lama tidak berubah; `Keterangan` selalu **kolom terakhir**.
- Baris sintetis memakai `'-'` untuk Waktu Submit (bukan `1970`).

### Langkah 5 — UI: dropdown filter untuk Export CSV

File: [`app/views/admin/absensi/detail.php`](app/views/admin/absensi/detail.php).

**5a. Samakan container PDF.** Pada `<div ... class="pdf-dropdown-container">` (± baris 84) ganti nama kelas menjadi `export-dropdown-container`. Pada atribut `onclick` tombol PDF (± baris 85) sisipkan di **awal** nilai onclick: `document.getElementById('csvDropdown').style.display = 'none';` (agar hanya satu dropdown terbuka).

**5b. Ganti link CSV** (± baris 103–105, elemen `<a ... class="btn btn-success" ...>Export CSV</a>`) dengan:

```php
            <div style="position: relative; display: inline-block;" class="export-dropdown-container">
                <button type="button" class="btn btn-success" aria-haspopup="true" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; padding: 0.5rem 1rem; cursor: pointer;" onclick="document.getElementById('pdfDropdown').style.display = 'none'; var d = document.getElementById('csvDropdown'); d.style.display = d.style.display === 'none' ? 'block' : 'none'; event.stopPropagation();">
                    <i class='bx bx-export'></i> Export CSV <i class='bx bx-chevron-down'></i>
                </button>
                <div id="csvDropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid var(--border-color); border-radius: 0.5rem; min-width: 200px; z-index: 50; overflow: hidden;">
                    <?php foreach (['semua' => 'Semua Pegawai', 'hadir' => 'Pegawai Hadir', 'tidak_hadir' => 'Pegawai Tidak Hadir', 'tidak_absen' => 'Pegawai Tidak Absen'] as $f => $label): ?>
                        <a href="<?= url('admin/absensi-export/' . $kegiatan['kode_kegiatan'] . '?filter=' . $f) ?>" style="display: block; padding: 0.75rem 1rem; color: var(--text-main); text-decoration: none; font-size: 0.85rem;<?= $f !== 'tidak_absen' ? ' border-bottom: 1px solid var(--border-color);' : '' ?>">
                            <?= e($label) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
```

(Tanpa `target="_blank"` — CSV adalah unduhan.)

**5c. Skrip penutup dropdown** (± baris 292) ganti seluruh baris `<script>...</script>` dengan:

```html
<script>document.addEventListener("click", function(event) { document.querySelectorAll(".export-dropdown-container > div[id$='Dropdown']").forEach(function(d) { if (d.style.display === "block" && !d.parentElement.contains(event.target)) { d.style.display = "none"; } }); });</script>
```

## 5. Skenario Uji Manual (wajib sebelum selesai)

Data uji: 1 kegiatan *Published* dengan ≥1 pegawai **Hadir**, ≥1 **Tidak Hadir** (alasan panjang ±300 karakter), ≥1 **Tidak Hadir** dengan alasan `=HYPERLINK("http://x","klik")`, ≥1 **Tidak Hadir** dengan alasan `<script>alert(1)</script>`, dan ≥1 pegawai yang belum absen.

### PDF (pratinjau cetak)
| # | Filter | Yang harus terlihat |
|---|---|---|
| 1 | `tidak_hadir` | 6 kolom; Keterangan berisi alasan; alasan panjang membungkus, tidak keluar tabel |
| 2 | `semua` | 6 kolom; Hadir = `-`; Tidak Hadir = alasan; "Tidak Melakukan Absensi" = `-` |
| 3 | `hadir` | **5 kolom**, identik dengan sebelumnya |
| 4 | `tidak_absen` | **5 kolom**, identik dengan sebelumnya |
| 5 | `tidak_hadir` tanpa data | "Tidak ada data kehadiran" melebar penuh 6 kolom |
| 6 | Alasan `<script>` | Tampil sebagai teks, **tidak** dieksekusi |
| 7 | `?filter=ngawur` | Diperlakukan `semua` (label + 6 kolom) |
| 8 | Semua | Header tabel hijau muda saat cetak; Times New Roman; tidak ada notice di `storage/logs/error.log` |
| 9 | Data > 1 halaman | Header berulang, baris tidak terpotong |

### CSV (buka di Excel/LibreOffice dan di editor teks)
| # | Aksi | Yang harus terlihat |
|---|---|---|
| 10 | Klik **Export CSV** | Dropdown 4 pilihan tampil, gaya sama dengan Export PDF; klik di luar menutupnya; membuka satu dropdown menutup yang lain |
| 11 | `semua` | Nama file `Laporan_Kehadiran_Pegawai_semua_*.csv`; 11 kolom (`Keterangan` terakhir); jumlah baris = jumlah pegawai; baris belum absen ada dengan Status `Tidak Melakukan Absensi`, Waktu Submit `-` |
| 12 | `tidak_hadir` | Hanya status Tidak Hadir; Keterangan berisi alasan |
| 13 | `hadir` | **10 kolom** (tanpa Keterangan), hanya status Hadir |
| 14 | `tidak_absen` | **10 kolom**, hanya pegawai belum absen; kolom Kegiatan/Tanggal/Waktu/Lokasi terisi (dari `$kegiatan`); tidak ada `1970` |
| 15 | Alasan `=HYPERLINK(...)` | Di file tampil `'=HYPERLINK(...)`, **tidak** dieksekusi sebagai rumus |
| 16 | `?filter=ngawur` | Diperlakukan `semua`; nama file memakai `semua` |
| 17 | Jumlah baris CSV vs PDF | Untuk filter yang sama, jumlah dan urutan NIP di CSV **sama** dengan PDF |
| 18 | Log aktivitas | Entri baru memuat `CSV, filter: <nilai>` di Log Aktivitas |
| 19 | Tanpa login | `admin/absensi-export/...` mengarahkan ke login (tidak berubah) |

## 6. Kriteria Selesai (Definition of Done)

- [ ] Hanya 4 file berubah (`git diff --stat`): `AbsensiModel.php`, `AdminController.php`, `pdf_export.php`, `detail.php`.
- [ ] `php -l` lolos untuk semua file PHP yang diubah.
- [ ] Tidak ada `private/protected function` baru di `AdminController`.
- [ ] Logika filter hanya ada di satu tempat (`AbsensiModel::getDataLaporan`); blok `switch` lama di PDF sudah dihapus.
- [ ] `$filter` di-whitelist di PDF dan CSV.
- [ ] PDF & CSV: Keterangan muncul hanya di `tidak_hadir` dan `semua`.
- [ ] Output alasan PDF memakai `e()`; CSV menetralkan awalan `= + - @ tab CR`.
- [ ] Skenario uji 1–19 lolos.
- [ ] Total lebar kolom PDF 100% di kedua mode; tidak ada warna/font baru di luar DESIGN.md bagian Print.

## 7. Di Luar Cakupan

- BOM UTF-8 / ubah delimiter CSV.
- Menampilkan keterangan untuk pegawai yang belum mengisi absen.
- Menampilkan nama/tautan file bukti (`file_bukti`) di PDF atau CSV.
- Menyamakan label lokasi kosong CSV dengan PDF ("Daring").
- Memperbarui `AGENTS.md` / README (tabel route tidak berubah; kecuali tim ingin mencatat `AbsensiModel::getDataLaporan` pada tabel Model).
