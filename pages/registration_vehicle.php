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


$is_registered = false; 
$vehicle_info = []; 
$active_tab = $is_registered ? 'info' : 'register'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrasi Kendaraan</title>

    <?php include '../include/header.php'; ?>
    
    <style>
        #overlay {
            background-color: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 40;
        }
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
                <i class="fa-solid fa-car-side text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Registrasi Kendaraan</h2>
                <p class="text-gray-600 text-base">Daftarkan kendaraan Anda untuk izin parkir kampus.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl">
                <div class="flex border-b border-gray-300">
                    
                    <button id="tab-register" data-tab="register"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none 
                        <?= $active_tab == 'register' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-file-pen mr-2"></i> Registrasi Baru
                    </button>

                    <button id="tab-info" data-tab="info"
                        class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none
                        <?= $active_tab == 'info' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-circle-info mr-2"></i> Status Kendaraan
                    </button>
                </div>

                <div class="p-6">
                    
                    <div id="content-register" class="tab-content" style="display: <?= $active_tab == 'register' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Formulir Pendaftaran & Berkas</h3>

                        <form action="#" method="POST" class="space-y-6">
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-blue-800 mb-3"><i class="fa-solid fa-car-side mr-2"></i> Detail Kendaraan</h4>
                                
                                <label for="plat_nomor" class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                                <input type="text" id="plat_nomor" name="plat_nomor" required
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition mb-3"
                                    placeholder="Contoh: B 1234 XYZ">

                                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                                <select id="jenis" name="jenis" required
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition">
                                    <option value="">Pilih Jenis</option>
                                    <option value="motor">Sepeda Motor</option>
                                    <option value="mobil">Mobil</option>
                                </select>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-blue-800 mb-3"><i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Berkas Pendukung</h4>

                                <label for="stnk" class="block text-sm font-medium text-gray-700 mb-1">Foto STNK (Wajib)</label>
                                <input type="file" id="stnk" name="stnk" required accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 mb-3"/>

                                <label for="sim" class="block text-sm font-medium text-gray-700 mb-1">Foto KTP/SIM (Wajib)</label>
                                <input type="file" id="sim" name="sim" required accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200"/>
                            </div>
                            
                            <div class="text-right pt-4">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-lg text-white bg-blue-950 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transition duration-150 transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-paper-plane mr-2"></i>
                                    Dafratkan Kendaraan
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div id="content-info" class="tab-content" style="display: <?= $active_tab == 'info' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Status & Detail Kendaraan</h3>

                        <?php if ($is_registered): ?>
                            <div class="p-6 bg-green-50 rounded-lg border border-green-200 space-y-4">
                                <p class="text-lg font-semibold text-green-700">Status: Disetujui</p>
                                </div>
                        <?php else: ?>
                            <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                                <i class="fa-solid fa-triangle-exclamation text-4xl text-gray-600 mb-3"></i>
                                <p class="text-lg text-blue-950 font-semibold">Data Registrasi Belum Ada</p>
                                <p class="text-sm text-gray-600 mt-2 max-w-md mx-auto">Anda belum memiliki registrasi kendaraan yang aktif atau disetujui. Silakan daftar pada menu **Registrasi Baru** untuk mendapatkan izin parkir.</p>
                                <button onclick="document.getElementById('tab-register').click()"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                                    <i class="fa-solid fa-plus-circle mr-2"></i> Mulai Registrasi Sekarang
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