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
$active_tab = $_GET['tab'] ?? 'new_report';

$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM lost_items WHERE reporter_id = :uid ORDER BY created_at DESC");
$stmt->execute(['uid' => $user['id']]);
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Keamanan</title>
    <?php include '../include/header.php'; ?>
    <style>
        .tab-button.active { border-bottom-width: 4px; border-color: #3b82f6; color: #1e40af; }
    </style>
</head>
<body class="bg-gray-50">
    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>

    <aside id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
        <div class="p-5 border-b bg-blue-900 text-white">
            <h2 class="text-xl font-bold">GeoSafe</h2>
        </div>
        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="<?= $home_path ?>" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>Home</a></li>
            <li><a href="<?= $apps_path ?>" class="block hover:text-blue-500"><i class="fa-solid fa-table-cells mr-2"></i>Apps</a></li>
            <li><a href="../public/logout.php" class="block hover:text-red-500"><i class="fa-solid fa-right-from-bracket mr-2"></i>Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl"><i class="fa-solid fa-bars"></i></button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="<?= $home_path ?>" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="<?= $apps_path ?>" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a></li>
            <li>
                <a href="../public/logout.php" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 transition duration-300 shadow-md flex items-center space-x-1">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i><span class="text-sm font-semibold">Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <main class="min-h-screen">
        <section class="w-full pt-8 pb-6 mb-8 border-b-2 border-gray-200">
            <div class="max-w-4xl mx-auto text-center px-6">
                <i class="fa-solid fa-file-invoice text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Laporan Keamanan</h2>
                <p class="text-gray-600">Ajukan laporan insiden dan lacak status laporan Anda.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl overflow-hidden">
                <div class="flex border-b border-gray-300">
                    <button data-tab="new_report" class="tab-button w-1/2 py-3 font-semibold <?= $active_tab === 'new_report' ? 'active' : '' ?>">
                        <i class="fa-solid fa-flag mr-2"></i> Buat Laporan Baru
                    </button>
                    <button data-tab="tracking" class="tab-button w-1/2 py-3 font-semibold <?= $active_tab === 'tracking' ? 'active' : '' ?>">
                        <i class="fa-solid fa-map-pin mr-2"></i> Status Laporan
                    </button>
                </div>

                <div class="p-6">
                    <div id="content-new_report" class="tab-content" style="<?= $active_tab === 'new_report' ? 'block' : 'display:none' ?>">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Formulir Pengajuan Laporan</h3>
                        <form action="../public/lost_item.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center"><i class="fa-solid fa-location-dot mr-2"></i> Detail Insiden</h4>
                                <label class="block text-sm font-medium">Nama Barang</label>
                                <input type="text" name="judul" required class="w-full p-2 border rounded-lg mb-3">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium">Lokasi</label>
                                        <input type="text" name="location" required class="w-full p-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Tanggal</label>
                                        <input type="date" name="lost_date" required class="w-full p-2 border rounded-lg">
                                    </div>
                                </div>
                                <label class="block text-sm font-medium">Jenis Laporan</label>
                                <select name="status" class="w-full p-2 border rounded-lg mb-3">
                                    <option value="hilang">Kehilangan Barang</option>
                                    <option value="ditemukan">Menemukan Barang</option>
                                </select>
                                <label class="block text-sm font-medium">Deskripsi</label>
                                <textarea name="deskripsi" rows="4" required class="w-full p-2 border rounded-lg"></textarea>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center"><i class="fa-solid fa-camera mr-2"></i> Foto Bukti</h4>
                                <input type="file" name="bukti" accept="image/*" class="w-full p-2 border rounded-lg bg-white">
                            </div>
                            <div class="text-right"><button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-bold">Ajukan Laporan</button></div>
                        </form>
                    </div>

                    <div id="content-tracking" class="tab-content" style="<?= $active_tab === 'tracking' ? 'block' : 'display:none' ?>">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Status Laporan Anda</h3>
                        <?php if (empty($reports)): ?>
                            <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed">
                                <i class="fa-solid fa-inbox text-4xl text-gray-500 mb-3"></i>
                                <p class="font-semibold">Belum Ada Laporan</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($reports as $r): ?>
                                    <div class="p-4 border-l-4 rounded shadow bg-white <?= $r['status'] === 'hilang' ? 'border-red-600' : 'border-green-600' ?>">
                                        <div class="font-bold text-blue-900 text-lg"><?= htmlspecialchars($r['item_name']) ?></div>
                                        <div class="text-xs text-gray-500 mb-2 flex items-center gap-2">
                                            <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($r['location']) ?></span>
                                            <span>|</span>
                                            <span><i class="fa-solid fa-calendar"></i> <?= htmlspecialchars($r['lost_date']) ?></span>
                                            <span class="ml-auto px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $r['status'] === 'hilang' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                                <?= htmlspecialchars($r['status']) ?>
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-700 leading-relaxed"><?= htmlspecialchars($r['description']) ?></div>
                                        <?php if (!empty($r['evidence_image'])): ?>
                                            <div class="mt-3">
                                                <img src="../uploads/lost_items/<?= htmlspecialchars($r['evidence_image']) ?>" class="w-40 h-auto rounded-lg shadow-sm border border-gray-200">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div> </div> </div> </div> </main>

    <footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
        <p class="text-sm">Contact: support@GeoSafe.com</p>
        <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe</p>
    </footer>

    <script>
        const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.style.display = 'none');
                tab.classList.add('active');
                document.getElementById(`content-${target}`).style.display = 'block';
            });
        });

        const sidebar = document.getElementById("sidebar");
        const menuBtn = document.getElementById("menuBtn");
        const overlay = document.getElementById("overlay");
        function toggleSidebar() {
            sidebar.classList.toggle("-translate-x-full");
            overlay.classList.toggle("hidden");
            setTimeout(() => overlay.classList.toggle("opacity-0"), 10);
        }
        menuBtn.addEventListener("click", toggleSidebar);
        overlay.addEventListener("click", toggleSidebar);
    </script>
</body>
</html>