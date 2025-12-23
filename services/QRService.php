<?php

require_once __DIR__ . '/../include/connection.php';

class QRService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Validasi akses berdasarkan:
     * - role
     * - ruangan
     * - jam akses
     */
    public function validateAccess(int $userId, string $role, int $roomId): string
    {
        // Ambil aturan akses ruangan
        $stmt = $this->db->prepare("
            SELECT start_time, end_time 
            FROM room_access
            WHERE room_id = :room
              AND role = :role
        ");
        $stmt->execute([
            'room' => $roomId,
            'role' => $role
        ]);

        $rule = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$rule) {
            return 'ditolak';
        }

        // Cek jam akses
        $now = date('H:i:s');
        if ($now < $rule['start_time'] || $now > $rule['end_time']) {
            return 'ditolak';
        }

        return 'masuk';
    }

    /**
     * Simpan log akses
     */
    public function logAccess(int $userId, int $roomId, string $status): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO access_logs (user_id, room_id, access_time, status)
            VALUES (:user, :room, NOW(), :status)
        ");

        $stmt->execute([
            'user'   => $userId,
            'room'   => $roomId,
            'status' => $status
        ]);
    }
}