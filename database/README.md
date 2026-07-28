# 📦 Database — AKSI KEBAL

Folder ini berisi file SQL untuk migrasi dan seed database.

## File

| File | Deskripsi |
|------|-----------|
| `aksi_kebal.sql` | Script migrasi lengkap: pembuatan database, semua tabel, index, dan data seed awal |

## Cara Menjalankan Migrasi

### Menggunakan MySQL CLI

```bash
mysql -u root -p < database/aksi_kebal.sql
```

### Menggunakan phpMyAdmin

1. Buka phpMyAdmin (`http://localhost/phpmyadmin`)
2. Klik tab **Import**
3. Pilih file `aksi_kebal.sql`
4. Klik **Go / Eksekusi**

### Menggunakan XAMPP / Laragon

1. Pastikan MySQL service berjalan
2. Gunakan salah satu cara di atas

## Skema Database

```
tim_kerja (id_tim_kerja PK, nama_tim_kerja)
     │
     └──── pegawai (nip PK, id_tim_kerja FK, id_jabatan FK, ...)
              │
jabatan (id_jabatan PK, nama_jabatan)
     │       │
     └───────┘
              │
              └──── absensi (id_absensi PK, nip FK, id_kegiatan FK, ...)
                       │
kegiatan (id_kegiatan PK, nama_kegiatan, status_kegiatan, ...)
         │
         └───────────┘
```

## Data Seed Awal

- **7** Tim Kerja
- **13** Jabatan
- **10** Pegawai (1 Admin + 9 Pegawai)
- **5** Kegiatan (2 Published, 3 Draft)

### Akun Admin Default

| Field | Value |
|-------|-------|
| NIP | `199001012020011001` |
| Email | `admin@kemenag.go.id` |
| Password | `admin123` |

> ⚠️ **Penting:** Ganti password admin default di production!
