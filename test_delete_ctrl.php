<?php
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/app/models/JabatanModel.php';
require_once __DIR__ . '/app/controllers/AdminController.php';

// Mock session and environment
session_start();
$_SESSION['user'] = ['nip' => '123', 'role' => 'admin'];
$_SERVER['REQUEST_METHOD'] = 'POST';

$csrf = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf;
$_POST['csrf_token'] = $csrf;

$controller = new AdminController();

ob_start();
$controller->jabatan_delete(9999);
$output = ob_get_clean();

echo "Flash message: \n";
print_r($_SESSION['flash'] ?? 'none');
