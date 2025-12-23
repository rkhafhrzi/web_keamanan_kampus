<?php
require_once __DIR__ . '/../controllers/PasswordResetController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$controller = new PasswordResetController();
$controller->requestReset();
