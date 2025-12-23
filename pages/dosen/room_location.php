<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$room_details = [
    'R-305' => [
        'nama' => 'Ruang Kelas Multimedia',
        'kapasitas' => 40,
        'fasilitas' => ['Proyektor', 'AC', 'Whiteboard Digital', 'WIFI'],
        'lokasi' => 'Gedung A, Lantai 3',
        'status' => 'Tersedia',
    ],
    'LAB-KIM' => [
        'nama' => 'Laboratorium Kimia Dasar',
        'kapasitas' => 25,
        'fasilitas' => ['Fume Hood', 'Peralatan Gelas Lengkap', 'Stasiun Darurat'],
        'lokasi' => 'Gedung Lab, Lantai 1',
        'status' => 'Sedang Dipakai',
    ],
];

$active_tab = $_GET['tab'] ?? 'search_room'; 
$search_query = $_GET['q'] ?? '';
$search_result = null;

if ($search_query && $active_tab == 'room_detail') {
    $key = strtoupper(trim($search_query));
    if (isset($room_details[$key])) {
        $search_result = $room_details[$key];
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
            color: #1e40af; /
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
            <li><a href="apps_dosen.php" class="block hover:text-blue-500"><i
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
            <li><a href="home_dosen.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_dosen.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
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
                    
                    <div id="content-search_room" class="tab-content" style="display: <?= $active_tab == 'search_room' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Formulir Pencarian</h3>
                        
                        <form action="room_location.php" method="GET" class="space-y-4">
                            <input type="hidden" name="tab" value="room_detail">
                            <label for="room_query" class="block text-lg font-medium text-gray-700 mb-1">Masukkan Kode/Nama Ruangan</label>
                            
                            <div class="flex space-x-2">
                                <input type="text" id="room_query" name="q" required value="<?= htmlspecialchars($search_query); ?>"
                                    class="flex-grow p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                                    placeholder="Contoh: R-305 atau LAB-KIM">
                                
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-150">
                                    <i class="fa-solid fa-search mr-2"></i> Cari
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200 text-sm text-gray-700">
                             <h4 class="font-bold text-blue-800 mb-2 flex items-center"><i class="fa-solid fa-lightbulb mr-2"></i> Tips Cepat</h4>
                             <ul class="list-disc list-inside space-y-1 ml-4">
                                <li>Gunakan kode ruangan yang spesifik (misal: R-201, LAB-FIS).</li>
                                <li>Ruangan yang statusnya 'Tersedia' dapat diajukan peminjaman via menu Apps.</li>
                             </ul>
                        </div>
                    </div>
                    
                    <div id="content-room_detail" class="tab-content" style="display: <?= $active_tab == 'room_detail' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Detail Ruangan: <span class="text-blue-600"><?= htmlspecialchars($search_query); ?></span></h3>

                        <?php if ($search_result): ?>
                            <div class="bg-white p-6 border-l-4 border-blue-600 shadow-lg rounded-lg">
                                
                                <h4 class="text-xl font-extrabold text-blue-950 mb-4"><?= htmlspecialchars($search_result['nama']); ?></h4>
                                
                                <div class="grid grid-cols-2 gap-4 text-gray-700 border-b pb-4 mb-4">
                                    <div>
                                        <p class="font-semibold text-sm">Kode Ruangan:</p>
                                        <p class="text-lg font-bold text-blue-700"><?= strtoupper(htmlspecialchars($search_query)); ?></p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm">Lokasi:</p>
                                        <p class="text-lg font-bold"><?= htmlspecialchars($search_result['lokasi']); ?></p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm">Kapasitas:</p>
                                        <p class="text-lg font-bold"><?= htmlspecialchars($search_result['kapasitas']); ?> Orang</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm">Status Saat Ini:</p>
                                        <?php if ($search_result['status'] == 'Tersedia'): ?>
                                            <span class="text-lg font-bold text-green-600 flex items-center"><i class="fa-solid fa-check-circle mr-2"></i> Tersedia</span>
                                        <?php else: ?>
                                            <span class="text-lg font-bold text-red-600 flex items-center"><i class="fa-solid fa-times-circle mr-2"></i> Sedang Dipakai</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="font-semibold text-sm text-blue-950 mb-2">Fasilitas:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($search_result['fasilitas'] as $fasilitas): ?>
                                            <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">
                                                <i class="fa-solid fa-wifi mr-1"></i> <?= htmlspecialchars($fasilitas); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                                <i class="fa-solid fa-question-circle text-4xl text-gray-500 mb-3"></i>
                                <p class="text-lg text-gray-700 font-semibold">Ruangan Tidak Ditemukan</p>
                                <p class="text-sm text-gray-600 mt-2">Pastikan kode ruangan yang Anda masukkan benar.</p>
                                <button onclick="document.getElementById('tab-search_room').click()"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                                    <i class="fa-solid fa-redo mr-2"></i> Kembali ke Pencarian
                                </button>
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