<?php
session_start();
require_once '../../include/connection.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../../public/login.php');
    exit;
}

$db = Database::getConnection();

/**
 * ============================
 * 1. STATISTIK AKSES (KUMULATIF)
 * ============================
 */
$roles = [
    'Mahasiswa' => '%@mhs.ubpkarawang.ac.id',
    'Dosen' => '%@ubpkarawang.ac.id',
    'Petugas' => '%@staff.ubpkarawang.ac.id'
];

$stats = [];

foreach ($roles as $label => $domain) {
    $stmt = $db->prepare("
        SELECT
            SUM(al.status = 'masuk')  AS masuk,
            SUM(al.status = 'keluar') AS keluar
        FROM access_logs al
        JOIN users u ON u.id = al.user_id
        WHERE u.email LIKE :domain
    ");
    $stmt->execute(['domain' => $domain]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $stats[$label] = [
        'masuk' => (int) ($row['masuk'] ?? 0),
        'keluar' => (int) ($row['keluar'] ?? 0),
    ];
}

$labels = array_keys($stats);
$dataMasuk = array_column($stats, 'masuk');
$dataKeluar = array_column($stats, 'keluar');
$dataAktif = array_map(
    fn($v) => max(0, $v['masuk'] - $v['keluar']),
    $stats
);

/**
 * ============================
 * 2. STATISTIK KENDARAAN
 * ============================
 */
$vStats = [
    'Mahasiswa' => 0,
    'Dosen' => 0,
    'Lainnya' => 0
];

$stmt = $db->query("
    SELECT u.email
    FROM vehicles v
    JOIN users u ON u.id = v.user_id
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (str_contains($row['email'], '@mhs.ubpkarawang.ac.id')) {
        $vStats['Mahasiswa']++;
    } elseif (str_contains($row['email'], '@ubpkarawang.ac.id')) {
        $vStats['Dosen']++;
    } else {
        $vStats['Lainnya']++;
    }
}

$totalMhs = $vStats['Mahasiswa'];
$totalDsn = $vStats['Dosen'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian</title>
    <?php include '../../include/header.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>

<body class="bg-gray-50">

    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden z-40"></div>

    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform z-50">
        <div class="p-5 border-b bg-blue-950 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>
        <ul class="p-5 space-y-5 text-blue-950 font-medium">
            <li><a href="home_petugas.php"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
            <li><a href="validation_qr.php" class="font-bold text-blue-600">
                    <i class="fa-solid fa-qrcode mr-2"></i> Scan QR</a></li>
            <li><a href="../../public/logout.php" class="text-red-600">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <!-- ===== NAVBAR ===== -->
    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-950 text-xl" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-2xl font-bold text-blue-950 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_petugas.php"
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

    <main class="min-h-screen pb-12 bg-gray-50">

        <section class="w-full pt-10 pb-4 mb-6">
            <div class="max-w-6xl mx-auto px-6 flex items-center gap-5">
                <i class="fa-solid fa-shield-halved text-5xl text-blue-900"></i>
                <div>
                    <h2 class="text-3xl font-bold text-blue-950">Laporan Harian</h2>
                    <p class="text-gray-500">
                        Rekap kumulatif aktivitas keamanan civitas kampus
                    </p>
                </div>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- BAR: MASUK / KELUAR -->
            <div class="bg-white p-5 rounded-xl shadow-sm">
                <div style="height:250px">
                    <canvas id="barChartStats" data-title="Arus Masuk & Keluar"
                        data-labels='<?= json_encode($labels) ?>' data-masuk='<?= json_encode($dataMasuk) ?>'
                        data-keluar='<?= json_encode($dataKeluar) ?>'>
                    </canvas>
                </div>
            </div>

            <!-- DOUGHNUT: AKTIF -->
            <div class="bg-white p-5 rounded-xl shadow-sm">
                <div style="height:250px">
                    <canvas id="doughnutChartActive" data-title="Jumlah Orang Aktif"
                        data-labels='<?= json_encode($labels) ?>' data-aktif='<?= json_encode($dataAktif) ?>'>
                    </canvas>
                </div>
            </div>

            <!-- PIE: KENDARAAN -->
            <div class="bg-white p-5 rounded-xl shadow-sm">
                <div style="height:250px">
                    <canvas id="pieChartVehicle" data-title="Kendaraan Terdaftar"
                        data-labels='<?= json_encode(array_keys($vStats)) ?>'
                        data-values='<?= json_encode(array_values($vStats)) ?>'>
                    </canvas>
                </div>
            </div>

            <!-- PIE: CIVITAS -->
            <div class="bg-white p-5 rounded-xl shadow-sm">
                <div style="height:250px">
                    <canvas id="pieChartCivitas" data-title="Mahasiswa vs Dosen"
                        data-labels='<?= json_encode(["Mahasiswa", "Dosen"]) ?>'
                        data-values='<?= json_encode([$totalMhs, $totalDsn]) ?>'>
                    </canvas>
                </div>
            </div>

        </div>
    </main>

    <script src="../../js/report_charts.js"></script>

</body>

</html>