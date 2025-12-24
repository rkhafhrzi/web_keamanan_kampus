<?php
require_once '../services/PasswordResetService.php';

$email    = $_POST['email_final'] ?? '';
$password = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (!$email || !$password || !$confirm) {
    die('Data tidak lengkap');
}

if ($password !== $confirm) {
    die('Password tidak sama');
}

if (strlen($password) < 8) {
    die('Password minimal 8 karakter');
}

$service = new PasswordResetService();

if (!$service->resetPassword($email, $password)) {
    die('Gagal reset password');
}

header('Location: ../public/login.php?reset=success');
exit;
