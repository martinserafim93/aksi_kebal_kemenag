<?php
/**
 * AKSI KEBAL - Router (App Class)
 * 
 * Menangani routing URL ke Controller dan Method yang sesuai.
 * Format URL: /controller/method/params
 * 
 * Contoh:
 *   /admin/dashboard     -> AdminController::dashboard()
 *   /admin/pegawai/edit/1 -> AdminController::pegawai_edit(1)
 *   /absensi?kegiatan=1  -> AbsensiController::index()
 */

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Tentukan controller berdasarkan URL
        if (isset($url[0]) && !empty($url[0])) {
            $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                // Controller tidak ditemukan, gunakan error 404
                $this->controller = 'HomeController';
                $this->method = 'notFound';
            }
        }

        // Load controller
        require_once __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Tentukan method
        if (isset($url[1]) && !empty($url[1])) {
            $methodName = str_replace('-', '_', $url[1]);
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } else {
                $this->method = 'notFound';
            }
        }

        // Sisa URL menjadi parameter
        $this->params = $url ? array_values($url) : [];

        // Panggil controller->method(params)
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse URL menjadi array
     * Contoh: "admin/dashboard" -> ["admin", "dashboard"]
     */
    protected function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
