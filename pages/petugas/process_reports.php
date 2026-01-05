<?php
session_start();
require_once '../../include/connection.php';

// 1. KEAMANAN: Pastikan hanya petugas yang bisa mengakses file eksekusi ini
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Content-Type: text/plain');
    die("Akses Ilegal: Anda tidak memiliki izin.");
}

$db = Database::getConnection();
$action = $_GET['action'] ?? '';
$redirect_to = "form_reports.php"; // Pastikan nama file utama Anda ini

// --- LOGIKA CREATE ---
if ($action == 'create') {
    // Gunakan ID petugas dari session, bukan angka 3 permanen
    $user_id = $_SESSION['user']['id'];
    
    $stmt = $db->prepare("INSERT INTO reports (user_id, report_type, period_start, period_end, description, generated_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $user_id, 
        $_POST['report_type'], 
        $_POST['period_start'], 
        $_POST['period_end'], 
        $_POST['description']
    ]);
    header("Location: $redirect_to?tab=detail_laporan");
    exit;
}

// --- LOGIKA DELETE ---
if ($action == 'delete') {
    $stmt = $db->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    header("Location: $redirect_to?tab=detail_laporan");
    exit;
}

// --- LOGIKA UPDATE ---
if ($action == 'update') {
    $stmt = $db->prepare("UPDATE reports SET report_type=?, period_start=?, period_end=?, description=? WHERE id=?");
    $stmt->execute([
        $_POST['report_type'], 
        $_POST['period_start'], 
        $_POST['period_end'], 
        $_POST['description'], 
        $_POST['id']
    ]);
    header("Location: $redirect_to?tab=detail_laporan");
    exit;
}