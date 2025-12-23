<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'petugas') {
    if (!isset($_SESSION['user'])) {
        header('Location: ../../public/login.php');
        exit;
    }
}

$user_data = $_SESSION['user'] ?? ['nama' => 'Petugas Keamanan'];

$reports = [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Petugas</title>

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
            <li><a href="home_petugas.php" class="block text-blue-950 font-bold border-l-4 border-blue-600 pl-1">
                    <i class="fa-solid fa-house mr-2"></i> Home</a></li>

            <li class="relative group">
                <a href="apps_petugas.php" class="block hover:text-blue-500">
                    <i class="fa-solid fa-table-cells mr-2"></i> Apps
                </a>
            </li>
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
            <li><a href="home_petugas.php"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Home</a>
            </li>
            <li><a href="apps_petugas.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
            <li>
                <a href="../../public/logout.php"
                    class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    <span class="text-sm font-semibold">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <main class="min-h-screen bg-gray-50 pt-10 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <section class="mb-10 p-6 bg-white shadow-lg rounded-xl border-l-4 border-blue-600">
                <h2 class="text-3xl font-bold text-blue-950 mb-2">Selamat Bertugas,
                    <?= htmlspecialchars($user_data['nama']); ?>!
                </h2>
                <p class="text-gray-600">Dasbor pusat kontrol keamanan dan administrasi kampus.</p>
            </section>

            <div class="grid grid-cols-1 cg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-50">
                            <h3 class="text-lg font-bold text-blue-950 flex items-center">
                                <i class="fa-solid fa-shield-halved mr-2 text-indigo-500 text-base"></i>
                                Verifikasi & Akses
                            </h3>
                            <span
                                class="text-[10px] bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-bold uppercase tracking-wider">Petugas
                                Mode</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <a href="validation_qr.php"
                                class="group flex items-center p-4 bg-gray-50 hover:bg-green-50 rounded-xl transition-all duration-200 border border-gray-100 hover:border-green-200 shadow-sm">
                                <div
                                    class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-qrcode text-2xl text-green-600"></i>
                                </div>
                                <div class="ml-4 text-left">
                                    <p class="text-sm font-bold text-gray-800">Validasi QR</p>
                                    <p class="text-[11px] text-gray-500 leading-tight">Cek status masuk/keluar</p>
                                </div>
                            </a>

                            <a href="check_vehicle.php"
                                class="group flex items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-xl transition-all duration-200 border border-gray-100 hover:border-indigo-200 shadow-sm">
                                <div
                                    class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-check-to-slot text-2xl text-indigo-600"></i>
                                </div>
                                <div class="ml-4 text-left">
                                    <p class="text-sm font-bold text-gray-800">Cek Kendaraan</p>
                                    <p class="text-[11px] text-gray-500 leading-tight">Verifikasi izin parkir</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                </div>
            </div>

            <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100">
                <h3 class="text-base font-bold text-blue-950 mb-4 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-orange-500"></i> Log Aktivitas
                </h3>

                <div class="space-y-2">
                    <div
                        class="flex items-center justify-between p-2.5 hover:bg-gray-50 rounded-xl border border-transparent hover:border-gray-100 transition">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">M-001 (Mahasiswa)</p>
                                <p class="text-[10px] text-gray-500 uppercase">Keluar • Pintu Utara</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-400">10:45</span>
                    </div>

                    <div
                        class="flex items-center justify-between p-2.5 hover:bg-gray-50 rounded-xl border border-transparent hover:border-gray-100 transition">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">D-101 (Dosen)</p>
                                <p class="text-[10px] text-gray-500 uppercase">Masuk • Pintu Utama</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-400">09:15</span>
                    </div>

                    <div
                        class="flex items-center justify-between p-2.5 hover:bg-gray-50 rounded-xl border border-transparent hover:border-gray-100 transition">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs">
                                <i class="fa-solid fa-car"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">B 1234 A (Mobil)</p>
                                <p class="text-[10px] text-gray-500 uppercase">Keluar • Parkir B</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-gray-400">09:00</span>
                    </div>
                </div>

                <a href="log_history.php"
                    class="mt-4 block w-full text-center py-2 text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                    Lihat Semua Riwayat
                </a>
            </div>
            <br><br>
            <div
                class="bg-white p-5 shadow-sm rounded-2xl border border-red-100 mb-6 bg-gradient-to-r from-white to-red-50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-red-800 flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-2 text-red-500 animate-pulse"></i>
                        Peringatan Keamanan Hari Ini
                    </h3>
                    <span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-black rounded-full animate-bounce">
                        3 NEW
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-white/80 rounded-xl border border-red-200 shadow-sm">
                        <div class="mt-1">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <p class="text-xs font-bold text-gray-800">Akses Ditolak (QR Tidak Valid)</p>
                                <span class="text-[9px] text-gray-400 font-medium">08:12 WIB</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-0.5">ID: MHS-99281 mencoba akses di Gerbang Barat.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-white/80 rounded-xl border border-amber-200 shadow-sm">
                        <div class="mt-1">
                            <i class="fa-solid fa-car-burst text-amber-500 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <p class="text-xs font-bold text-gray-800">Parkir Melebihi Batas Waktu</p>
                                <span class="text-[9px] text-gray-400 font-medium">14:05 WIB</span>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-0.5">Kendaraan B 4432 KLY terpantau di Area Parkir C
                                selama 24 jam+.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <a href="../security_reports.php"
                        class="py-2 text-[11px] font-bold text-center text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm">
                        Lapor Sekarang
                    </a>
                    <a href="../security_information.php"
                        class="py-2 text-[11px] font-bold text-center text-red-600 border border-red-200 hover:bg-red-100 rounded-lg transition">
                        Lihat Semua
                    </a>
                </div>
            </div>
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