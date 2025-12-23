<?php
session_start();

if (!isset($_SESSION['user']) || !is_array($_SESSION['user']) || ($_SESSION['user']['role'] !== 'mahasiswa'))  {
    header('Location: ../../public/login.php'); 
    exit;
}

$user = $_SESSION['user'];

$kode = $user['kode_identitas'] ?? null; 
$nama = $user['nama'] ?? 'Pengguna Tidak Dikenal';
$role = $user['role'] ?? 'mahasiswa'; 

$role_display = 'mahasiswa'; 

if (!$kode) {
    $error_message = "Kode identitas Dosen tidak ditemukan dalam sesi.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Mahasiswa</title>
    <?php include '../../include/header.php'; ?>
</head>

<body class="bg-gray-50">
    <div id="overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>

    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

        <div class="p-5 border-b bg-blue-900 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>

        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="home_mahasiswa.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>
                    Home</a></li>
            <li><a href="apps_mahasiswa.php" class="block text-blue-900 font-bold border-l-4 border-blue-600 pl-1"><i
                        class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../../public/logout.php" class="block hover:text-red-500"><i
                        class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_mahasiswa.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_mahasiswa.php" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a>
            </li>
            <li>
                <a href="../../public/logout.php"
                    class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    <span class="text-sm font-semibold">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <main class="min-h-screen bg-gray-50">

        <section class="w-full bg-gray-50 pt-8 pb-6 mb-8 border-b-2 border-gray-200">
            <div class="max-w-4xl mx-auto text-center px-6">
                <i class="fa-solid fa-qrcode text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">QR Code Akses</h2>
                <p class="text-gray-600 text-base">Gunakan untuk absensi, akses masuk, dan identifikasi cepat.</p>
            </div>
        </section>

        <section class="mt-4 px-6 max-w-lg mx-auto pb-16">

            <div class="bg-white rounded-2xl p-6 shadow-xl border-t-8 border-blue-950
                        transition duration-300 transform hover:-translate-y-0.5 hover:shadow-2xl">

                <h3 class="text-xl font-extrabold text-blue-950 mb-1 text-center">Identitas Anda</h3>
                <p class="text-gray-600 text-sm mb-4 text-center">Tunjukkan QR Code ini kepada petugas.</p>

                <div class="text-center mb-4 border-b pb-3 border-gray-200">
                    <p class="font-bold text-lg text-blue-950"><?= htmlspecialchars($nama) ?></p>
                    <p class="text-sm text-gray-600">
                        <?= htmlspecialchars($role) ?>: <span class="font-semibold text-blue-800"><?= htmlspecialchars($kode) ?></span>
                    </p>
                </div>

                <?php if ($kode): ?>
                <div class="bg-gray-100 p-4 rounded-lg flex justify-center border-2 border-gray-300">
                    <img src="../../public/qr.php?kode=<?= urlencode($kode) ?>" alt="QR Code Akses" 
                        class="w-56 h-56 bg-white p-2 rounded-lg shadow-md hover:shadow-xl transition duration-300"
                        onerror="this.onerror=null; this.src='https://via.placeholder.com/224?text=QR+Gagal';"
                        title="Kode Anda: <?= htmlspecialchars($kode) ?>">
                </div>

                <div class="text-center mt-6">
                    <a href="../../public/qr.php?kode=<?= urlencode($kode) ?>&download=true"
                        download="qr-<?= htmlspecialchars($kode) ?>.png"
                        class="inline-flex items-center bg-blue-950 text-white font-semibold px-5 py-2 rounded-full hover:bg-blue-800 transition shadow-md">
                        <i class="fa-solid fa-download mr-2"></i> Download Gambar QR
                    </a>
                </div>
                 <?php else: ?>
                    <div class="bg-red-50 border border-red-300 text-red-700 p-4 rounded-lg text-center font-semibold">
                        Gagal memuat QR Code. Kode identitas Dosen tidak ditemukan.
                    </div>
                <?php endif; ?>
            </div>

        </section>

    </main>
    
    <footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
        <p class="text-sm">Contact: support@GeoSafe.com</p>
        <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe. All rights reserved.</p>
    </footer>

    <script>
        const sidebar = document.getElementById("sidebar");
        const menuBtn = document.getElementById("menuBtn");
        const overlay = document.getElementById("overlay");

        function toggleSidebar() {
            sidebar.classList.toggle("-translate-x-full");
            
            if (sidebar.classList.contains("-translate-x-full")) {
                overlay.classList.add("opacity-0");
                setTimeout(() => overlay.classList.add("hidden"), 300);
            } else {
                overlay.classList.remove("hidden");
                setTimeout(() => overlay.classList.remove("opacity-0"), 10);
            }
        }

        menuBtn.addEventListener("click", toggleSidebar);
        overlay.addEventListener("click", toggleSidebar);
    </script>
</body>

</html>