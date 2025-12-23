<?php
session_start();

if (isset($_SESSION['user']) && isset($_SESSION['user']['role'])) {
    
    $role = $_SESSION['user']['role'];
    $redirect_url = '';

    switch ($role) {
        case 'mahasiswa':
            $redirect_url = '../pages/mahasiswa/home_mahasiswa.php';
            break;
            
        case 'dosen':
            $redirect_url = '../pages/dosen/home_dosen.php';
            break;
            
        case 'petugas':
            $redirect_url = '../pages/petugas/home_petugas.php';
            break;
            
        default:
            session_unset();
            session_destroy();
            $redirect_url = 'login.php'; 
            break;
    }

    header('Location: ' . $redirect_url);
    exit;
}

header('Location: login.php');
exit;
?>