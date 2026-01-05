<?php
session_start();
require_once '../../include/connection.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../../public/login.php');
    exit;
}

$pdo = Database::getConnection();

try {
    // 2. Query Log Aktivitas (Dosen, Mahasiswa, Tamu)
    $sql_preview = "
    SELECT * FROM (
        SELECT u.nama_lengkap AS nama,
            CASE 
                WHEN u.email LIKE '%@mhs.ubpkarawang.ac.id' THEN 'Mahasiswa'
                WHEN u.email LIKE '%@ubpkarawang.ac.id' THEN 'Dosen'
                ELSE 'Staff'
            END AS role,
            al.access_time AS waktu, al.status AS aktivitas, 'civitas' AS tipe_log
        FROM access_logs al
        JOIN users u ON al.user_id = u.id
        UNION ALL
        SELECT name AS nama, 'Tamu' AS role, check_in AS waktu, 'masuk' AS aktivitas, 'tamu' AS tipe_log
        FROM guests WHERE check_in IS NOT NULL
        UNION ALL
        SELECT name AS nama, 'Tamu' AS role, check_out AS waktu, 'keluar' AS aktivitas, 'tamu' AS tipe_log
        FROM guests WHERE check_out IS NOT NULL
    ) AS preview_gabungan ORDER BY waktu DESC LIMIT 4";

    $home_logs = $pdo->query($sql_preview)->fetchAll(PDO::FETCH_ASSOC);

    // 3. Query Peringatan Keamanan Aktif (Kotak Merah)
    $sql_alerts = "SELECT * FROM lost_items WHERE status = 'hilang' ORDER BY created_at DESC LIMIT 2";
    $active_alerts = $pdo->query($sql_alerts)->fetchAll(PDO::FETCH_ASSOC);

    // Hitung jumlah laporan baru khusus hari ini (untuk badge NEW)
    $sql_count = "SELECT COUNT(*) FROM lost_items WHERE status = 'hilang' AND DATE(created_at) = CURDATE()";
    $total_new = $pdo->query($sql_count)->fetchColumn();

    // 4. Query Laporan & Rekap Keamanan (Tabel reports & lost_items)
    $sql_combined_reports = "
    SELECT * FROM (
        SELECT description AS judul, report_type AS kategori, generated_at AS waktu, 'laporan' AS tipe_data, 'keamanan' AS status_label
        FROM reports
        UNION ALL
        SELECT item_name AS judul, location AS kategori, created_at AS waktu, 'kehilangan' AS tipe_data, status AS status_label
        FROM lost_items
    ) AS gabungan_keamanan ORDER BY waktu DESC LIMIT 5";

    $all_security_data = $pdo->query($sql_combined_reports)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Untuk debugging jika ada error: die($e->getMessage());
    $home_logs = [];
    $active_alerts = [];
    $all_security_data = [];
    $total_new = 0;
}

