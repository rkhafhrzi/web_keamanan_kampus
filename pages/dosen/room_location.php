<?php
session_start();
require_once '../../include/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../../public/login.php');
    exit;
}

$pdo = Database::getConnection();

// Ambil input q dan tentukan tab
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_result = null;

// Logika penentuan tab: Jika ada pencarian, buka tab detail. Jika tidak, buka tab search.
$active_tab = (isset($_GET['q']) && $_GET['q'] !== '') ? 'room_detail' : 'search_room';

// Jika user mengklik tab secara manual, timpa variabel active_tab
if (isset($_GET['tab'])) {
    $active_tab = $_GET['tab'];
}

if ($search_query !== '') {
    try {
        // Membersihkan input dari spasi berlebih untuk pencarian yang lebih akurat
        $clean_query = str_replace(' ', '', $search_query);
        
        // Perbaikan: Gunakan dua nama parameter yang berbeda (:q1 dan :q2)
        $stmt_room = $pdo->prepare("SELECT * FROM rooms WHERE 
            REPLACE(name, ' ', '') LIKE :q1 OR 
            REPLACE(building, ' ', '') LIKE :q2 
            LIMIT 1");
        
        // Kirimkan datanya untuk kedua parameter tersebut
        $stmt_room->execute([
            'q1' => "%$clean_query%",
            'q2' => "%$clean_query%"
        ]);
        
        $search_result = $stmt_room->fetch();

        if ($search_result) {
            $active_tab = 'room_detail';
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lokasi Ruangan</title>

    <?php include '../../include/header.php'; ?>

    <style>
        .tab-button.active {
            border-bottom-width: 4px;
            border-color: #3b82f6;
            color: #1e40af;/
        }
    </style>
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
            <li><a href="home_dosen.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>
                    Home</a></li>
            <li><a href="apps_dosen.php" class="block hover:text-blue-500"><i class="fa-solid fa-table-cells mr-2"></i>
                    Apps</a></li>
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
            <li><a href="home_dosen.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_dosen.php" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a></li>
            <li>
                <a href="../../public/logout.php"
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
                <i class="fa-solid fa-map-marked-alt text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Pencarian Lokasi Ruangan</h2>
                <p class="text-gray-600 text-base">Temukan lokasi, fasilitas, dan detail penggunaan ruangan kampus.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl">
                <div class="flex border-b border-gray-300">

                    <button id="tab-search_room" data-tab="search_room"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none 
                        <?= $active_tab == 'search_room' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-search mr-2"></i> Cari Ruangan
                    </button>

                    <button id="tab-room_detail" data-tab="room_detail"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none
                        <?= $active_tab == 'room_detail' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-info-circle mr-2"></i> Detail Ruangan
                    </button>
                </div>

                <div class="p-6">

                    <div id="content-search_room" class="tab-content"
                        style="display: <?= $active_tab == 'search_room' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Formulir Pencarian</h3>

                        <form action="room_location.php" method="GET" class="space-y-4">
                            <label for="room_query" class="block text-lg font-medium text-gray-700 mb-1">Masukkan Nama
                                Ruangan</label>

                            <div class="flex space-x-2">
                                <input type="text" id="room_query" name="q" required
                                    value="<?= htmlspecialchars($search_query); ?>"
                                    class="flex-grow p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                                    placeholder="Contoh: Lab Komputer">

                                <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-search"></i> Cari
                                </button>
                            </div>
                        </form>

                        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200 text-sm text-gray-700">
                            <h4 class="font-bold text-blue-800 mb-2 flex items-center"><i
                                    class="fa-solid fa-lightbulb mr-2"></i> Tips Cepat</h4>
                            <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>Gunakan kode ruangan yang spesifik (misal: R-201, LAB-FIS).</li>
                                <li>Ruangan yang statusnya 'Tersedia' dapat diajukan peminjaman via menu Apps.</li>
                            </ul>
                        </div>
                    </div>

                    <div id="content-room_detail" class="tab-content"
                        style="display: <?= $active_tab == 'room_detail' ? 'block' : 'none' ?>;">

                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">
                            Detail Ruangan: <span class="text-blue-600"><?= htmlspecialchars($search_query); ?></span>
                        </h3>

                        <?php if ($search_result): ?>
                            <div class="bg-white p-6 border-l-4 border-blue-600 shadow-lg rounded-lg">
                                <h4 class="text-xl font-extrabold text-blue-950 mb-4">
                                    <?= isset($search_result['name']) ? htmlspecialchars($search_result['name']) : 'Nama Tidak Ditemukan'; ?>
                                </h4>

                                <p class="text-lg font-bold text-blue-800">
                                    <?= isset($search_result['building']) ? htmlspecialchars($search_result['building']) : 'Gedung Tidak Ditemukan'; ?>
                                </p>

                                <p class="text-sm text-gray-500 mt-4">
                                    Koordinat: <?= $search_result['latitude'] ?? '0' ?>,
                                    <?= $search_result['longitude'] ?? '0' ?>
                                </p>
                            </div>
                            <div>
                                <p class="font-semibold text-sm text-gray-500">Akses Role:</p>
                                <p class="text-lg font-bold uppercase text-gray-700">
                                    <?= htmlspecialchars($search_result['access_role'] ?? '-'); ?>
                                </p>
                            </div>
                        </div>


                    <?php else: ?>
                        <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                            <i class="fa-solid fa-face-frown text-4xl text-gray-400 mb-3"></i>
                            <p class="text-lg text-gray-700 font-semibold">Ruangan Tidak Ditemukan</p>
                            <p class="text-sm text-gray-600 mt-2">Coba gunakan kata kunci lain seperti "Lab" atau
                                "Gedung A".</p>
                        </div>
                    <?php endif; ?>

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

        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        function activateTab(targetTab) {
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-blue-950');
            });
            tabContents.forEach(content => {
                content.style.display = 'none';
            });

            const button = document.getElementById(`tab-${targetTab}`);
            const content = document.getElementById(`content-${targetTab}`);

            if (button && content) {
                button.classList.add('active');
                button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-blue-950');
                content.style.display = 'block';
            }
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.dataset.tab;
                history.pushState(null, '', `room_location.php?tab=${targetTab}&q=<?= urlencode($search_query) ?>`);
                activateTab(targetTab);
            });
        });

        activateTab('<?= $active_tab ?>');

        <?php if ($search_result && $active_tab !== 'room_detail'): ?>
            activateTab('room_detail');
        <?php endif; ?>
    </script>
</body>

</html>