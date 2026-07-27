<?php
/**
 * AKSI KEBAL - Home Controller
 * 
 * Controller default untuk halaman utama.
 * Menangani redirect dan halaman landing.
 */

class HomeController extends Controller
{
    /**
     * Halaman utama - redirect ke login admin atau halaman info
     */
    public function index(): void
    {
        $this->view('home/index', [
            'title' => APP_NAME . ' - ' . APP_FULL_NAME
        ]);
    }

    /**
     * Halaman 404 Not Found
     */
    public function notFound(): void
    {
        http_response_code(404);
        $this->view('errors/404', [
            'title' => '404 - Halaman Tidak Ditemukan'
        ]);
    }
}
