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
        $sql = "
            INSERT INTO lost_items
            (reporter_id, item_name, description, location, lost_date, status)
            VALUES
            (:reporter_id, :item_name, :description, :location, :lost_date, 'hilang')
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'reporter_id' => $data['reporter_id'],
            'item_name'   => $data['item_name'],
            'description' => $data['description'],
            'location'    => $data['location'],
            'lost_date'   => $data['lost_date'],
        ]);
    }
}
