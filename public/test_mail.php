<?php
require_once '../include/Mailer.php';

$ok = Mailer::send(
    'EMAIL_TUJUAN@gmail.com',
    'Test SMTP GeoSafe',
    'Jika email ini masuk, SMTP Gmail kamu BERHASIL.'
);

echo $ok ? 'EMAIL TERKIRIM' : 'EMAIL GAGAL';
