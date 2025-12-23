<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_data = $_SESSION['user'] ?? ['nama' => 'Pengguna'];

$reports = []; 

$room_details = [
    'R-305' => [
        'nama' => 'Ruang Kelas Multimedia',
        'kapasitas' => 40,
        'fasilitas' => ['Proyektor', 'AC', 'Whiteboard Digital', 'WIFI'],
        'lokasi' => 'Gedung A, Lantai 3',
        'status' => 'Tersedia',
    ],
    'LAB-KIM' => [
        'nama' => 'Laboratorium Kimia Dasar',
        'kapasitas' => 25,
        'fasilitas' => ['Fume Hood', 'Peralatan Gelas Lengkap', 'Stasiun Darurat'],
        'lokasi' => 'Gedung Lab, Lantai 1',
        'status' => 'Sedang Dipakai',
    ],
];

$search_query = $_GET['q'] ?? '';
$search_result = null;

if ($search_query) {
    $key = strtoupper(trim($search_query));
    if (isset($room_details[$key])) {
        $search_result = $room_details[$key];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Dosen</title>

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
            <li><a href="home_mahasiswa.php" class="block text-blue-950 font-bold border-l-4 border-blue-600 pl-1">
                    <i class="fa-solid fa-house mr-2"></i> Home</a></li>
            <li><a href="apps_mahasiswa.php" class="block hover:text-blue-500">
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
            <li><a href="home_dosen.php" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Home</a></li>
            <li><a href="apps_dosen.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
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
                <h2 class="text-3xl font-bold text-blue-950 mb-2">Selamat Datang, <?= htmlspecialchars($user_data['nama']); ?>!</h2>
                <p class="text-gray-600">Portal Anda untuk semua layanan kampus. Tetap terhubung dan aman.</p>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-white p-6 shadow-xl rounded-xl">
                        <h3 class="text-xl font-bold text-blue-950 mb-5 border-b pb-2 flex items-center">
                            <i class="fa-solid fa-map-marked-alt mr-2 text-blue-600"></i> Cari & Cek Status Ruangan
                        </h3>
                        
                        <form action="room_location.php" method="GET" class="space-y-4 mb-4">
                            <label for="room_query" class="block text-sm font-medium text-gray-700">Masukkan Kode Ruangan</label>
                            
                            <div class="flex space-x-2">
                                <input type="text" id="room_query" name="q" required value="<?= htmlspecialchars($search_query); ?>"
                                    class="flex-grow p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                                    placeholder="Contoh: R-305 atau LAB-KIM">
                                
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-150">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <?php if ($search_query && $search_result): ?>
                            <div class="mt-4 p-4 border-l-4 <?= $search_result['status'] == 'Tersedia' ? 'border-green-600 bg-green-50' : 'border-red-600 bg-red-50' ?> rounded-lg shadow-inner">
                                <h4 class="text-lg font-bold text-blue-900 mb-2 flex items-center">
                                    <i class="fa-solid fa-building mr-2"></i> Detail: <?= strtoupper(htmlspecialchars($search_query)); ?>
                                </h4>
                                <ul class="text-sm space-y-1 text-gray-700">
                                    <li><span class="font-semibold">Nama:</span> <?= htmlspecialchars($search_result['nama']); ?></li>
                                    <li><span class="font-semibold">Lokasi:</span> <?= htmlspecialchars($search_result['lokasi']); ?></li>
                                    <li><span class="font-semibold">Status:</span> 
                                        <span class="font-bold <?= $search_result['status'] == 'Tersedia' ? 'text-green-700' : 'text-red-700' ?>">
                                            <?= htmlspecialchars($search_result['status']); ?>
                                        </span>
                                    </li>
                                    <li><span class="font-semibold">Fasilitas:</span> <?= implode(', ', array_slice($search_result['fasilitas'], 0, 2)); ?>...</li>
                                </ul>
                            </div>
                        <?php elseif ($search_query && !$search_result): ?>
                             <div class="mt-4 p-4 border-l-4 border-yellow-600 bg-yellow-50 rounded-lg shadow-inner text-sm text-gray-700">
                                <p class="font-semibold flex items-center"><i class="fa-solid fa-exclamation-triangle mr-2"></i> Ruangan dengan kode **<?= htmlspecialchars($search_query); ?>** tidak ditemukan.</p>
                             </div>
                        <?php endif; ?>

                        <p class="text-xs text-gray-500 mt-4">Untuk informasi lebih lengkap tentang peminjaman, gunakan menu **Apps**.</p>
                    </div>

                    <div class="bg-white p-6 shadow-xl rounded-xl">
                        <h3 class="text-xl font-bold text-blue-950 mb-5 border-b pb-2 flex items-center"><i class="fa-solid fa-bolt mr-2 text-indigo-500"></i> Akses Cepat</h3>
                        <div class="grid grid-cols-2 max-w-xl mx-auto gap-6 text-center">
                            
                            <a href="qr_dosen.php" class="block p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition duration-150 shadow-md">
                                <i class="fa-solid fa-qrcode text-4xl text-indigo-950 mb-2"></i>
                                <p class="text-md font-semibold text-gray-700">Tampilkan QR Code</p>
                                <p class="text-xs text-gray-500 mt-1">Akses cepat dan validasi</p>
                            </a>
                            
                            <a href="../security_reports.php" class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg transition duration-150 shadow-md">
                                <i class="fa-solid fa-flag text-4xl text-red-600 mb-2"></i>
                                <p class="text-md font-semibold text-gray-700">Lapor Keamanan</p>
                                <p class="text-xs text-gray-500 mt-1">Laporkan insiden darurat</p>
                            </a>
                            
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">
                    
                    <div class="bg-white p-6 shadow-xl rounded-xl">
                        <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center"><i class="fa-solid fa-shield-halved mr-2 text-green-600"></i> Info Keamanan Terbaru</h3>
                        
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200 text-sm text-gray-700 mb-4">
                            <p class="font-bold text-green-700 mb-1">Peringatan: Peningkatan Patroli Malam</p>
                            <p class="text-xs text-gray-600 line-clamp-2">
                                Dalam rangka meningkatkan keamanan, jam patroli malam di area asrama dan perpustakaan diperpanjang. Harap selalu kunci kendaraan Anda.
                            </p>
                        </div>

                        <a href="../security_information.php"
                            class="w-full text-center inline-block bg-blue-950 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                            Lihat Semua Informasi Detail <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    
                    <div class="bg-white p-6 shadow-xl rounded-xl">
                        <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center"><i class="fa-solid fa-map-pin mr-2 text-red-500"></i> Status Laporan Terakhir</h3>
                        <?php if (empty($reports)): ?>
                            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <i class="fa-solid fa-check-circle text-3xl text-green-500 mb-2"></i>
                                <p class="text-md text-gray-600">Tidak ada laporan aktif saat ini.</p>
                                <p class="text-sm text-gray-500 mt-1">Semua aman. Ajukan laporan jika terjadi insiden.</p>
                            </div>
                        <?php else: ?>
                            <?php endif; ?>
                    </div>

                </div>
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