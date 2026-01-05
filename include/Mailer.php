<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $mail = new PHPMailer(true);

        try {
            // ======================
            // SMTP CONFIG
            // ======================
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;

            // ⚠️ EMAIL PENGIRIM (WAJIB VALID)
            $mail->Username   = 'if24.rakhafahrezi@mhs.ubpkarawang.ac.id';
            $mail->Password   = 'ecfc ywcb gzbl zoln';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ======================
            // SENDER & RECEIVER
            // ======================
            $mail->setFrom('if24.rakhafahrezi@mhs.ubpkarawang.ac.id', 'GeoSafe System');
            $mail->addAddress($to);

            // ======================
            // CONTENT
            // ======================
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;

        } catch (Exception $e) {
            // log error kalau gagal
            file_put_contents(
                __DIR__ . '/../storage/mail_error.log',
                $mail->ErrorInfo . PHP_EOL,
                FILE_APPEND
            );
            return false;
        }
    }
}
