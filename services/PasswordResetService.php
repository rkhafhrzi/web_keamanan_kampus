<?php
require_once __DIR__ . '/../include/connection.php';
require_once __DIR__ . '/../include/Mailer.php';

class PasswordResetService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function sendResetLink(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) return false;

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $this->db->prepare("
            INSERT INTO password_resets (user_id, token, expires_at)
            VALUES (:uid, :token, :exp)
        ")->execute([
            'uid' => $user['id'],
            'token' => $token,
            'exp' => $expires
        ]);

        $link = "http://localhost/keamanan_kampus/public/reset_password.php?token={$token}";

        $sent = Mailer::send(
            $email,
            'Reset Password',
            "Klik link berikut untuk reset password:<br><a href='{$link}'>Reset Password</a>"
        );

        $this->logNotification($user['id'], 'reset_password', $sent);

        return $sent;
    }

    public function resetPasswordByEmail(string $email, string $newPassword): bool
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) return false;

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->db->prepare("
            UPDATE users SET password = :pw WHERE id = :id
        ")->execute([
            'pw' => $hash,
            'id' => $user['id']
        ]);

        return true;
    }

    private function logNotification(int $userId, string $type, bool $sent): void
    {
        $this->db->prepare("
            INSERT INTO notifications (user_id, type, message, status)
            VALUES (:uid, :type, :msg, :status)
        ")->execute([
            'uid' => $userId,
            'type' => $type,
            'msg' => 'Email reset password',
            'status' => $sent ? 'sent' : 'failed'
        ]);
    }
}
