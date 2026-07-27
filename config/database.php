<?php
/**
 * AKSI KEBAL - Database Configuration
 * 
 * Konfigurasi koneksi database MySQL menggunakan PDO.
 * Sesuaikan dengan kredensial database Anda.
 */

return [
    'host'     => 'localhost',
    'dbname'   => 'aksi_kebal',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
