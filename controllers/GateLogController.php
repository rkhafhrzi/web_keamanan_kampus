<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../include/connection.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$userId    = $data['user_id'] ?? null;
$roomId    = $data['room_id'] ?? null;
$direction = $data['direction'] ?? null;

if (!$userId || !$roomId || !in_array($direction, ['masuk', 'keluar'])) {
    echo json_encode(['message' => 'Data tidak valid']);
    exit;
}

$db = Database::getConnection();

$db->prepare("
    INSERT INTO gate_logs (user_id, gate_id, direction, logged_at)
    VALUES (:user, :gate, :dir, NOW())
")->execute([
    'user' => $userId,
    'gate' => $roomId,
    'dir'  => $direction
]);

// ambil info user
$stmt = $db->prepare("SELECT email FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch();

echo json_encode([
    'message' => 'Akses berhasil dicatat',
    'user' => $user
]);
