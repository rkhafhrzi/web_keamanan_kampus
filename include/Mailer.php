<?php

class Mailer
{
    /**
     * Simulated mail sender (DEV / INTERNAL MODE)
     */
    public static function send(string $to, string $subject, string $body): bool
    {
        // Log ke file (opsional, untuk bukti)
        $log = sprintf(
            "[%s] TO: %s | SUBJECT: %s\n%s\n\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            strip_tags($body)
        );

        file_put_contents(
            __DIR__ . '/../storage/mail.log',
            $log,
            FILE_APPEND
        );

        // Anggap email berhasil terkirim
        return true;
    }
}