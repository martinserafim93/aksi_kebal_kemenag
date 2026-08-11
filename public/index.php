<?php
/**
 * AKSI KEBAL - Entry Point
 * 
 * File ini adalah entry point utama aplikasi.
 * Semua request diarahkan ke sini oleh .htaccess.
 */

// Load konfigurasi
require_once __DIR__ . '/../config/app.php';

// Error handling berdasarkan environment
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Session cookie security
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

// Secure cookie hanya jika HTTPS
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}

// Start session
session_start();

// Load core classes
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../core/helpers.php';

// Load router dan jalankan aplikasi
require_once __DIR__ . '/../core/App.php';

$app = new App();
