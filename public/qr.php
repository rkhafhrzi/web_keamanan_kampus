<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Content-Type: image/png');
    exit; 
}

require_once __DIR__ . '/../controllers/QRController.php';

$controller = new QRController();
$controller->tampilkanQR();