<?php
/**
 * AKSI KEBAL - Entry Point
 * 
 * File ini adalah entry point utama aplikasi.
 * Semua request diarahkan ke sini oleh .htaccess.
 */

// Start session
session_start();

// Load konfigurasi
require_once __DIR__ . '/../config/app.php';

// Load core classes
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Middleware.php';
require_once __DIR__ . '/../core/helpers.php';

// Load router dan jalankan aplikasi
require_once __DIR__ . '/../core/App.php';

$app = new App();
