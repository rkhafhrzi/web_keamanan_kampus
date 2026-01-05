<?php
session_start();

require_once '../include/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../../public/login.php');
    exit;
}

$user = $_SESSION['user'];
$user_role = $user['role'] ?? 'default';
$role_slug = strtolower($user_role);

$home_path = "{$role_slug}/home_{$role_slug}.php";
$apps_path = "{$role_slug}/apps_{$role_slug}.php";

/**
 * GOOGLE MAPS EMBED (HARDCODE - STABLE)
 */
$GOOGLE_MAPS_EMBED_URL = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.542837501017!2d107.29873607499114!3d-6.323615493665882!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69762d4c316603%3A0x50a8005dfd52a897!2sUniversitas%20Buana%20Perjuangan%20Karawang!5e0!3m2!1sid!2sid!4v1766493844431!5m2!1sid!2sid";

$active_tab = $_GET['tab'] ?? 'lost_and_found';

/**
 * DATA BARANG HILANG
 */
$db = Database::getConnection();
$stmt = $db->prepare("
    SELECT 
        li.*,
        u.email AS reporter_email
    FROM lost_items li
    JOIN users u ON u.id = li.reporter_id
    ORDER BY li.created_at DESC
");
$stmt->execute();
$lostItems = $stmt->fetchAll();

/**
 * DATA PERINGATAN DARI TABEL REPORTS
 */
$stmtAlerts = $db->prepare("
    SELECT 
        report_type AS tipe,
        description AS detail,
        DATE_FORMAT(generated_at, '%d %b %Y') AS tanggal,
        period_start,
        period_end
    FROM reports 
    ORDER BY generated_at DESC 
    LIMIT 10
");
$stmtAlerts->execute();
$alertsFromDb = $stmtAlerts->fetchAll();

/**
 * Fungsi Mapping Warna & Ikon berdasarkan report_type
 */
function getReportStyle($type)
{
    switch ($type) {
        case 'keamanan_harian':
            return [
                'judul' => 'Laporan Keamanan',
                'warna' => 'bg-red-50 text-red-800 border-red-400',
                'icon' => 'fa-solid fa-shield-halved'
            ];
        case 'akses_ruangan':
            return [
                'judul' => 'Akses Ruangan',
                'warna' => 'bg-yellow-50 text-yellow-800 border-yellow-400',
                'icon' => 'fa-solid fa-door-open'
            ];
        case 'gerbang':
            return [
                'judul' => 'Lalu Lintas Gerbang',
                'warna' => 'bg-blue-50 text-blue-800 border-blue-400',
                'icon' => 'fa-solid fa-id-card-clip'
            ];
        default:
            return [
                'judul' => 'Pemberitahuan',
                'warna' => 'bg-gray-50 text-gray-800 border-gray-400',
                'icon' => 'fa-solid fa-circle-info'
            ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Keamanan</title>

    <?php include '../include/header.php'; ?>

    <style>
        .tab-button.active {
            border-bottom-width: 4px;
            border-color: #10b981;
            color: #065f46;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden z-40"></div>

    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

        <div class="p-5 border-b bg-blue-900 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>

        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="<?= $home_path ?>" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>
                    Home</a></li>
            <li><a href="<?= $apps_path ?>" class="block hover:text-blue-500"><i
                        class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../public/logout.php" class="block hover:text-red-500"><i
                        class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="<?= $home_path ?>" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="<?= $apps_path ?>"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a>
            </li>
            <li>
                <a href="../public/logout.php"
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
                <i class="fa-solid fa-shield-halved text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950">Informasi Keamanan</h2>
                <p class="text-gray-600">Monitoring laporan, peringatan, dan peta keamanan kampus</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl">

                <!-- TAB -->
                <div class="flex border-b">
                    <button data-tab="lost_and_found"
                        class="tab-button w-1/3 py-3 font-semibold <?= $active_tab === 'lost_and_found' ? 'active' : '' ?>">
                        <i class="fa-solid fa-box-open mr-2"></i> Barang
                    </button>
                    <button data-tab="alerts"
                        class="tab-button w-1/3 py-3 font-semibold <?= $active_tab === 'alerts' ? 'active' : '' ?>">
                        <i class="fa-solid fa-bell mr-2"></i> Peringatan
                    </button>
                    <button data-tab="map"
                        class="tab-button w-1/3 py-3 font-semibold <?= $active_tab === 'map' ? 'active' : '' ?>">
                        <i class="fa-solid fa-map-location-dot mr-2"></i> Peta
                    </button>
                </div>

                <div class="p-6">

                    <!-- BARANG -->
                    <div id="content-lost_and_found" class="tab-content"
                        style="<?= $active_tab === 'lost_and_found' ? '' : 'display:none' ?>">
                        <?php if (empty($lostItems)): ?>
                            <div class="text-center py-10 bg-gray-100 rounded-lg border border-dashed">
                                <p class="text-gray-700 font-semibold">Belum ada laporan barang.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($lostItems as $item): ?>
                                    <div
                                        class="p-4 rounded-xl border-l-4 shadow bg-white <?= $item['status'] === 'hilang' ? 'border-red-600' : 'border-green-600' ?>">
                                        <div class="font-bold text-lg text-blue-900"><?= htmlspecialchars($item['item_name']) ?>
                                        </div>

                                        <p class="text-sm text-gray-600">
                                            <i class="fa-solid fa-location-dot mr-1"></i> Lokasi:
                                            <?= htmlspecialchars($item['location']) ?>
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            <i class="fa-solid fa-user mr-1"></i> Pelapor:
                                            <?= htmlspecialchars($item['reporter_email']) ?>
                                        </p>

                                        <p class="text-sm mt-2 text-gray-700 leading-relaxed">
                                            <?= htmlspecialchars($item['description']) ?>
                                        </p>

                                        <?php if (!empty($item['evidence_image'])): ?>
                                            <div class="mt-3">
                                                <img src="../uploads/lost_items/<?= htmlspecialchars($item['evidence_image']) ?>"
                                                    class="w-40 h-auto rounded-lg shadow-sm border border-gray-200 object-cover hover:scale-105 transition-transform"
                                                    alt="Foto Barang" onerror="this.style.display='none'">
                                                <p class="text-[10px] text-gray-400 mt-1 italic">*Bukti foto terlampir</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ALERT -->
                    <div id="content-alerts" class="tab-content"
                        style="<?= $active_tab === 'alerts' ? '' : 'display:none' ?>">

                        <?php if (empty($alertsFromDb)): ?>
                            <div class="text-center py-10 bg-gray-100 rounded-lg border border-dashed">
                                <p class="text-gray-700 font-semibold">Tidak ada laporan peringatan saat ini.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($alertsFromDb as $a):
                                    $style = getReportStyle($a['tipe']);
                                    ?>
                                    <div class="p-4 rounded-xl border-l-4 shadow-sm <?= $style['warna'] ?> border">
                                        <div class="flex justify-between items-start mb-1">
                                            <div class="font-bold text-sm uppercase tracking-wide">
                                                <i class="<?= $style['icon'] ?> mr-2"></i><?= $style['judul'] ?>
                                            </div>
                                            <span class="text-[10px] font-semibold opacity-70">
                                                <?= $a['tanggal'] ?>
                                            </span>
                                        </div>

                                        <p class="text-sm leading-relaxed mb-2">
                                            <?= htmlspecialchars($a['detail']) ?>
                                        </p>

                                        <?php if ($a['period_start'] && $a['period_end']): ?>
                                            <div class="text-[10px] mt-2 pt-2 border-t border-black/10 flex items-center gap-2">
                                                <i class="fa-regular fa-calendar"></i>
                                                <span>Periode: <?= date('d/m/Y', strtotime($a['period_start'])) ?> -
                                                    <?= date('d/m/Y', strtotime($a['period_end'])) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- MAP -->
                    <div id="content-map" class="tab-content"
                        style="<?= $active_tab === 'map' ? '' : 'display:none' ?>">
                        <h3 class="text-2xl font-bold text-blue-900 mb-4">Peta Keamanan Kampus</h3>
                        <div id="mapContainer"
                            class="rounded-xl overflow-hidden border shadow h-[450px] bg-gray-100 flex items-center justify-center">
                            <span class="text-gray-500">Memuat peta...</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>

<footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
    <p class="text-sm">Contact: support@GeoSafe.com</p>
    <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe</p>
</footer>

<script>
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');
    const mapContainer = document.getElementById('mapContainer');
    const mapUrl = <?= json_encode($GOOGLE_MAPS_EMBED_URL) ?>;
    let mapLoaded = false;

    function loadMap() {
        if (mapLoaded) return;

        mapContainer.innerHTML = `
        <iframe
            src="${mapUrl}"
            class="w-full h-full"
            style="border:0"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    `;
        mapLoaded = true;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.style.display = 'none');

            tab.classList.add('active');
            document.getElementById(`content-${target}`).style.display = 'block';

            if (target === 'map') loadMap();
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        if ("<?= $active_tab ?>" === "map") loadMap();
    });
</script>

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

</html>