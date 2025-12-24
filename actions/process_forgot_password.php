<?php
require_once '../services/PasswordResetService.php';

$email = trim($_POST['email'] ?? '');

if (!$email) {
    http_response_code(400);
    echo 'EMAIL_KOSONG';
    exit;
}

$service = new PasswordResetService();

if (!$service->sendVerificationCode($email)) {
    http_response_code(404);
    echo 'EMAIL_TIDAK_DITEMUKAN';
    exit;
}

echo 'OK';
