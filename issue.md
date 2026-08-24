# Issue: Pelaporan PDF Absensi Pegawai — Filter Hadir, Tidak Hadir, dan Semua

## Deskripsi

Saat ini, fitur **Export PDF** pada halaman detail absensi kegiatan hanya menampilkan **semua pegawai yang sudah melakukan absensi** tanpa opsi filter. Fitur ini perlu ditingkatkan agar admin dapat mencetak laporan berdasarkan status kehadiran, serta menyertakan pegawai yang **sama sekali belum mengisi absensi** (dianggap Tidak Hadir).

---

## Kebutuhan Fungsional

### 1. Filter Laporan PDF Berdasarkan Status Kehadiran
Admin harus bisa memilih jenis laporan PDF yang ingin dicetak:
- **Hadir Saja** → hanya menampilkan pegawai dengan `status_kehadiran = 'Hadir'`
- **Tidak Hadir Saja** → menampilkan pegawai dengan `status_kehadiran = 'Tidak Hadir'` **DAN** pegawai yang sama sekali tidak melakukan absensi
- **Semua (Hadir + Tidak Hadir)** → menampilkan seluruh data, termasuk pegawai yang tidak melakukan absensi

### 2. Pegawai yang Tidak Melakukan Absensi
- Pegawai yang **tidak terdaftar di tabel `absensi`** untuk kegiatan tersebut harus **tetap ditampilkan** di laporan dengan status **"Tidak Hadir"**.
- Ambil data pegawai dari tabel `pegawai` lalu lakukan `LEFT JOIN` atau logika serupa untuk mendeteksi siapa yang belum melakukan absensi.
- Kolom **Waktu Submit** untuk pegawai ini diisi dengan tanda strip (`-`).

### 3. Tabel Rekapitulasi
- Tabel rekap tetap sama seperti fitur sebelumnya:
  - **Hadir**: jumlah pegawai yang hadir
  - **Tidak Hadir**: jumlah pegawai yang tidak hadir + yang tidak mengisi absensi
  - **Total Pegawai**: total seluruh pegawai (yang absen + yang tidak absen)
- Rekapitulasi disesuaikan berdasarkan filter yang dipilih.

---

## Tahapan Implementasi

### Tahap 1: Tambah Dropdown / Tombol Filter di Halaman Detail Absensi

**File:** `app/views/admin/absensi/detail.php`

**Apa yang dilakukan:**
Ubah tombol "Export PDF" yang saat ini berupa satu tombol, menjadi **dropdown** (atau tiga tombol terpisah) dengan opsi:
- Export PDF — Semua
- Export PDF — Hadir Saja
- Export PDF — Tidak Hadir Saja

**Contoh perubahan:**
```html
<!-- SEBELUM: satu tombol saja -->
<a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan']) ?>">Export PDF</a>

<!-- SESUDAH: dropdown dengan 3 opsi -->
<div class="dropdown">
    <button class="btn btn-danger dropdown-toggle">
        <i class='bx bxs-file-pdf'></i> Export PDF
    </button>
    <div class="dropdown-menu">
        <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=semua') ?>">
            Semua (Hadir + Tidak Hadir)
        </a>
        <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=hadir') ?>">
            Hadir Saja
        </a>
        <a href="<?= url('admin/absensi-export-pdf/' . $kegiatan['kode_kegiatan'] . '?filter=tidak_hadir') ?>">
            Tidak Hadir Saja
        </a>
    </div>
</div>
```

**Catatan untuk developer:**
- Gunakan CSS sederhana untuk dropdown (tidak perlu library tambahan).
- Kirim parameter `?filter=` via query string agar controller bisa menangkapnya.

---

### Tahap 2: Buat Method Baru di Model untuk Mengambil Pegawai yang Tidak Absen

**File:** `app/models/AbsensiModel.php`

**Apa yang dilakukan:**
Buat method baru bernama `getPegawaiTidakAbsen($id_kegiatan)` yang mengembalikan daftar pegawai yang **tidak ada record absensi-nya** untuk kegiatan tertentu.

**Contoh query SQL:**
```sql
SELECT p.nip, p.nama_lengkap
FROM pegawai p
WHERE p.nip NOT IN (
    SELECT a.nip FROM absensi a WHERE a.id_kegiatan = :id_kegiatan
)
ORDER BY p.nama_lengkap ASC
```

