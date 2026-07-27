<?php
/**
 * AKSI KEBAL - Base Controller
 * 
 * Kelas dasar untuk semua controller.
 * Menyediakan method untuk memuat view dan model.
 */

class Controller
{
    /**
     * Load model
     * 
     * @param string $model Nama model (contoh: 'Pegawai')
     * @return object Instance model
     */
    protected function model(string $model): object
    {
        $modelFile = __DIR__ . '/../app/models/' . $model . '.php';

        if (!file_exists($modelFile)) {
            throw new \Exception("Model '{$model}' not found at {$modelFile}");
        }

        require_once $modelFile;
        return new $model;
    }

    /**
     * Load view dengan data
     * 
     * @param string $view Path view (contoh: 'admin/dashboard' atau 'pegawai/absensi')
     * @param array $data Data yang dikirim ke view (extract sebagai variabel)
     */
    protected function view(string $view, array $data = []): void
    {
        $viewFile = __DIR__ . '/../app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View '{$view}' not found at {$viewFile}");
        }

        // Extract data agar bisa diakses sebagai variabel di view
        extract($data);

        require_once $viewFile;
    }

    /**
     * Redirect ke URL tertentu
     * 
     * @param string $url URL tujuan (relatif dari BASE_URL)
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * Kirim response JSON
     * 
     * @param mixed $data Data yang akan di-encode ke JSON
     * @param int $statusCode HTTP status code
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Halaman 404 Not Found
     */
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404');
    }
}
