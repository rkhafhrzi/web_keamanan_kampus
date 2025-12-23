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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Keamanan</title>

    <?php include '../include/header.php'; ?>
    
    <style>
        .tab-button.active {
            border-bottom-width: 4px;
            border-color: #3b82f6; 
            color: #1e40af; 
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
                <i class="fa-solid fa-file-invoice text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Laporan Keamanan</h2>
                <p class="text-gray-600 text-base">Ajukan laporan insiden dan lacak status laporan Anda.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl">
                <div class="flex border-b border-gray-300">
                    
                    <button id="tab-new_report" data-tab="new_report"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none 
                        <?= $active_tab == 'new_report' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-flag mr-2"></i> Buat Laporan Baru
                    </button>

                    <button id="tab-tracking" data-tab="tracking"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none
                        <?= $active_tab == 'tracking' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-map-pin mr-2"></i> Status Laporan
                    </button>
                </div>

                <div class="p-6">
                    
                    <div id="content-new_report" class="tab-content" style="display: <?= $active_tab == 'new_report' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Formulir Pengajuan Laporan (Barang Hilang/Ditemukan)</h3>

                        <form action="#" method="POST" class="space-y-6" enctype="multipart/form-data">
                            
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center"><i class="fa-solid fa-location-dot mr-2"></i> Detail Insiden</h4>
                                
                                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Laporan</label>
                                <input type="text" id="judul" name="judul" required
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition mb-3"
                                    placeholder="Contoh: Pencurian Laptop di Kantin">

                                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Lengkap</label>
                                <textarea id="deskripsi" name="deskripsi" rows="4" required
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition"
                                    placeholder="Jelaskan kronologi, lokasi, dan waktu kejadian secara detail."></textarea>
                            </div>
                            
                            <div class="bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center"><i class="fa-solid fa-camera mr-2"></i> Upload Bukti (Opsional)</h4>

                                <label for="bukti" class="block text-sm font-medium text-gray-700 mb-1">Foto/Video Bukti</label>
                                <input type="file" id="bukti" name="bukti" accept=".jpg,.jpeg,.png,.mp4,.mov"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200"/>
                                <p class="text-xs text-gray-500 mt-1">Mendukung format gambar dan video (Max 5MB)</p>
                            </div>
                            
                            <div class="text-right pt-4">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 transition duration-150 transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-bell-slash mr-2"></i>
                                    Ajukan Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div id="content-tracking" class="tab-content" style="display: <?= $active_tab == 'tracking' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Lacak Status Laporan Anda</h3>

                        <?php if (empty($reports)): ?>
                            <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                                <i class="fa-solid fa-inbox text-4xl text-gray-500 mb-3"></i>
                                <p class="text-lg text-gray-700 font-semibold">Belum Ada Laporan Diajukan</p>
                                <p class="text-sm text-gray-600 mt-2">Silakan ajukan laporan baru pada menu **Buat Laporan Baru** untuk melacak insiden.</p>
                                <button onclick="document.getElementById('tab-new_report').click()"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                                    <i class="fa-solid fa-plus-circle mr-2"></i> Buat Laporan Sekarang
                                </button>
                            </div>
                        <?php else: ?>
                             <p>Data laporan tersedia...</p>
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

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.dataset.tab;

                tabButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.remove('text-blue-800');
                    btn.classList.add('border-transparent', 'text-gray-500', 'hover:text-blue-950');
                });
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });

                button.classList.add('active');
                button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-blue-950');
                
                document.getElementById(`content-${targetTab}`).style.display = 'block';
            });
        });
    </script>
</body>

</html>