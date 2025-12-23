<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    if (!isset($_SESSION['user'])) {
        header('Location: ../../public/login.php');
        exit;
    }
}

$lost_found_items = [
    ['id' => 1, 'barang' => 'Dompet Kulit Hitam', 'ditemukan_oleh' => 'Petugas A', 'lokasi' => 'Area Parkir C', 'tanggal' => '2025-12-16', 'status' => 'Belum Diambil'],
    ['id' => 2, 'barang' => 'Kunci Motor Yamaha', 'ditemukan_oleh' => 'Mahasiswa B', 'lokasi' => 'Lantai 3 Gedung R', 'tanggal' => '2025-12-15', 'status' => 'Sudah Diambil'],
];

$registered_vehicles = [
    ['plat' => 'B 1234 XYZ', 'tipe' => 'Motor', 'pemilik' => '210987654 (Mhs)', 'status' => 'Aktif'],
    ['plat' => 'D 5678 ABC', 'tipe' => 'Mobil', 'pemilik' => 'DSN1001 (Dosen)', 'status' => 'Aktif'],
];

$alerts = [
    ['waktu' => '2025-12-16 15:45', 'sumber' => 'Petugas Pos 2', 'isi' => 'Ada kebakaran kecil di tempat sampah belakang Gedung A. Sudah ditangani.', 'level' => 'Wajar'],
    ['waktu' => '2025-12-16 10:00', 'sumber' => 'Sistem Peringatan', 'isi' => 'Peringatan kepadatan lalu lintas di pintu masuk utama.', 'level' => 'Normal'],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Operasional Keamanan</title>

    <?php include '../../include/header.php'; ?>

    <style>
        .tab-content {
            display: none;
        }

        .tab-button.active {
            border-bottom: 3px solid #F97316; 
            color: #C2410C;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>
    <aside id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
        <div class="p-5 border-b bg-blue-900 text-white"><h2 class="text-xl font-bold">GeoSafe</h2></div>
        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="home_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
            <li><a href="apps_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../../actions/logout.php" class="block hover:text-red-500"><i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">Menu Operasional Keamanan</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_petugas.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
            <li><a href="../../actions/logout.php" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1"><i class="fa-solid fa-right-from-bracket text-sm"></i><span class="text-sm font-semibold">Logout</span></a></li>
        </ul>
    </nav>
    <main class="min-h-screen bg-gray-100 pt-10 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-extrabold text-blue-950 mb-6 flex items-center">
                <i class="fa-solid fa-shield-halved mr-3 text-orange-600"></i> Alat Bantu Petugas
            </h2>

            <div class="bg-white p-2 rounded-t-xl shadow-md border-b-2 border-gray-200 flex justify-between overflow-x-auto">
                <button data-tab="lost_found" class="tab-button flex-1 text-center py-3 px-2 text-gray-700 hover:bg-gray-50 transition duration-150 rounded-lg">
                    <i class="fa-solid fa-magnifying-glass-location mr-1"></i> Kehilangan & Temuan
                </button>
                <button data-tab="alerts" class="tab-button flex-1 text-center py-3 px-2 text-gray-700 hover:bg-gray-50 transition duration-150 rounded-lg">
                    <i class="fa-solid fa-bell mr-1"></i> Sistem Peringatan
                </button>
                <button data-tab="vehicles" class="tab-button flex-1 text-center py-3 px-2 text-gray-700 hover:bg-gray-50 transition duration-150 rounded-lg">
                    <i class="fa-solid fa-car mr-1"></i> Kendaraan Terdaftar
                </button>
            </div>

            <div class="bg-white p-6 shadow-xl rounded-b-xl mb-8">

                <div id="tab-lost_found" class="tab-content">
                    <h3 class="text-xl font-bold text-orange-700 mb-4 flex items-center">
                        <i class="fa-solid fa-box-open mr-2"></i> Daftar Barang Temuan
                    </h3>
                    <p class="text-gray-600 mb-6">Catatan barang yang ditemukan di lingkungan kampus/gedung. Petugas dapat memperbarui status jika barang sudah diambil oleh pemiliknya.</p>
                    
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg mb-4 hover:bg-blue-700">
                        <i class="fa-solid fa-plus mr-2"></i> Tambah Barang Temuan Baru
                    </button>

                    <div class="overflow-x-auto shadow-lg rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi Ditemukan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($lost_found_items as $item): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['barang']); ?></div>
                                        <div class="text-xs text-gray-500">Oleh: <?= htmlspecialchars($item['ditemukan_oleh']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($item['lokasi']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($item['tanggal']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php echo ($item['status'] == 'Belum Diambil') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?= htmlspecialchars($item['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900">Ubah Status</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="tab-alerts" class="tab-content">
                    <h3 class="text-xl font-bold text-red-700 mb-4 flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i> Log Peringatan & Insiden
                    </h3>
                    <p class="text-gray-600 mb-6">Catatan insiden atau peringatan yang memerlukan perhatian cepat. Petugas dapat melihat riwayat dan mencatat insiden baru.</p>

                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg mb-4 hover:bg-red-700">
                        <i class="fa-solid fa-message-alert mr-2"></i> Buat Peringatan Baru
                    </button>

                    <div class="space-y-4">
                        <?php foreach ($alerts as $alert): ?>
                        <div class="p-4 rounded-lg shadow-md border-l-4 
                            <?php echo ($alert['level'] == 'Wajar') ? 'bg-orange-50 border-orange-500' : 'bg-blue-50 border-blue-500'; ?>">
                            <div class="flex justify-between items-center">
                                <span class="font-bold 
                                    <?php echo ($alert['level'] == 'Wajar') ? 'text-orange-700' : 'text-blue-700'; ?>">
                                    [<?= htmlspecialchars($alert['level']); ?>]
                                </span>
                                <span class="text-xs text-gray-500"><?= htmlspecialchars($alert['waktu']); ?></span>
                            </div>
                            <p class="mt-1 text-gray-800 text-sm"><?= htmlspecialchars($alert['isi']); ?></p>
                            <p class="text-xs text-gray-600 mt-1">Sumber: <?= htmlspecialchars($alert['sumber']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="tab-vehicles" class="tab-content">
                    <h3 class="text-xl font-bold text-green-700 mb-4 flex items-center">
                        <i class="fa-solid fa-motorcycle mr-2"></i> Daftar Kendaraan Resmi
                    </h3>
                    <p class="text-gray-600 mb-6">Daftar kendaraan yang memiliki izin parkir resmi (dosen, staf, mahasiswa tertentu). Digunakan untuk validasi dan penertiban parkir.</p>

                    <div class="mb-4 flex space-x-3">
                         <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            <i class="fa-solid fa-id-card-clip mr-2"></i> Registrasi Kendaraan Baru
                        </button>
                        <input type="text" placeholder="Cari Plat Nomor..."
                            class="flex-grow px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="overflow-x-auto shadow-lg rounded-lg border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat Nomor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik (ID)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($registered_vehicles as $vehicle): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?= htmlspecialchars($vehicle['plat']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($vehicle['tipe']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($vehicle['pemilik']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars($vehicle['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
                overlay.classList.add("hidden", "opacity-0");
            } else {
                overlay.classList.remove("hidden");
                setTimeout(() => overlay.classList.remove("opacity-0"), 10);
            }
        }
        menuBtn.addEventListener("click", toggleSidebar);
        overlay.addEventListener("click", toggleSidebar);

        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        function showTab(tabId) {
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            document.getElementById(`tab-${tabId}`).style.display = 'block';
            document.querySelector(`button[data-tab="${tabId}"]`).classList.add('active');
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                showTab(button.dataset.tab);
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            showTab('lost_found'); 
        });
    </script>
</body>

</html>