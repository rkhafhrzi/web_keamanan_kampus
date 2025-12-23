<?php
session_start();
require_once __DIR__ . '/../services/QRService.php';

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$roomId = $data['room_id'] ?? null;

if (!$roomId) {
    echo json_encode(['message' => 'QR tidak valid']);
    exit;
}

$user = $_SESSION['user'];

$service = new QRService();
$status = $service->validateAccess(
    $user['id'],
    $user['role'],
    $roomId
);

$service->logAccess($user['id'], $roomId, $status);

echo json_encode([
    'message' => $status === 'masuk'
        ? 'Akses diizinkan'
        : 'Akses ditolak'
]);