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
// Petugas diganti dengan Tamu
$roles = [
    'Mahasiswa' => '%@mhs.ubpkarawang.ac.id',
    'Dosen' => '%@ubpkarawang.ac.id'
];

$stats = [];

// 1a. Ambil data untuk Mahasiswa dan Dosen
foreach ($roles as $label => $domain) {
    $stmt = $db->prepare("
        SELECT
            SUM(CASE WHEN al.status = 'masuk' THEN 1 ELSE 0 END) AS masuk,
            SUM(CASE WHEN al.status = 'keluar' THEN 1 ELSE 0 END) AS keluar
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

// 1b. Ambil data khusus untuk Tamu (dari tabel guests)
$stmtTamu = $db->query("
    SELECT 
        COUNT(check_in) AS masuk, 
        COUNT(check_out) AS keluar 
    FROM guests
");
$rowTamu = $stmtTamu->fetch(PDO::FETCH_ASSOC);

$stats['Tamu'] = [
    'masuk' => (int) ($rowTamu['masuk'] ?? 0),
    'keluar' => (int) ($rowTamu['keluar'] ?? 0),
];

// Menyiapkan variabel untuk Chart.js (Nama variabel tetap sama)
$labels = array_keys($stats); // Sekarang isinya ['Mahasiswa', 'Dosen', 'Tamu']
$dataMasuk = array_values(array_column($stats, 'masuk'));
$dataKeluar = array_values(array_column($stats, 'keluar'));

$dataAktif = array_values(array_map(
    fn($v) => max(0, $v['masuk'] - $v['keluar']),
    $stats
));

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

/**
 * ============================
 * 3. STATISTIK BARANG (LOST & FOUND)
 * ============================
 */
$stmtBarang = $db->query("
    SELECT 
        lost_date,
        SUM(CASE WHEN status = 'hilang' THEN 1 ELSE 0 END) AS total_hilang,
        SUM(CASE WHEN status = 'ditemukan' THEN 1 ELSE 0 END) AS total_ditemukan
    FROM lost_items 
    GROUP BY lost_date
    ORDER BY lost_date ASC
    LIMIT 10
");

$dataBarang = $stmtBarang->fetchAll(PDO::FETCH_ASSOC);

// Menyiapkan data untuk dikirim ke atribut data- HTML
$labelsBarang = array_column($dataBarang, 'lost_date');
$dataHilang = array_map('intval', array_column($dataBarang, 'total_hilang'));
$dataDitemukan = array_map('intval', array_column($dataBarang, 'total_ditemukan'));
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

        <section class="w-full bg-gray-50 pt-8 pb-6 mb-8 border-b-2 border-gray-200">
            <div class="max-w-4xl mx-auto text-center px-6">
                <i class="fa-solid fa-file-alt text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950">Laporan Harian</h2>
                <p class="text-gray-600">Rekap kumulatif aktivitas keamanan civitas kampus.</p>
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
                    <canvas id="doughnutChartActive" data-title="Tren Orang Aktif Saat Ini"
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

            <div class="bg-white p-5 rounded-xl shadow-sm">
                <div style="height:250px">
                    <canvas id="lineChartBarang" data-title="Tren Laporan Barang Hilang & Ditemukan"
                        data-labels='["2025-12-08","2025-12-09","2025-12-10","2025-12-23","2026-01-03"]'
                        data-hilang='[0,0,1,2,1]' data-ditemukan='[0,1,0,0,0]'>
                    </canvas>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex justify-between items-start">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                            <i class="fa-solid fa-file-signature text-2xl"></i>
                        </div>
                        <span
                            class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest">
                            Internal Petugas
                        </span>
                    </div>

                    <h3 class="text-xl font-extrabold text-amber-950 mt-5">Manajemen Laporan</h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Kelola arsip keamanan, input data aktivitas harian, dan edit laporan periode gerbang atau akses
                        ruangan.
                    </p>
                </div>

                <div class="mt-6">
                    <a href="form_reports.php"
                        class="group flex items-center justify-between bg-amber-700 text-white px-5 py-3 rounded-lg font-bold hover:bg-amber-500 transition-all shadow-sm">
                        <span>Buka Formulir Laporan</span>
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
        <p class="text-sm">Contact: support@GeoSafe.com</p>
        <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe. All rights reserved.</p>
    </footer>

    <script src="../../js/report_charts.js?v=<?= time(); ?>"></script>

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