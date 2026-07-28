<?php
/**
 * Konten Dashboard Admin
 */

// Data dari Controller:
// $total_pegawai, $total_kegiatan, $total_kegiatan_published, $total_absensi_hari_ini
// $kegiatan_terbaru

ob_start();
?>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: #ffffff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    
    .stat-icon.pegawai { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .stat-icon.kegiatan { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .stat-icon.published { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.absensi { background: linear-gradient(135deg, #f59e0b, #d97706); }
    
    .stat-info h4 {
        font-size: 0.875rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
    
    .stat-info .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 991px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .table-activities {
        width: 100%;
        border-collapse: collapse;
    }

    .table-activities th {
        text-align: left;
        padding: 1rem;
        border-bottom: 2px solid var(--border-color);
        color: var(--text-muted);
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .table-activities td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.95rem;
    }

    .table-activities tr:last-child td {
        border-bottom: none;
    }

    .badge-status {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-status.draft { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
    .badge-status.published { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
    .badge-status.completed { background: #dbeafe; color: #1e40af; border: 1px solid #60a5fa; }
    .badge-status.cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
    
</style>

<!-- Welcome Banner -->
<div class="card" style="background: linear-gradient(135deg, var(--sidebar-bg), #0f172a); color: white; border: none;">
    <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: white;">Selamat Datang, <?= e(adminData('nama_lengkap')) ?>! 👋</h2>
            <p style="color: #94a3b8; font-size: 0.95rem;">
                Anda masuk sebagai <strong><?= e(adminData('role')) ?></strong> (<?= e(adminData('nama_jabatan') ?? '-') ?>). Pantau seluruh aktivitas dan absensi hari ini.
            </p>
        </div>
        <div>
            <div style="background: rgba(255, 255, 255, 0.1); padding: 0.75rem 1.25rem; border-radius: 0.5rem; border: 1px solid rgba(255, 255, 255, 0.2); text-align: center;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: #cbd5e1; font-weight: 600;">Hari Ini</div>
                <div style="font-size: 1.1rem; font-weight: 700; margin-top: 0.25rem;"><?= e(formatTanggal(date('Y-m-d'))) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon pegawai">
            <i class='bx bx-group'></i>
        </div>
        <div class="stat-info">
            <h4>Total Pegawai</h4>
            <div class="stat-value"><?= number_format($total_pegawai) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon kegiatan">
            <i class='bx bx-calendar'></i>
        </div>
        <div class="stat-info">
            <h4>Total Kegiatan</h4>
            <div class="stat-value"><?= number_format($total_kegiatan) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon published">
            <i class='bx bx-check-circle'></i>
        </div>
        <div class="stat-info">
            <h4>Keg. Published</h4>
            <div class="stat-value"><?= number_format($total_kegiatan_published) ?></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon absensi">
            <i class='bx bx-fingerprint'></i>
        </div>
        <div class="stat-info">
            <h4>Absensi Hari Ini</h4>
            <div class="stat-value"><?= number_format($total_absensi_hari_ini) ?></div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Latest Activities Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kegiatan Terbaru</h3>
            <a href="<?= url('admin/kegiatan') ?>" style="font-size: 0.85rem; color: var(--primary); font-weight: 500;">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div style="overflow-x: auto;">
                <table class="table-activities">
                    <thead>
                        <tr>
                            <th>Nama Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kegiatan_terbaru)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                    Belum ada data kegiatan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kegiatan_terbaru as $kegiatan): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: var(--text-main);"><?= e($kegiatan['nama_kegiatan']) ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
                                            <i class='bx bx-map'></i> <?= e($kegiatan['lokasi_kegiatan']) ?>
                                        </div>
                                    </td>
                                    <td><?= e(formatTanggal($kegiatan['tanggal_kegiatan'])) ?></td>
                                    <td>
                                        <?= e(date('H:i', strtotime($kegiatan['waktu_mulai']))) ?> - 
                                        <?= e(date('H:i', strtotime($kegiatan['waktu_selesai']))) ?>
                                    </td>
                                    <td>
                                        <span class="badge-status <?= strtolower(e($kegiatan['status_kegiatan'])) ?>">
                                            <?= e($kegiatan['status_kegiatan']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Status Kegiatan</h3>
        </div>
        <div class="card-body" style="display: flex; align-items: center; justify-content: center; min-height: 300px;">
            <canvas id="kegiatanChart"></canvas>
        </div>
    </div>
</div>

<?php
// Script untuk merender Chart.js
$totalDraft = $total_kegiatan - $total_kegiatan_published; // Sederhananya, asumsikan selisihnya adalah draft (atau status lain)

// Jika ingin lebih presisi, kita bisa melakukan query group by status, tapi ini sudah cukup merepresentasikan
$extra_js = "
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('kegiatanChart');
    
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Published', 'Draft / Lainnya'],
                datasets: [{
                    data: [{$total_kegiatan_published}, {$totalDraft}],
                    backgroundColor: [
                        '#10b981', // green for published
                        '#94a3b8'  // gray for draft
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                family: \"'Inter', sans-serif\",
                                size: 13
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
";

// Simpan output buffer ke variabel $content
$content = ob_get_clean();

// Panggil layout utama
require_once __DIR__ . '/layouts/main.php';