**Contoh method:**
```php
public function getPegawaiTidakAbsen(int $id_kegiatan): array
{
    $this->db->query(
        "SELECT p.nip, p.nama_lengkap
         FROM pegawai p
         WHERE p.nip NOT IN (
             SELECT a.nip FROM absensi a WHERE a.id_kegiatan = :id_kegiatan
         )
         ORDER BY p.nama_lengkap ASC"
    );
    $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
    return $this->db->fetchAll();
}
```

---

### Tahap 3: Update Method `getStatistik()` di Model agar Menghitung Pegawai yang Tidak Absen

**File:** `app/models/AbsensiModel.php`

**Apa yang dilakukan:**
Buat method baru (atau modifikasi method yang ada) agar statistik mencakup total pegawai yang terdaftar di sistem.

**Contoh method baru:**
```php
public function getStatistikLengkap(int $id_kegiatan): array
{
    // Hitung pegawai yang sudah absen
    $this->db->query(
        "SELECT 
            SUM(CASE WHEN status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN status_kehadiran = 'Tidak Hadir' THEN 1 ELSE 0 END) as tidak_hadir_absen
         FROM absensi WHERE id_kegiatan = :id_kegiatan"
    );
    $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
    $result = $this->db->fetch();

    // Hitung total pegawai di sistem
    $this->db->query("SELECT COUNT(*) as total FROM pegawai");
    $totalPegawai = (int) $this->db->fetch()['total'];

    // Hitung pegawai yang tidak melakukan absensi sama sekali
    $this->db->query(
        "SELECT COUNT(*) as jumlah FROM pegawai
         WHERE nip NOT IN (SELECT nip FROM absensi WHERE id_kegiatan = :id_kegiatan)"
    );
    $this->db->bind(':id_kegiatan', $id_kegiatan, PDO::PARAM_INT);
    $tidakAbsen = (int) $this->db->fetch()['jumlah'];

    $hadir = $result ? (int) $result['hadir'] : 0;
    $tidakHadirAbsen = $result ? (int) $result['tidak_hadir_absen'] : 0;

    return [
        'total_pegawai'    => $totalPegawai,
        'hadir'            => $hadir,
        'tidak_hadir'      => $tidakHadirAbsen + $tidakAbsen, // yang isi tidak hadir + yang tidak isi sama sekali
        'tidak_absen'      => $tidakAbsen, // yang tidak mengisi absen sama sekali
    ];
}
```

---

### Tahap 4: Update Controller untuk Menangani Parameter Filter

**File:** `app/controllers/AdminController.php`

**Apa yang dilakukan:**
Modifikasi method `absensi_export_pdf()` agar:
1. Membaca parameter `?filter=` dari query string (`hadir`, `tidak_hadir`, atau `semua`).
2. Berdasarkan filter, siapkan data yang berbeda untuk dikirim ke view.

**Contoh perubahan:**
```php
public function absensi_export_pdf($identifier = null): void
{
    // ... (kode validasi kegiatan tetap sama) ...

    $model = $this->model('AbsensiModel');
    $filter = query('filter', 'semua'); // default: semua

    // Ambil data absensi yang sudah diisi
    $semuaAbsensi = $model->getAllFilteredForExport(['kegiatan' => $kegiatan['id_kegiatan']]);

    // Ambil pegawai yang tidak melakukan absensi sama sekali
    $pegawaiTidakAbsen = $model->getPegawaiTidakAbsen($kegiatan['id_kegiatan']);

    // Siapkan data berdasarkan filter
    switch ($filter) {
        case 'hadir':
            $absensi = array_filter($semuaAbsensi, fn($r) => $r['status_kehadiran'] === 'Hadir');
            break;
        case 'tidak_hadir':
            // Pegawai yang isi "Tidak Hadir" + yang tidak isi absen sama sekali
            $tidakHadir = array_filter($semuaAbsensi, fn($r) => $r['status_kehadiran'] === 'Tidak Hadir');
            // Gabungkan dengan pegawai yang tidak absen (format-kan datanya agar seragam)
            foreach ($pegawaiTidakAbsen as $p) {
                $tidakHadir[] = [
                    'nip'               => $p['nip'],
                    'nama_lengkap'      => $p['nama_lengkap'],
                    'status_kehadiran'  => 'Tidak Hadir',
                    'created_at'        => null, // tidak ada waktu submit
                ];
            }
            $absensi = array_values($tidakHadir);
            break;
        default: // semua
            // Gabungkan absensi yang ada + pegawai yang tidak mengisi absen
            $absensi = $semuaAbsensi;
            foreach ($pegawaiTidakAbsen as $p) {
                $absensi[] = [
                    'nip'               => $p['nip'],
                    'nama_lengkap'      => $p['nama_lengkap'],
                    'status_kehadiran'  => 'Tidak Hadir',
                    'created_at'        => null,
                ];
            }
            break;
    }

    // Hitung statistik lengkap
    $statistik = $model->getStatistikLengkap($kegiatan['id_kegiatan']);

    $this->view('admin/absensi/pdf_export', [
        'title'     => 'Laporan Absensi - ' . $kegiatan['nama_kegiatan'],
        'kegiatan'  => $kegiatan,
        'absensi'   => $absensi,
        'statistik' => $statistik,
        'filter'    => $filter, // kirim info filter ke view
    ]);
}
```

