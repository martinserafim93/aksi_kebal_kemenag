<?php
/**
 * AKSI KEBAL - Application Configuration
 * 
 * Konfigurasi utama aplikasi termasuk URL base, nama aplikasi,
 * dan pengaturan umum lainnya.
 */

// Base URL aplikasi (sesuaikan dengan environment)
define('BASE_URL', 'http://localhost/aksi_kebal_kemenag/public');

// Nama Aplikasi
define('APP_NAME', 'AKSI KEBAL');
define('APP_FULL_NAME', 'Absensi Kegiatan Serentak Kementerian Beramal dan Andal');

// Versi Aplikasi
define('APP_VERSION', '1.1.0');

// Environment (development / production)
define('APP_ENV', 'development');

// Timezone
date_default_timezone_set('Asia/Makassar');

// Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('MAX_COMPRESSED_SIZE', 1 * 1024 * 1024); // 1 MB target after compression

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
