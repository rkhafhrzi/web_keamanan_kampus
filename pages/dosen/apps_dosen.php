<?php
session_start();

if (
    !isset($_SESSION['user']) ||
    !is_array($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'dosen'
) {
    header('Location: ../../public/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>App Dosen</title>

    <?php include '../../include/header.php'; ?>

    <style>
        #overlay {
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 40;
        }

        #overlay.show {
            opacity: 1;
        }
    </style>

</head>

<body class="bg-gray-50">
    <div id="overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>

    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

        <div class="p-5 border-b bg-blue-950 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>

        <ul class="p-5 space-y-5 text-blue-950 font-medium">
            <li><a href="home_mahasiswa.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>
                    Home</a></li>
            <li><a href="apps_mahasiswa.php" class="block text-blue-900 font-bold border-l-4 border-blue-600 pl-1">
                    <i class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../../public/logout.php" class="block hover:text-red-500">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-950 text-xl" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-2xl font-bold text-blue-950 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_dosen.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_dosen.php"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a>
            </li>
            <li><a href="../../public/logout.php"
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
                <i class="fa-solid fa-th-large text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Aplikasi Sistem</h2>
                <p class="text-gray-600 text-base">Jelajahi semua fitur dan layanan yang tersedia.</p>
            </div>
        </section>

        <section class="mt-4 px-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6 max-w-6xl mx-auto pb-16">
            
            <a href="room_location.php" class="group block">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-indigo-600 transition duration-300 transform group-hover:scale-[1.02] group-hover:shadow-2xl">
                    
                    <div class="flex flex-col items-center text-center space-y-3">
                        <i class="fa-solid fa-map-marked-alt text-5xl text-indigo-600 transition-colors duration-300 group-hover:text-blue-950"></i>
                        <h3 class="text-xl font-extrabold text-blue-950">Lokasi Ruangan</h3>
                        <p class="text-gray-500 text-sm">Cari dan lihat detail status ketersediaan ruangan kampus.</p>
                    </div>
                    
                    <hr class="border-gray-100 my-4">

                    <div class="flex justify-center items-center">
                        <span class="inline-flex items-center text-sm font-bold text-indigo-600 group-hover:text-blue-950 transition">
                            Cari Ruangan <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>
            <a href="qr_dosen.php" class="group block">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-600 transition duration-300 transform group-hover:scale-[1.02] group-hover:shadow-2xl">
                    
                    <div class="flex flex-col items-center text-center space-y-3">
                        <i class="fa-solid fa-qrcode text-5xl text-blue-950 transition-colors duration-300 group-hover:text-blue-600"></i>
                        <h3 class="text-xl font-extrabold text-blue-950">QR Code Akses</h3>
                        <p class="text-gray-500 text-sm">Kode unik untuk absensi dan validasi masuk.</p>
                    </div>
                    
                    <hr class="border-gray-100 my-4">

                    <div class="flex justify-center items-center">
                        <span class="inline-flex items-center text-sm font-bold text-blue-600 group-hover:text-blue-950 transition">
                            Akses Cepat <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

            <a href="../registration_vehicle.php" class="group block">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-gray-600 transition duration-300 transform group-hover:scale-[1.02] group-hover:shadow-2xl">
                    
                    <div class="flex flex-col items-center text-center space-y-3">
                        <i class="fa-solid fa-id-card text-5xl text-gray-600 transition-colors duration-300 group-hover:text-blue-950"></i>
                        <h3 class="text-xl font-extrabold text-blue-950">Registrasi Kendaraan</h3>
                        <p class="text-gray-500 text-sm">Daftar kendaraan Anda untuk izin parkir kampus.</p>
                    </div>
                    
                    <hr class="border-gray-100 my-4">

                    <div class="flex justify-center items-center">
                        <span class="inline-flex items-center text-sm font-bold text-gray-600 group-hover:text-blue-950 transition">
                            Layanan Otomotif <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

            <a href="../security_information.php" class="group block">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-green-600 transition duration-300 transform group-hover:scale-[1.02] group-hover:shadow-2xl">
                    
                    <div class="flex flex-col items-center text-center space-y-3">
                        <i class="fa-solid fa-shield-halved text-5xl text-green-600 transition-colors duration-300 group-hover:text-blue-950"></i>
                        <h3 class="text-xl font-extrabold text-blue-950">Informasi Keamanan</h3>
                        <p class="text-gray-500 text-sm">Periksa peringatan dan panduan keamanan terbaru.</p>
                    </div>
                    
                    <hr class="border-gray-100 my-4">

                    <div class="flex justify-center items-center">
                        <span class="inline-flex items-center text-sm font-bold text-green-600 group-hover:text-blue-950 transition">
                            Peringatan Sistem <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

            <a href="../security_reports.php" class="group block">
                <div
                    class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-red-600 transition duration-300 transform group-hover:scale-[1.02] group-hover:shadow-2xl">
                    
                    <div class="flex flex-col items-center text-center space-y-3">
                        <i class="fa-solid fa-file-lines text-5xl text-red-600 transition-colors duration-300 group-hover:text-blue-950"></i>
                        <h3 class="text-xl font-extrabold text-blue-950">Laporan Keamanan</h3>
                        <p class="text-gray-500 text-sm">Kirim dan tinjau riwayat laporan insiden atau darurat.</p>
                    </div>
                    
                    <hr class="border-gray-100 my-4">

                    <div class="flex justify-center items-center">
                        <span class="inline-flex items-center text-sm font-bold text-red-600 group-hover:text-blue-950 transition">
                            Lapor Insiden <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                        </span>
                    </div>
                </div>
            </a>

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