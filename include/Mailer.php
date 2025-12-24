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
            $mail->Username   = 'ghevibi125@gmail.com';
            $mail->Password   = 'wajh abiw ffve jxrw';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ======================
            // SENDER & RECEIVER
            // ======================
            $mail->setFrom('ghevibi125@gmail.com', 'GeoSafe System');
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
