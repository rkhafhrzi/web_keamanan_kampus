<?php
session_start();

// 1. Panggil file koneksi
require_once '../../include/connection.php';

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
$nama = $user['nama'] ?? $user['email'] ?? 'Mahasiswa';

// 2. Cara mengambil koneksi PDO dari Class Database
$pdo = Database::getConnection();

try {
    // --- QUERY 1: INFO KEAMANAN ---
    // Menggunakan PDO prepare statement agar lebih aman
    // --- QUERY 1: INFO KEAMANAN TERBARU ---
// Kita ambil 1 laporan terbaru secara global (tanpa WHERE user_id) 
// atau dari tabel khusus informasi jika ada.
    $stmt_info = $pdo->prepare("SELECT report_type, description, generated_at FROM reports ORDER BY generated_at DESC LIMIT 1");
    $stmt_info->execute();
    $info_keamanan = $stmt_info->fetch();

    // --- QUERY 2: STATUS LAPORAN TERAKHIR ---
    // Sesuaikan nama tabel (misal: reports atau lost_items) dan kolom (misal: user_id)
    // --- QUERY 3: BARANG HILANG TERAKHIR ---
    // --- QUERY BARANG HILANG TERAKHIR ---
    $stmt_lost = $pdo->prepare("SELECT id, item_name, description, status, lost_date FROM lost_items WHERE reporter_id = :userId ORDER BY created_at DESC LIMIT 2");
    $stmt_lost->execute(['userId' => $userId]);
    $lost_items = $stmt_lost->fetchAll();

} catch (PDOException $e) {
    // Jika ada error query, simpan sebagai array kosong agar halaman tidak pecah
    $info_keamanan = null;
    $reports = [];
    // Opsi: echo "Error: " . $e->getMessage(); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Mahasiswa</title>

    <?php include '../../include/header.php'; ?>
</head>

<body class="bg-gray-50">
    <div id="overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>

    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

        <div class="p-5 border-b bg-blue-950 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>

        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="home_mahasiswa.php" class="block text-blue-950 font-bold border-l-4 border-gray-600 pl-1">
                    <i class="fa-solid fa-house mr-2"></i> Home</a></li>

            <li class="relative group">
                <a href="apps_mahasiswa.php" class="block hover:text-blue-500">
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
            <li><a href="home_mahasiswa.php"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Home</a>
            </li>
            <li><a href="apps_mahasiswa.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
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

            <section class="mb-10 p-6 bg-white shadow-lg rounded-xl border-l-4 border-gray-600">
                <h2 class="text-3xl font-bold text-blue-950 mb-2">Selamat Datang,
                    <?= htmlspecialchars($nama); ?>!
                </h2>
                <p class="text-gray-600">Portal Anda untuk semua layanan kampus. Tetap terhubung dan aman.</p>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white p-6 shadow-xl rounded-xl">
                        <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center">
                            <i class="fa-solid fa-shield-halved mr-2 text-green-600"></i> Info Keamanan Terbaru
                        </h3>

                        <?php if ($info_keamanan): ?>
                            <div class="p-3 bg-green-50 rounded-lg border border-green-200 text-sm text-gray-700 mb-4">
                                <p class="font-bold text-green-700 mb-1">
                                    <?php echo htmlspecialchars($info_keamanan['report_type'] ?? $info_keamanan['report_type'] ?? 'Informasi'); ?>
                                </p>
                                <p class="text-xs text-gray-600 line-clamp-2">
                                    <?php echo htmlspecialchars($info_keamanan['description'] ?? $info_keamanan['description'] ?? '-'); ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-500 mb-4 italic">
                                Belum ada informasi keamanan terbaru saat ini.
                            </div>
                        <?php endif; ?>

                        <a href="../security_information.php"
                            class="w-full text-center inline-block bg-blue-950 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                            Lihat Semua Informasi Detail <i class="fa-solid fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white p-6 shadow-xl rounded-xl">
                        <h3 class="text-xl font-bold text-blue-950 mb-5 border-b pb-2 flex items-center">
                            <i class="fa-solid fa-bolt mr-2 text-indigo-500"></i> Akses Cepat
                        </h3>
                        <div class="grid grid-cols-2 max-w-xl mx-auto gap-6 text-center">
                            <a href="qr_mahasiswa.php"
                                class="block p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition duration-150 shadow-md border border-indigo-100">
                                <i class="fa-solid fa-qrcode text-4xl text-indigo-950 mb-2"></i>
                                <p class="text-md font-semibold text-gray-700">Tampilkan QR Code</p>
                                <p class="text-xs text-gray-500 mt-1">Akses cepat dan validasi</p>
                            </a>

                            <a href="../security_reports.php"
                                class="block p-4 bg-red-50 hover:bg-red-100 rounded-lg transition duration-150 shadow-md border border-red-100">
                                <i class="fa-solid fa-flag text-4xl text-red-600 mb-2"></i>
                                <p class="text-md font-semibold text-gray-700">Lapor Keamanan</p>
                                <p class="text-xs text-gray-500 mt-1">Laporkan insiden darurat</p>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white p-6 shadow-xl rounded-xl mt-8">
                        <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center">
                            <i class="fa-solid fa-map-pin mr-2 text-red-500"></i> Status Laporan
                        </h3>

                        <?php if (empty($lost_items)): ?>
                            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <i class="fa-solid fa-circle-info text-3xl text-gray-400 mb-2"></i>
                                <p class="text-md text-gray-600">Tidak ada laporan barang hilang.</p>
                                <p class="text-sm text-gray-500 mt-1">Gunakan menu lapor jika Anda kehilangan sesuatu.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($lost_items as $item): ?>
                                    <div
                                        class="p-4 bg-orange-50/50 rounded-lg border border-orange-100 flex justify-between items-start">
                                        <div class="flex-1 pr-4">
                                            <p class="text-sm font-bold text-blue-950 uppercase tracking-wide">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                            </p>

                                            <p class="text-xs text-gray-600 mt-1 line-clamp-1">
                                                <?php echo htmlspecialchars($item['description']); ?>
                                            </p>

                                            <p class="text-[10px] text-gray-400 mt-2 flex items-center">
                                                <i class="fa-regular fa-calendar-check mr-1"></i>
                                                ID: #LI-<?php echo htmlspecialchars($item['id']); ?> •
                                                Kejadian: <?php echo date('d M Y', strtotime($item['lost_date'])); ?>
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase 
                            <?php
                            $status_lost = strtolower($item['status'] ?? 'hilang');
                            if ($status_lost == 'ditemukan' || $status_lost == 'dikembalikan') {
                                echo 'bg-green-100 text-green-700';
                            } else {
                                echo 'bg-red-100 text-red-700';
                            }
                            ?>">
                                                <?php echo htmlspecialchars($item['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="mt-4 pt-2 text-center">
                                <a href="../security_reports.php"
                                    class="text-xs font-semibold text-green-600 hover:text-green-800 flex items-center justify-center">
                                    Lihat Status Laporan <i class="fa-solid fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
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