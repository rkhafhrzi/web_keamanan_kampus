<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../../public/login.php');
    exit;
}

$data_log_universal = [
    ['role' => 'Dosen', 'waktu_masuk' => '2025-12-16 08:00:12', 'status' => 'Selesai'],
    ['role' => 'Dosen', 'waktu_masuk' => '2025-12-16 09:30:00', 'status' => 'Aktif'],
    ['role' => 'Mahasiswa', 'waktu_masuk' => '2025-12-16 13:00:00', 'status' => 'Selesai'],
    ['role' => 'Mahasiswa', 'waktu_masuk' => '2025-12-16 14:10:00', 'status' => 'Aktif'],
    ['role' => 'Tamu', 'waktu_masuk' => '2025-12-16 14:00:00', 'status' => 'Aktif'],
];

$registered_vehicles = [
    ['plat' => 'B 1234 XYZ', 'pemilik' => '210987654 (Mhs)'],
    ['plat' => 'D 5678 ABC', 'pemilik' => 'DSN1001 (Dosen)'],
];

$stats = ['Dosen' => ['masuk' => 0, 'keluar' => 0], 'Mahasiswa' => ['masuk' => 0, 'keluar' => 0], 'Tamu' => ['masuk' => 0, 'keluar' => 0]];
foreach ($data_log_universal as $log) {
    $role = $log['role'];
    if (isset($stats[$role])) {
        $stats[$role]['masuk']++;
        if ($log['status'] === 'Selesai')
            $stats[$role]['keluar']++;
    }
}

$labels = array_keys($stats);
$dataMasuk = [];
$dataKeluar = [];
$dataAktif = [];
foreach ($stats as $val) {
    $dataMasuk[] = $val['masuk'];
    $dataKeluar[] = $val['keluar'];
    $dataAktif[] = $val['masuk'] - $val['keluar'];
}

$vStats = ['Mahasiswa' => 0, 'Dosen' => 0, 'Lainnya' => 0];
foreach ($registered_vehicles as $v) {
    if (strpos($v['pemilik'], '(Mahasiswa)') !== false)
        $vStats['Mahasiswa']++;
    elseif (strpos($v['pemilik'], '(Dosen)') !== false)
        $vStats['Dosen']++;
    else
        $vStats['Lainnya']++;
}

$totalMhs = $vStats['Mahasiswa']; 
$totalDsn = $vStats['Dosen'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian</title>
    <?php include '../../include/header.php'; ?>
</head>

<body class="bg-gray-50">
    <div id="overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40">
    </div>

    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

        <div class="p-5 border-b bg-blue-900 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>

        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="home_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>
                    Home</a></li>
            <li><a href="apps_petugas.php" class="block hover:text-blue-500"><i
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
            <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
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

    <main class="min-h-screen pb-12 bg-gray-50">
        <section class="w-full bg-transparent pt-10 pb-4 mb-6">
            <div class="max-w-6xl mx-auto px-6 flex items-center gap-5">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-5xl text-blue-900"></i>
                </div>
                <div class="text-left">
                    <h2 class="text-3xl font-bold text-blue-950 tracking-tight">Laporan Harian</h2>
                    <p class="text-gray-500 text-base">Monitoring statistik aktivitas civitas dan aset kendaraan secara
                        real-time.</p>
                </div>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white p-5 shadow-sm rounded-xl border border-gray-100">
                <div style="height: 250px;">
                    <canvas id="barChartStats" data-title="Arus Masuk & Keluar"
                        data-labels='<?= json_encode($labels); ?>' data-masuk='<?= json_encode($dataMasuk); ?>'
                        data-keluar='<?= json_encode($dataKeluar); ?>'></canvas>
                </div>
            </div>

            <div class="bg-white p-5 shadow-sm rounded-xl border border-gray-100">
                <div style="height: 250px;">
                    <canvas id="doughnutChartActive" data-title="Perbandingan Aktif"
                        data-labels='<?= json_encode($labels); ?>'
                        data-aktif='<?= json_encode($dataAktif); ?>'></canvas>
                </div>
            </div>

            <div class="bg-white p-5 shadow-sm rounded-xl border border-gray-100">
                <div style="height: 250px;">
                    <canvas id="pieChartVehicle" data-title="Pemilik Kendaraan Terdaftar"
                        data-labels='<?= json_encode(array_keys($vStats)); ?>'
                        data-values='<?= json_encode(array_values($vStats)); ?>'></canvas>
                </div>
            </div>

            <div class="bg-white p-5 shadow-sm rounded-xl border border-gray-100">
                <div style="height: 250px;">
                    <canvas id="pieChartCivitas" data-title="Perbandingan Mahasiswa vs Dosen"
                        data-labels='<?= json_encode(["Mahasiswa", "Dosen"]); ?>'
                        data-values='<?= json_encode([$totalMhs, $totalDsn]); ?>'></canvas>
                </div>
            </div>

        </div>
    </main>

    <script src="../../js/report_charts.js"></script>

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