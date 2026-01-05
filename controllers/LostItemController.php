<?php
session_start();

require_once __DIR__ . '/../services/LostItemService.php';

class LostItemController
{
    public function store(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: ../../public/login.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../pages/security_reports.php');
            exit;
        }

        $user = $_SESSION['user'];

        $data = [
            'reporter_id' => $user['id'],
            'item_name'   => $_POST['judul'] ?? '',
            'description' => $_POST['deskripsi'] ?? '',
            'location'    => $_POST['lokasi'] ?? '',
            'lost_date'   => $_POST['tanggal'] ?? date('Y-m-d'),
            'status'      => $_POST['status'] ?? 'hilang',
            'bukti_foto'  => $_FILES['bukti'] ?? null,
        ];

        $service = new LostItemService();
        $service->create($data);

        header('Location: ../pages/security_information.php?tab=lost_and_found&success=1');
        exit;
    }
}
