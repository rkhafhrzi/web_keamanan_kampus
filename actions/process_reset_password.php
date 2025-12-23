<?php
require_once __DIR__ . '/../controllers/PasswordResetController.php';

$controller = new PasswordResetController();
$controller->resetPassword();
