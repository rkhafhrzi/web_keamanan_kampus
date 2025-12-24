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

    public function sendVerificationCode(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        $otp = (string) random_int(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // hapus OTP lama
        $this->db->prepare(
            "DELETE FROM password_resets WHERE user_id = :uid"
        )->execute(['uid' => $user['id']]);

        // simpan OTP
        $this->db->prepare("
            INSERT INTO password_resets (user_id, token, expires_at, used)
            VALUES (:uid, :token, :exp, 0)
        ")->execute([
            'uid'   => $user['id'],
            'token' => $otp,
            'exp'   => $expires
        ]);

        $sent = Mailer::send(
            $email,
            'Kode Verifikasi Reset Password',
            "Kode OTP Anda: <b>{$otp}</b> (berlaku 10 menit)"
        );

        $this->db->prepare("
            INSERT INTO notifications (user_id, type, message, status)
            VALUES (:uid, 'reset_password', 'Kirim OTP reset password', :status)
        ")->execute([
            'uid'    => $user['id'],
            'status' => $sent ? 'sent' : 'failed'
        ]);

        return $sent;
    }

    public function resetPassword(string $email, string $newPassword): bool
    {
        // ambil user
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            return false;
        }

        // hash password baru
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        // update password
        $this->db->prepare("
            UPDATE users
            SET password = :pw
            WHERE id = :id
        ")->execute([
            'pw' => $hash,
            'id' => $user['id']
        ]);

        // (opsional tapi rapi) catat notifikasi
        $this->db->prepare("
            INSERT INTO notifications (user_id, type, message, status)
            VALUES (:uid, 'reset_password', 'Password berhasil direset', 'sent')
        ")->execute([
            'uid' => $user['id']
        ]);

        return true;
    }

}