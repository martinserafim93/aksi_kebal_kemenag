<?php ob_start(); 
$qrCodeUrl = !empty($kegiatan['qr_code']) ? $kegiatan['qr_code'] : url("absensi?kegiatan=" . $kegiatan['kode_kegiatan']);
?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0; font-size: 1.5rem; color: var(--text-main);">QR Code Absensi</h2>
            <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.95rem;">Kegiatan: <?= e($kegiatan['nama_kegiatan']) ?></p>
        </div>
        <div>
            <a href="<?= url('admin/kegiatan') ?>" class="btn" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main);">
                <i class='bx bx-arrow-back'></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto; text-align: center;">
    <div class="card-body" style="padding: 3rem 1.5rem;">
        <h3 style="margin-bottom: 1.5rem;">Scan untuk Absensi</h3>
        <p style="color: var(--text-muted); margin-bottom: 2rem;">
            Silakan scan QR code di bawah ini atau bagikan link absensi kepada pegawai.
        </p>

        <!-- QR Code Container -->
        <div id="qrcode-container" style="display: inline-block; padding: 1.5rem; background: #ffffff; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-bottom: 2rem;"></div>
        
        <div style="margin-bottom: 2rem;">
            <p style="font-weight: 500; margin-bottom: 0.5rem;">Atau gunakan link berikut:</p>
            <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
                <input type="text" id="absensi-url" class="form-control" value="<?= e($qrCodeUrl) ?>" readonly style="max-width: 350px; background: #f8fafc;">
                <button type="button" class="btn btn-primary" onclick="copyUrl()" title="Salin URL">
                    <i class='bx bx-copy'></i>
                </button>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 1rem;">
            <button type="button" class="btn btn-gradient-success" onclick="downloadQRCode()">
                <i class='bx bx-download'></i> Download QR Code
            </button>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<!-- Load library QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var qrCodeUrl = "<?= addslashes($qrCodeUrl) ?>";
        var qrContainer = document.getElementById("qrcode-container");
        
        // Generate QR Code
        var qrcode = new QRCode(qrContainer, {
            text: qrCodeUrl,
            width: 256,
            height: 256,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });
    });

    function copyUrl() {
        var urlInput = document.getElementById("absensi-url");
        urlInput.select();
        urlInput.setSelectionRange(0, 99999); // Untuk perangkat mobile
        document.execCommand("copy");
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'URL Absensi berhasil disalin ke clipboard.',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function downloadQRCode() {
        var canvas = document.querySelector('#qrcode-container canvas');
        if (canvas) {
            var url = canvas.toDataURL("image/png");
            var a = document.createElement('a');
            a.href = url;
            a.download = 'QRCode_Absensi_<?= preg_replace('/[^a-zA-Z0-9_-]/', '_', $kegiatan['nama_kegiatan']) ?>.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'QR Code belum siap untuk diunduh.',
            });
        }
    }
</script>
<?php 
$extra_js = ob_get_clean(); 
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
