<?php

require_once __DIR__ . '/../include/connection.php';

class LostItemService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): void
    {
        $evidence_image = null;

        // 1. Logika Upload Foto
        if (isset($data['bukti_foto']) && $data['bukti_foto']['error'] === 0) {
            $upload_dir = __DIR__ . '/../uploads/lost_items/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $extension = pathinfo($data['bukti_foto']['name'], PATHINFO_EXTENSION);
            $filename = 'REPORT_' . time() . '_' . uniqid() . '.' . $extension;

            if (move_uploaded_file($data['bukti_foto']['tmp_name'], $upload_dir . $filename)) {
                $evidence_image = $filename;
            }
        }

        // 2. Query SQL - Pastikan semua kolom tertulis di sini
        $sql = "
        INSERT INTO lost_items
        (reporter_id, item_name, description, location, lost_date, status, evidence_image, created_at)
        VALUES
        (:reporter_id, :item_name, :description, :location, :lost_date, :status, :evidence_image, NOW())
    ";

        $stmt = $this->db->prepare($sql);

        // 3. Execute - Kuncinya harus SAMA PERSIS dengan di atas
        $stmt->execute([
            'reporter_id' => $data['reporter_id'],
            'item_name' => $data['item_name'],
            'description' => $data['description'],
            'location' => $data['location'],
            'lost_date' => $data['lost_date'],
            'status' => $data['status'] ?? 'hilang', // Pastikan parameter ini ada
            'evidence_image' => $evidence_image             // Pastikan parameter ini ada
        ]);
    }
}