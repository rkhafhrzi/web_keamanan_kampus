<?php
require_once __DIR__ . '/../services/PasswordResetService.php';
require_once __DIR__ . '/../include/Session.php';

class PasswordResetController
{
    private PasswordResetService $service;

    public function __construct()
    {
        Session::start();
        $this->service = new PasswordResetService();
    }

    public function requestReset(): void
    {
        $email = trim($_POST['email'] ?? '');

        if (!$email || !$this->service->sendResetLink($email)) {
            Session::set('login_error', 'Gagal mengirim email reset.');
        } else {
            Session::set('login_error', 'Link reset password telah dikirim.');
        }

        header('Location: ../public/lupa_password.php');
        exit;
    }

    public function resetPassword(): void
    {
        $email = $_POST['email_final'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirm) {
            Session::set('login_error', 'Password tidak sama.');
            header('Location: ../public/lupa_password.php');
            exit;
        }

        if ($this->service->resetPasswordByEmail($email, $newPassword)) {
            Session::set('login_error', 'Password berhasil diperbarui.');
            header('Location: ../public/login.php');
            exit;
        }

        Session::set('login_error', 'Gagal reset password.');
        header('Location: ../public/lupa_password.php');
        exit;
    }

}
