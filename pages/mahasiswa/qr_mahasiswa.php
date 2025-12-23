<?php
session_start();

if (
    !isset($_SESSION['user']) ||
    !is_array($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'mahasiswa'
) {
    header('Location: ../../public/login.php');
    exit;
}

$user = $_SESSION['user'];

$userId = $user['id'];
$nama   = $user['nama'] ?? $user['email'] ?? 'Mahasiswa';
$role   = $user['role'];

// sementara: mahasiswa akses Lab Komputer (room_id = 1)
$roomId = 1;

// payload QR (JSON)
$qrPayload = json_encode([
    'user_id'   => $userId,
    'role'      => $role,
    'room_id'   => $roomId,
    'timestamp' => time()
]);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>QR Mahasiswa</title>
    <?php include '../../include/header.php'; ?>

    <!-- QR Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>

<body class="bg-gray-50">

<!-- ===== SIDEBAR & NAV (TETAP) ===== -->
<div id="overlay"
     class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>

<aside id="sidebar"
       class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
    <div class="p-5 border-b bg-blue-900 text-white">
        <h2 class="text-xl font-bold">GeoSafe</h2>
    </div>
    <ul class="p-5 space-y-5 text-blue-900 font-medium">
        <li><a href="home_mahasiswa.php"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
        <li><a href="apps_mahasiswa.php" class="font-bold text-blue-600">
            <i class="fa-solid fa-qrcode mr-2"></i> Apps</a></li>
        <li><a href="../../public/logout.php" class="text-red-600">
            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
    </ul>
</aside>

<nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16">
    <button id="menuBtn" class="md:hidden text-blue-900 text-xl">
        <i class="fa-solid fa-bars"></i>
    </button>
    <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
</nav>

<!-- ===== MAIN ===== -->
<main class="min-h-screen bg-gray-50">

<section class="w-full pt-10 pb-6 mb-8 border-b">
    <div class="max-w-4xl mx-auto text-center px-6">
        <i class="fa-solid fa-qrcode text-5xl text-blue-950 mb-3"></i>
        <h2 class="text-3xl font-extrabold text-blue-950 mb-1">QR Code Akses</h2>
        <p class="text-gray-600">Tunjukkan QR ini ke petugas untuk akses ruangan</p>
    </div>
</section>

<section class="px-6 max-w-lg mx-auto pb-16">
    <div class="bg-white rounded-2xl p-6 shadow-xl border-t-8 border-blue-950">

        <h3 class="text-xl font-extrabold text-blue-950 mb-1 text-center">Identitas Anda</h3>

        <div class="text-center mb-4 border-b pb-3">
            <p class="font-bold text-lg text-blue-950"><?= htmlspecialchars($nama) ?></p>
            <p class="text-sm text-gray-600">
                Role: <span class="font-semibold text-blue-800"><?= htmlspecialchars($role) ?></span>
            </p>
        </div>

        <!-- QR Container -->
        <div class="flex justify-center">
            <div id="qrcode" class="bg-white p-4 rounded-lg border"></div>
        </div>

        <p class="text-xs text-gray-500 text-center mt-4">
            QR berlaku sementara & divalidasi otomatis oleh sistem
        </p>
    </div>
</section>

</main>

<footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950">
    <p class="text-sm">Contact: support@GeoSafe.com</p>
    <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe</p>
</footer>

<!-- ===== SCRIPT ===== -->
<script>
const sidebar = document.getElementById("sidebar");
const menuBtn = document.getElementById("menuBtn");
const overlay = document.getElementById("overlay");

menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("-translate-x-full");
    overlay.classList.toggle("hidden");
});

// Generate QR
new QRCode(document.getElementById("qrcode"), {
    text: <?= json_encode($qrPayload); ?>,
    width: 220,
    height: 220
});
</script>

</body>
</html>