<?php
require_once __DIR__ . '/../include/connection.php';
require_once __DIR__ . '/../include/Session.php';

class AuthService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        Session::start();
    }

    /**
     * Login user
     */
    public function login(string $email, string $password): array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Email tidak terdaftar.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Password salah.'];
        }

        // Tentukan role dari domain email
        $role = $this->detectRole($email);
        if (!$role) {
            return ['success' => false, 'message' => 'Domain email tidak diizinkan.'];
        }

        Session::regenerate();
        $nameFromEmail = explode('@', $user['email'])[0];
        Session::set('login', true);
        Session::set('user', [
            'id'    => $user['id'],
            'email' => $user['email'],
            'nama'  => ucfirst($nameFromEmail),
            'role'  => $role
        ]);

        return [
            'success' => true,
            'role' => $role
        ];
    }

    /**
     * Deteksi role berdasarkan domain email
     */
    private function detectRole(string $email): ?string
    {
        $domain = strtolower(substr(strrchr($email, "@"), 1));

        return match ($domain) {
            'mhs.ubpkarawang.ac.id'   => 'mahasiswa',
            'ubpkarawang.ac.id'       => 'dosen',
            'staff.ubpkarawang.ac.id' => 'petugas',
            default => null
        };
    }

    public function logout(): void
    {
        Session::destroy();
    }
}
