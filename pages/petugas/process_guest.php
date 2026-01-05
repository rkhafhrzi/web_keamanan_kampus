<?php
session_start();
require_once '../../include/connection.php';

// Pastikan hanya petugas yang bisa akses
if (!isset($_SESSION['user'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$pdo = Database::getConnection();

// Mengambil action dari POST atau GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'register') {
        // --- LOGIKA SIMPAN TAMU BARU ---
        $nama = $_POST['nama'];
        $instansi = $_POST['instansi'];
        $tujuan = $_POST['tujuan'];
        $petugas_id = $_SESSION['user']['id'];

        $sql = "INSERT INTO guests (name, institution, purpose, visit_date, status, created_by, created_at) 
                VALUES (?, ?, ?, CURDATE(), 'approved', ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $instansi, $tujuan, $petugas_id]);
        
        header("Location: guest_dashboard.php?msg=register_success");
        exit;

    } elseif ($action === 'checkin') {
        // --- LOGIKA CATAT MASUK ---
        $id_tamu = $_POST['id_tamu'];
        $sql = "UPDATE guests SET check_in = NOW(), status = 'checked_in' WHERE id = ?";
        $pdo->prepare($sql)->execute([$id_tamu]);
        
        header("Location: guest_dashboard.php?msg=checkin_success");
        exit;

    } elseif ($action === 'checkout') {
        // --- LOGIKA CATAT KELUAR ---
        $id_tamu = $_POST['id_tamu'];
        $sql = "UPDATE guests SET check_out = NOW(), status = 'checked_out' WHERE id = ?";
        $pdo->prepare($sql)->execute([$id_tamu]);
        
        header("Location: guest_dashboard.php?msg=checkout_success");
        exit;

    } elseif ($action === 'update') {
        // --- LOGIKA EDIT DATA ---
        $id = $_POST['id_tamu'];
        $nama = $_POST['nama'];
        $instansi = $_POST['instansi'];
        $tujuan = $_POST['tujuan'];

        $sql = "UPDATE guests SET name = ?, institution = ?, purpose = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$nama, $instansi, $tujuan, $id]);

        header("Location: guest_dashboard.php?msg=update_success");
        exit;

    } elseif ($action === 'delete') {
        // --- LOGIKA HAPUS DATA ---
        $id = $_GET['id'];
        $sql = "DELETE FROM guests WHERE id = ?";
        $pdo->prepare($sql)->execute([$id]);

        header("Location: guest_dashboard.php?msg=delete_success");
        exit;
    }

    // Jika tidak ada action yang cocok, kembali ke dashboard
    header("Location: guest_dashboard.php");

} catch (PDOException $e) {
    // Memberikan pesan error yang lebih jelas jika gagal
    die("Gagal memproses data ke database: " . $e->getMessage());
}