---

### Tahap 5: Update View PDF untuk Menampilkan Info Filter dan Rekapitulasi

**File:** `app/views/admin/absensi/pdf_export.php`

**Apa yang dilakukan:**
1. Tampilkan label filter yang dipilih di bawah informasi kegiatan.
2. Sesuaikan kolom "Waktu Submit" agar menampilkan tanda strip (`-`) jika `created_at` bernilai `null`.
3. Update tabel rekapitulasi agar menampilkan data yang benar berdasarkan statistik lengkap.

**Perubahan 1 — Tambah informasi filter:**
```html
<tr>
    <td>Filter Laporan</td>
    <td>: <?php
        switch ($filter ?? 'semua') {
            case 'hadir': echo 'Pegawai Hadir Saja'; break;
            case 'tidak_hadir': echo 'Pegawai Tidak Hadir Saja'; break;
            default: echo 'Semua Pegawai (Hadir + Tidak Hadir)';
        }
    ?></td>
</tr>
```

**Perubahan 2 — Tangani `created_at` null:**
```php
<!-- SEBELUM -->
<td class="text-center"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>

<!-- SESUDAH -->
<td class="text-center">
    <?= !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?>
</td>
```

**Perubahan 3 — Update rekapitulasi:**
```html
<div class="summary">
    <div style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px;">
        Rekapitulasi Kehadiran
    </div>
    <div class="summary-row">
        <span>Hadir</span>
        <span><?= $statistik['hadir'] ?> Orang</span>
    </div>
    <div class="summary-row">
        <span>Tidak Hadir</span>
        <span><?= $statistik['tidak_hadir'] ?> Orang</span>
    </div>
    <div class="summary-row">
        <span>Tidak Mengisi Absen</span>
        <span><?= $statistik['tidak_absen'] ?> Orang</span>
    </div>
    <div class="summary-row total">
        <span>Total Pegawai</span>
        <span><?= $statistik['total_pegawai'] ?> Orang</span>
    </div>
</div>
```

---

## Ringkasan File yang Perlu Diubah

| No | File | Perubahan |
|----|------|-----------|
| 1 | `app/views/admin/absensi/detail.php` | Ubah tombol Export PDF menjadi dropdown dengan 3 opsi filter |
| 2 | `app/models/AbsensiModel.php` | Tambah method `getPegawaiTidakAbsen()` dan `getStatistikLengkap()` |
| 3 | `app/controllers/AdminController.php` | Update method `absensi_export_pdf()` untuk menangani query `?filter=` |
| 4 | `app/views/admin/absensi/pdf_export.php` | Update view PDF untuk info filter, handle null `created_at`, dan rekapitulasi lengkap |

---

## Catatan Penting

- **Tidak perlu** mengubah struktur database. Cukup menggunakan query SQL untuk mendeteksi pegawai yang belum absen.
- Gunakan **query string** (`?filter=hadir`) bukan POST, agar link bisa dibuka langsung di tab baru.
- Pastikan tabel rekapitulasi di PDF menyesuaikan dengan filter yang dipilih.
- Lakukan **pengujian** dengan skenario: ada pegawai yang hadir, ada yang tidak hadir, dan ada yang sama sekali tidak mengisi absensi.
