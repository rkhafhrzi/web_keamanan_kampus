<?php
class QRService
{
    /**
     * Mengambil gambar QR Code biner dari API eksternal.
     * @param string $kode Data yang akan di-encode.
     * @return string|false Data gambar PNG biner, atau false jika gagal.
     */
    public function buat(string $kode): string|false
    {
        if (empty($kode)) {
            return false;
        }
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?" .
                  http_build_query([
                      'size' => '300x300',
                      'data' => $kode,
                      'margin' => 1
                  ]);
            
        $response = @file_get_contents($apiUrl);
        return $response;
    }
}