<?php
require_once '../services/PasswordResetService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$email = trim($_POST['email'] ?? '');
$otp   = trim($_POST['code'] ?? '');

if (!$email || !$otp) {
    http_response_code(400);
    exit('Data tidak lengkap');
}

$service = new PasswordResetService();

if (!$service->verifyOtp($email, $otp)) {
    http_response_code(401);
    exit('Kode salah atau kadaluarsa');
}

echo 'VERIFIED';
