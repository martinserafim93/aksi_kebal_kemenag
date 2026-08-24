<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - <?= e($kegiatan['nama_kegiatan']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .header-logo {
            width: 70px;
            height: auto;
            flex-shrink: 0;
        }
        .header-text {
            text-align: center;
        }
        .header-text h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-text h2 {
            margin: 4px 0 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-address {
            margin: 4px 0 0;
            font-size: 11px;
            font-style: italic;
            color: #333;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center !important;
        }
        .summary {
            float: right;
            border: 1px solid #000;
            padding: 10px;
            font-size: 12px;
            width: 250px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
            .no-print {
                display: none !important;
            }
        }
        .print-btn {
            display: block;
            margin: 0 auto 20px;
            padding: 10px 20px;
            background: #10b981;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .print-btn:hover {
            background: #059669;
        }
    </style>
</head>
<body onload="window.print()">

    <button class="print-btn no-print" onclick="window.print()">Cetak Laporan / Simpan PDF</button>

    <div class="header">
        <div class="header-content">
            <img src="<?= url('assets/img/kemenag-new-2025.png') ?>" alt="Logo Kemenag" class="header-logo">
            <div class="header-text">
                <h1>LAPORAN KEHADIRAN PEGAWAI PADA AKSI KEBAL</h1>
                <h2>KANTOR WILAYAH KEMENTERIAN AGAMA PROVINSI KALIMANTAN UTARA</h2>
                <p class="header-address">Jalan Ahmad Yani Poros Bulungan – Berau KM. 2, Tanjung Selor 77212</p>
            </div>
        </div>
    </div>

    <table class="info-table">
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
        <tr>
            <td>Nama Kegiatan</td>
            <td>: <?= e($kegiatan['nama_kegiatan']) ?></td>
        </tr>
        <tr>
            <td>Jenis Kegiatan</td>
            <td>: <?= e($kegiatan['jenis_kegiatan']) ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: <?= date('d F Y', strtotime($kegiatan['tanggal_kegiatan'])) ?></td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>: <?= date('H:i', strtotime($kegiatan['waktu_mulai'])) ?> - <?= date('H:i', strtotime($kegiatan['waktu_selesai'])) ?></td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>: <?= e($kegiatan['lokasi_kegiatan']) ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">NIP</th>
                <th width="35%">Nama Pegawai</th>
                <th width="20%">Waktu Submit</th>
                <th width="20%">Status Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($absensi)): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data kehadiran</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($absensi as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= e($row['nip']) ?></td>
                        <td><?= e($row['nama_lengkap']) ?></td>
                        <td class="text-center"><?= !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-' ?></td>
                        <td class="text-center"><?= e($row['status_kehadiran']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary">
        <div style="font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px;">Rekapitulasi Kehadiran</div>
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

</body>
</html>
