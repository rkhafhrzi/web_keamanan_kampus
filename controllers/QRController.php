<?php
session_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../services/QRService.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Zxing\QrReader;

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$qrText = null;

/**
 * ==============================
 * MODE 1: CAMERA SCAN (JSON)
 * ==============================
 */
$raw = file_get_contents('php://input');
if ($raw) {
    $qrText = $raw;
}

/**
 * ==============================
 * MODE 2: UPLOAD QR IMAGE
 * ==============================
 */
if (isset($_FILES['qr_image'])) {
    $reader = new QrReader($_FILES['qr_image']['tmp_name']);
    $qrText = $reader->text();
}

if (!$qrText) {
    echo json_encode(['message' => 'QR tidak terbaca']);
    exit;
}

// ==============================
// PARSE QR
// ==============================
$data = json_decode($qrText, true);

if (!$data || !isset($data['user_id'], $data['role'], $data['room_id'])) {
    echo json_encode(['message' => 'Format QR tidak valid']);
    exit;
}

$service = new QRService();

$result = $service->validateAccess(
    $data['user_id'],
    $data['role'],
    $data['room_id']
);

echo json_encode([
    'message' => $result ? 'Akses diizinkan' : 'Akses ditolak'
]);