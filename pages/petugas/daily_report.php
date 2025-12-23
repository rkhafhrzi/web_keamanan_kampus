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
    'Dosen'     => '%@ubpkarawang.ac.id',
    'Petugas'   => '%@staff.ubpkarawang.ac.id'
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
        'masuk'  => (int) ($row['masuk'] ?? 0),
        'keluar' => (int) ($row['keluar'] ?? 0),
    ];
}

$labels     = array_keys($stats);
$dataMasuk  = array_column($stats, 'masuk');
$dataKeluar = array_column($stats, 'keluar');
$dataAktif  = array_map(
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
    'Dosen'     => 0,
    'Lainnya'   => 0
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
            <canvas id="barChartStats"
                data-title="Arus Masuk & Keluar"
                data-labels='<?= json_encode($labels) ?>'
                data-masuk='<?= json_encode($dataMasuk) ?>'
                data-keluar='<?= json_encode($dataKeluar) ?>'>
            </canvas>
        </div>
    </div>

    <!-- DOUGHNUT: AKTIF -->
    <div class="bg-white p-5 rounded-xl shadow-sm">
        <div style="height:250px">
            <canvas id="doughnutChartActive"
                data-title="Jumlah Orang Aktif"
                data-labels='<?= json_encode($labels) ?>'
                data-aktif='<?= json_encode($dataAktif) ?>'>
            </canvas>
        </div>
    </div>

    <!-- PIE: KENDARAAN -->
    <div class="bg-white p-5 rounded-xl shadow-sm">
        <div style="height:250px">
            <canvas id="pieChartVehicle"
                data-title="Kendaraan Terdaftar"
                data-labels='<?= json_encode(array_keys($vStats)) ?>'
                data-values='<?= json_encode(array_values($vStats)) ?>'>
            </canvas>
        </div>
    </div>

    <!-- PIE: CIVITAS -->
    <div class="bg-white p-5 rounded-xl shadow-sm">
        <div style="height:250px">
            <canvas id="pieChartCivitas"
                data-title="Mahasiswa vs Dosen"
                data-labels='<?= json_encode(["Mahasiswa","Dosen"]) ?>'
                data-values='<?= json_encode([$totalMhs, $totalDsn]) ?>'>
            </canvas>
        </div>
    </div>

</div>
</main>

<script src="../../js/report_charts.js"></script>

</body>
</html>