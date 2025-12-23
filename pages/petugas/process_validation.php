<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'petugas') {
      if (!isset($_SESSION['user'])) {
            header('Location: ../../public/login.php');
            exit;
      }
}

$type   = isset($_GET['type']) ? $_GET['type'] : ''; 
$id     = isset($_GET['id']) ? $_GET['id'] : '';    
$status = isset($_GET['status']) ? $_GET['status'] : ''; 

if (empty($type) || empty($id) || empty($status)) {
    $_SESSION['error'] = "Data tidak lengkap. Gagal mencatat kehadiran.";
    header('Location: validation_mahasiswa.php');
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$waktu_sekarang = date('H:i:s d-m-Y');

$pesan_sukses = "";
if ($type == 'mahasiswa') {
    $pesan_sukses = "Berhasil mencatat status <b>" . strtoupper($status) . "</b> untuk Mahasiswa dengan NIM: <b>$id</b> pada pukul $waktu_sekarang.";
} else {
    $pesan_sukses = "Berhasil mencatat status <b>" . strtoupper($status) . "</b> untuk Kendaraan: <b>$id</b>.";
}

$_SESSION['success_message'] = $pesan_sukses;

header('Location: validation_qr.php');
exit;