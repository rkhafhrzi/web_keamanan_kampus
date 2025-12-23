<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    if (!isset($_SESSION['user'])) {
        header('Location: ../../public/login.php');
        exit;
    }
}

$filter_role = $_GET['role'] ?? 'Semua';

$data_log_universal = [
    ['id_entitas' => 'DSN1001', 'nama' => 'Dr. Budi Santoso', 'role' => 'Dosen', 'waktu_masuk' => '2025-12-16 08:00:12', 'waktu_keluar' => '2025-12-16 16:30:45', 'status' => 'Selesai', 'kendaraan' => 'Mobil'],
    ['id_entitas' => 'DSN1002', 'nama' => 'Prof. Ani Kurnia', 'role' => 'Dosen', 'waktu_masuk' => '2025-12-16 09:30:00', 'waktu_keluar' => 'N/A', 'status' => 'Aktif', 'kendaraan' => 'Tanpa Kendaraan'],

    ['id_entitas' => '220123456', 'nama' => 'Suci Saffrilia', 'role' => 'Mahasiswa', 'waktu_masuk' => '2025-12-16 13:00:00', 'waktu_keluar' => '2025-12-16 15:45:00', 'status' => 'Selesai', 'kendaraan' => 'Sepeda Motor'],
    ['id_entitas' => '210987654', 'nama' => 'Rizky Pratama', 'role' => 'Mahasiswa', 'waktu_masuk' => '2025-12-16 14:10:00', 'waktu_keluar' => 'N/A', 'status' => 'Aktif', 'kendaraan' => 'Sepeda Motor'],

    ['id_entitas' => 'T0045', 'nama' => 'Santi Dewi', 'role' => 'Tamu', 'waktu_masuk' => '2025-12-16 14:00:00', 'waktu_keluar' => 'N/A', 'status' => 'Aktif', 'kendaraan' => 'Tanpa Kendaraan'],
    ['id_entitas' => 'T00123', 'nama' => 'Bambang Sudarso', 'role' => 'Tamu', 'waktu_masuk' => '2025-12-15 10:30:00', 'waktu_keluar' => '2025-12-15 11:45:00', 'status' => 'Selesai', 'kendaraan' => 'Mobil'],
];

$filtered_logs = [];
foreach ($data_log_universal as $log) {
    if ($filter_role === 'Semua' || $log['role'] === $filter_role) {
        $filtered_logs[] = $log;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Log</title>

    <?php include '../../include/header.php'; ?>

</head>

<body class="bg-gray-50">
    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>
    <aside id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
        <div class="p-5 border-b bg-blue-900 text-white"><h2 class="text-xl font-bold">GeoSafe</h2></div>
        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="home_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
            <li><a href="apps_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../../public/logout.php" class="block hover:text-red-500"><i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_petugas.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
            <li><a href="../../public/logout.php" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1"><i class="fa-solid fa-right-from-bracket text-sm"></i><span class="text-sm font-semibold">Logout</span></a></li>
        </ul>
    </nav>
    <main class="min-h-screen bg-gray-100 pt-10 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-extrabold text-blue-950 mb-6 flex items-center">
                <i class="fa-solid fa-clock-rotate-left mr-3 text-indigo-500"></i> Riwayat Masuk & Keluar
            </h2>

            <div class="bg-white p-6 shadow-xl rounded-xl mb-8">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 border-b pb-4">
                    <h3 class="text-xl font-bold text-blue-900 mb-2 md:mb-0">Filter Data</h3>

                    <form method="GET" action="log_history.php" class="flex space-x-3 items-center">
                        <label for="role_filter" class="text-gray-600 text-sm font-medium">Lihat:</label>
                        <select name="role" id="role_filter" onchange="this.form.submit()" 
                                class="border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="Semua" <?= ($filter_role === 'Semua') ? 'selected' : ''; ?>>Semua Entitas</option>
                            <option value="Dosen" <?= ($filter_role === 'Dosen') ? 'selected' : ''; ?>>Dosen/Staf</option>
                            <option value="Mahasiswa" <?= ($filter_role === 'Mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                            <option value="Tamu" <?= ($filter_role === 'Tamu') ? 'selected' : ''; ?>>Tamu</option>
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto shadow-lg rounded-lg border">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-950 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Entitas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Peran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Waktu Masuk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Waktu Keluar</th>
                                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider">Durasi/Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Kendaraan</th> </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($filtered_logs)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data log yang ditemukan untuk filter ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filtered_logs as $log): 
                                    $is_aktif = ($log['status'] === 'Aktif');
                                    
                                    $durasi = 'N/A';
                                    if (!$is_aktif) {
                                        $masuk_ts = strtotime($log['waktu_masuk']);
                                        $keluar_ts = strtotime($log['waktu_keluar']);
                                        $diff = abs($keluar_ts - $masuk_ts);
                                        $hours = floor($diff / 3600);
                                        $minutes = floor(($diff % 3600) / 60);
                                        $durasi = "{$hours}j {$minutes}m";
                                    }

                                    $kendaraan_icon = '';
                                    $kendaraan_class = 'text-gray-600';
                                    if ($log['kendaraan'] === 'Sepeda Motor') {
                                        $kendaraan_icon = '<i class="fa-solid fa-motorcycle mr-1"></i>';
                                        $kendaraan_class = 'text-indigo-600 font-semibold';
                                    } elseif ($log['kendaraan'] === 'Mobil') {
                                        $kendaraan_icon = '<i class="fa-solid fa-car-side mr-1"></i>';
                                        $kendaraan_class = 'text-blue-600 font-semibold';
                                    } else {
                                        $kendaraan_icon = '<i class="fa-solid fa-person-walking mr-1"></i>';
                                        $kendaraan_class = 'text-gray-500';
                                    }
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($log['nama']); ?></div>
                                        <div class="text-xs text-gray-500">ID: <?= htmlspecialchars($log['id_entitas']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold 
                                        <?php 
                                            echo ($log['role'] == 'Dosen') ? 'text-blue-600' : 
                                                (($log['role'] == 'Mahasiswa') ? 'text-indigo-600' : 'text-green-600'); 
                                        ?>">
                                        <?= htmlspecialchars($log['role']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($log['waktu_masuk']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($log['waktu_keluar']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <?php if ($is_aktif): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                AKTIF
                                            </span>
                                        <?php else: ?>
                                            <span class="text-sm font-medium text-gray-800"><?= $durasi; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm <?= $kendaraan_class; ?>">
                                        <?= $kendaraan_icon; ?>
                                        <?= htmlspecialchars($log['kendaraan']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
    </script>
</body>

</html>