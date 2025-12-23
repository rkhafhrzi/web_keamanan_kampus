<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../../public/login.php');
    exit;
}

$user = $_SESSION['user'];
$user_role = $user['role'] ?? 'default'; 
$role_slug = strtolower($user_role); 

$home_path = "{$role_slug}/home_{$role_slug}.php"; 
$apps_path = "{$role_slug}/apps_{$role_slug}.php"; 

$lost_and_found = [
    [
        'id' => 1,
        'jenis' => 'Ditemukan',
        'item' => 'Dompet Kulit Hitam',
        'lokasi' => 'Perpustakaan, Lantai 2',
        'tanggal' => '12 Des 2025',
        'kontak' => 'Satpam Pos A',
        'warna_jenis' => 'bg-green-100 text-green-700 border-green-300',
        'icon' => 'fa-solid fa-hand-holding-dollar',
    ],
    [
        'id' => 2,
        'jenis' => 'Hilang',
        'item' => 'Kunci Motor Yamaha',
        'lokasi' => 'Area Parkir B',
        'tanggal' => '11 Des 2025',
        'kontak' => '0812xxxxxx (Fulan)',
        'warna_jenis' => 'bg-red-100 text-red-700 border-red-300',
        'icon' => 'fa-solid fa-key',
    ],
];

$alerts = [
    [
        'id' => 1,
        'judul' => 'Peningkatan Patroli Malam',
        'detail' => 'Patroli diperpanjang hingga pukul 02:00 di area asrama dan fasilitas olahraga. Mohon kerjasamanya untuk menjaga ketenangan.',
        'tanggal' => '10 Des 2025',
        'level' => 'Penting',
        'warna_level' => 'bg-yellow-100 text-yellow-800 border-yellow-400',
        'icon' => 'fa-solid fa-triangle-exclamation',
    ],
    [
        'id' => 2,
        'judul' => 'Waspada Penipuan Online',
        'detail' => 'Hindari membagikan PIN atau password kampus melalui email atau link yang tidak dikenal. Pihak kampus tidak akan pernah meminta data sensitif Anda.',
        'tanggal' => '05 Des 2025',
        'level' => 'Informasi',
        'warna_level' => 'bg-blue-100 text-blue-800 border-blue-400',
        'icon' => 'fa-solid fa-circle-info',
    ],
];


$active_tab = $_GET['tab'] ?? 'lost_and_found'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    <div id="overlay"
        class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>

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
            <li><a href="<?= $apps_path ?>" class="hover:text-blue-600 transition duration-150">Apps</a></li>
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
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Informasi Keamanan</h2>
                <p class="text-gray-600 text-base">Periksa status barang hilang/ditemukan dan peringatan resmi kampus.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl">
                <div class="flex border-b border-gray-300">
                    
                    <button id="tab-lost_and_found" data-tab="lost_and_found"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none 
                        <?= $active_tab == 'lost_and_found' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-box-open mr-2"></i> Barang Hilang & Ditemukan
                    </button>

                    <button id="tab-alerts" data-tab="alerts"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none
                        <?= $active_tab == 'alerts' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-bell mr-2"></i> Peringatan Keamanan
                    </button>
                </div>

                <div class="p-6">
                    
                    <div id="content-lost_and_found" class="tab-content" style="display: <?= $active_tab == 'lost_and_found' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Daftar Barang Terbaru</h3>

                        <div class="space-y-4">
                            <?php if (empty($lost_and_found)): ?>
                                <div class="text-center py-10 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                                    <i class="fa-solid fa-magnifying-glass text-4xl text-gray-500 mb-3"></i>
                                    <p class="text-lg text-gray-700 font-semibold">Tidak ada data Barang Hilang/Ditemukan saat ini.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($lost_and_found as $item): ?>
                                    <div class="p-4 rounded-xl shadow-md bg-white border-l-4 border-blue-600 hover:bg-gray-50 transition duration-150">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-lg font-bold text-blue-900 flex items-center">
                                                    <i class="<?= htmlspecialchars($item['icon']); ?> mr-2 w-5 text-center"></i>
                                                    <?= htmlspecialchars($item['item']); ?>
                                                </div>
                                                <p class="text-xs text-gray-500 ml-7">Ditemukan di: **<?= htmlspecialchars($item['lokasi']); ?>**</p>
                                            </div>
                                            <span class="text-xs font-semibold px-3 py-1 rounded-full <?= htmlspecialchars($item['warna_jenis']); ?> ml-4">
                                                <?= htmlspecialchars($item['jenis']); ?>
                                            </span>
                                        </div>
                                        <div class="mt-2 pt-2 border-t border-gray-100 flex justify-between items-center text-sm">
                                            <div class="text-gray-600">
                                                <span class="font-medium">Tanggal:</span> <?= htmlspecialchars($item['tanggal']); ?>
                                            </div>
                                            <div class="text-sm font-semibold text-blue-700">
                                                Hubungi: <?= htmlspecialchars($item['kontak']); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="text-center mt-6">
                            <button class="px-5 py-2 bg-blue-950 text-white rounded-lg text-sm font-medium hover:bg-blue-500 hover:bg-blue-300 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-mdtransition">
                              <a href="security_reports.php">Laporkan Barang Anda yang Hilang</a>
                            </button>
                        </div>
                    </div>
                    
                    <div id="content-alerts" class="tab-content" style="display: <?= $active_tab == 'alerts' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Peringatan Resmi dari Kampus</h3>

                        <div class="space-y-4">
                            <?php if (empty($alerts)): ?>
                                <div class="text-center py-10 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                                    <i class="fa-solid fa-bell-slash text-4xl text-gray-500 mb-3"></i>
                                    <p class="text-lg text-gray-700 font-semibold">Tidak ada peringatan keamanan aktif saat ini.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($alerts as $alert): ?>
                                    <div class="p-4 rounded-xl shadow-md bg-white border-l-4 border-red-600 hover:bg-gray-50 transition duration-150">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-lg font-bold text-red-700 flex items-center">
                                                    <i class="<?= htmlspecialchars($alert['icon']); ?> mr-2 w-5 text-center"></i>
                                                    <?= htmlspecialchars($alert['judul']); ?>
                                                </div>
                                                <p class="text-xs text-gray-500 ml-7">Tanggal Rilis: **<?= htmlspecialchars($alert['tanggal']); ?>**</p>
                                            </div>
                                            <span class="text-xs font-semibold px-3 py-1 rounded-full <?= htmlspecialchars($alert['warna_level']); ?> ml-4">
                                                <?= htmlspecialchars($alert['level']); ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 mt-2 ml-7">
                                            <?= htmlspecialchars($alert['detail']); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

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

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.dataset.tab;

                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.remove('text-emerald-800'); 
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-blue-950');
                });
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });

                button.classList.add('active');
                button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-blue-950');
                
                if (targetTab === 'lost_and_found') {
                    button.style.borderColor = '#10b981'; 
                    button.style.color = '#065f46'; 
                } else if (targetTab === 'alerts') {
                    button.style.borderColor = '#dc2626';
                    button.style.color = '#991b1b';
                }

                document.getElementById(`content-${targetTab}`).style.display = 'block';
            });
        });

        const initialActiveTabButton = document.getElementById(`tab-<?= $active_tab ?>`);
        if (initialActiveTabButton) {
             initialActiveTabButton.click(); 
        } else {
             document.getElementById('tab-lost_and_found').click(); // Default jika tidak ada tab
        }
    </script>
</body>

</html>