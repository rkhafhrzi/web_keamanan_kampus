<?php
require_once __DIR__ . '/../services/AuthService.php';

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function handleLogin(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->redirectWithError('Email dan password wajib diisi.');
        }

        $result = $this->authService->login($email, $password);

        if (!$result['success']) {
            $this->redirectWithError($result['message']);
        }

        // Redirect sesuai role
        $redirect = match ($result['role']) {
            'mahasiswa' => '../pages/mahasiswa/home_mahasiswa.php',
            'dosen'     => '../pages/dosen/home_dosen.php',
            'petugas'   => '../pages/petugas/home_petugas.php',
        };

        header("Location: {$redirect}");
        exit;
    }

    private function redirectWithError(string $message): void
    {
        Session::set('login_error', $message);
        header('Location: ../public/login.php');
        exit;
    }
}