$user_data = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Petugas</title>

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
            <li><a href="home_petugas.php" class="block text-blue-950 font-bold border-l-4 border-blue-600 pl-1">
                    <i class="fa-solid fa-house mr-2"></i> Home</a></li>

            <li class="relative group">
                <a href="apps_petugas.php" class="block hover:text-blue-500">
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
            <li><a href="home_petugas.php"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Home</a>
            </li>
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

    <main class="min-h-screen bg-gray-50 pt-10 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <section class="mb-10 p-6 bg-white shadow-lg rounded-xl border-l-4 border-blue-600">
                <h2 class="text-3xl font-bold text-blue-950 mb-2">Selamat Bertugas,
                    <?= htmlspecialchars($user_data['nama']); ?>!
                </h2>
                <p class="text-gray-600">Dasbor pusat kontrol keamanan dan administrasi kampus.</p>
            </section>

            <div class="grid grid-cols-1 cg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-50">
                            <h3 class="text-lg font-bold text-blue-950 flex items-center">
                                <i class="fa-solid fa-shield-halved mr-2 text-indigo-500 text-base"></i>
                                Verifikasi & Akses
                            </h3>
                            <span
                                class="text-[10px] bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-bold uppercase tracking-wider">Petugas
                                Mode</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <a href="validation_qr.php"
                                class="group flex items-center p-4 bg-gray-50 hover:bg-green-50 rounded-xl transition-all duration-200 border border-gray-100 hover:border-green-200 shadow-sm">
                                <div
                                    class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-qrcode text-2xl text-green-600"></i>
                                </div>
                                <div class="ml-4 text-left">
                                    <p class="text-sm font-bold text-gray-800">Validasi QR</p>
                                    <p class="text-[11px] text-gray-500 leading-tight">Cek status masuk/keluar</p>
                                </div>
                            </a>

                            <a href="check_vehicle.php"
                                class="group flex items-center p-4 bg-gray-50 hover:bg-indigo-50 rounded-xl transition-all duration-200 border border-gray-100 hover:border-indigo-200 shadow-sm">
                                <div
                                    class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-check-to-slot text-2xl text-indigo-600"></i>
                                </div>
                                <div class="ml-4 text-left">
                                    <p class="text-sm font-bold text-gray-800">Cek Kendaraan</p>
                                    <p class="text-[11px] text-gray-500 leading-tight">Verifikasi izin parkir</p>
                                </div>
                            </a>

                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                </div>
            </div>

            <div class="bg-white p-5 shadow-sm rounded-2xl border border-gray-100">
                <h3 class="text-base font-bold text-blue-950 mb-4 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-orange-500"></i> Log Aktivitas
                </h3>

                <div class="space-y-2">
                    <?php if (empty($home_logs)): ?>
                        <p class="text-[10px] text-gray-400 text-center py-4 italic">Belum ada aktivitas hari ini.</p>
                    <?php else: ?>
                        <?php foreach ($home_logs as $log):
                            // Logika Icon dan Warna berdasarkan aktivitas
                            $is_masuk = ($log['aktivitas'] === 'masuk');
                            $bg_color = $is_masuk ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600';
                            $icon = $is_masuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket';

                            // Jika tamu, kita bisa ubah warnanya sedikit agar beda
                            if ($log['tipe_log'] === 'tamu') {
                                $bg_color = $is_masuk ? 'bg-indigo-100 text-indigo-600' : 'bg-purple-100 text-purple-600';
                            }
                            ?>
                            <div
                                class="flex items-center justify-between p-2.5 hover:bg-gray-50 rounded-xl border border-transparent hover:border-gray-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 <?= $bg_color ?> rounded-full flex items-center justify-center text-xs">
                                        <i class="fa-solid <?= $icon ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">
                                            <?= htmlspecialchars($log['nama']) ?> (
                                            <?= $log['role'] ?>)
                                        </p>
                                        <p class="text-[10px] text-gray-500 uppercase">
                                            <?= $log['aktivitas'] ?> •
                                            <?= $log['tipe_log'] === 'tamu' ? 'Lobby' : 'Area Gedung' ?>
                                        </p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-medium text-gray-400">
                                    <?= date('H:i', strtotime($log['waktu'])) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <a href="log_history.php"
                    class="mt-4 block w-full text-center py-2 text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-lg transition">
                    Lihat Semua Riwayat
                </a>
            </div>
            <br><br>
            <div
                class="bg-white p-5 shadow-sm rounded-2xl border border-red-100 mb-6 bg-gradient-to-r from-white to-red-50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-red-800 flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-2 text-red-500 animate-pulse"></i>
                        Peringatan Keamanan Hari Ini
                    </h3>
                    <?php if ($total_new > 0): ?>
                        <span class="px-2 py-0.5 bg-red-500 text-white text-[10px] font-black rounded-full animate-bounce">
                            <?= $total_new ?> NEW
                        </span>
                    <?php endif; ?>
                </div>

                <div class="space-y-3">
                    <?php if (empty($active_alerts)): ?>
                        <p class="text-[10px] text-gray-400 text-center py-4 italic">Tidak ada peringatan keamanan aktif.
                        </p>
                    <?php else: ?>
                        <?php foreach ($active_alerts as $alert):
                            // Logika warna border berdasarkan urgensi (contoh: jika ada kata 'Kunci' atau 'HP' dianggap lebih urgen)
                            $is_urgent = stripos($alert['item_name'], 'HP') !== false || stripos($alert['item_name'], 'Dompet') !== false;
                            $border_color = $is_urgent ? 'border-red-200' : 'border-amber-200';
                            $icon_color = $is_urgent ? 'text-red-500' : 'text-amber-500';
                            $icon = $is_urgent ? 'fa-circle-exclamation' : 'fa-box-archive';
                            ?>
                            <div
                                class="flex items-start gap-3 p-3 bg-white/80 rounded-xl border <?= $border_color ?> shadow-sm">
                                <div class="mt-1">
                                    <i class="fa-solid <?= $icon ?> <?= $icon_color ?> text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <p class="text-xs font-bold text-gray-800">Laporan Kehilangan:
                                            <?= htmlspecialchars($alert['item_name']) ?>
                                        </p>
                                        <span class="text-[9px] text-gray-400 font-medium">
                                            <?= date('H:i', strtotime($alert['created_at'])) ?> WIB
                                        </span>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-0.5">
                                        Dilaporkan di
                                        <?= htmlspecialchars($alert['location'] ?: 'Area Kampus') ?>. Mohon pantau area sekitar.
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <a href="form_reports.php"
                        class="py-2 text-[11px] font-bold text-center text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-sm">
                        Lapor Sekarang
                    </a>
                    <a href="../security_information.php"
                        class="py-2 text-[11px] font-bold text-center text-red-600 border border-red-200 hover:bg-red-100 rounded-lg transition">
                        Lihat Semua
                    </a>
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