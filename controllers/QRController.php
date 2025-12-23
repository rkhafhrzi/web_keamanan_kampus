<?php

require_once dirname(__DIR__) . '/services/QRService.php';
class QRController
{
    private $service;

    public function __construct()
    {
        $this->service = new QRService();
    }

    public function tampilkanQR()
    {
        $kode = $_GET['kode'] ?? 'QR_DEFAULT';
        $download = isset($_GET['download']) && $_GET['download'] === 'true';
        $gambar_png = $this->service->buat($kode);
        
        if ($gambar_png === false || empty($gambar_png)) {
            header('Content-Type: image/png');
            header("HTTP/1.0 500 Internal Server Error");
            exit; 
        }

        if (ob_get_level() > 0) {
            ob_clean();
        }

        if ($download) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="qr-code-' . $kode . '.png"');
        } else {
            header('Content-Type: image/png');
            header('Cache-Control: no-cache, must-revalidate'); 
        }
        echo $gambar_png;
        exit;
    }
}