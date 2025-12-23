<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'petugas') {
      if (!isset($_SESSION['user'])) {
            header('Location: ../../public/login.php');
            exit;
      }
}

$vehicle_info = null;
$search_query = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_plat'])) {
      $search_query = strtoupper(trim($_POST['plat_nomor']));

      $registered_vehicles = [
            'B 1234 ABC' => [
                  'pemilik' => 'Rizky Fadillah (Mahasiswa)',
                  'jenis' => 'Sepeda Motor',
                  'tipe' => 'Honda Vario 150',
                  'status_izin' => 'Aktif (Berlaku hingga 12/2026)',
                  'area_izin' => 'Parkir B & C',
                  'catatan' => 'Wajib menggunakan helm standar.',
            ],
            'D 5678 EFG' => [
                  'pemilik' => 'Dr. Budi Santoso (Dosen)',
                  'jenis' => 'Mobil',
                  'tipe' => 'Toyota Innova',
                  'status_izin' => 'Aktif Permanen',
                  'area_izin' => 'Parkir VIP',
                  'catatan' => 'Memiliki akses ke Parkir Utama.',
            ],
            'A 0000 ZZZ' => [
                  'pemilik' => 'Tidak Terdaftar',
                  'jenis' => 'Tidak Diketahui',
                  'tipe' => 'Tidak Diketahui',
                  'status_izin' => 'Ilegal / Tidak Memiliki Izin',
                  'area_izin' => 'N/A',
                  'catatan' => 'Peringatan: Kendaraan tidak terdaftar atau izin kadaluarsa.',
            ],
      ];

      if (isset($registered_vehicles[$search_query])) {
            $vehicle_info = $registered_vehicles[$search_query];
      } else {
            $vehicle_info = [
                  'pemilik' => 'Tidak Ditemukan',
                  'jenis' => 'N/A',
                  'tipe' => 'N/A',
                  'status_izin' => 'Tidak Ditemukan',
                  'area_izin' => 'N/A',
                  'catatan' => 'Plat nomor tidak ditemukan dalam database kendaraan terdaftar.',
            ];
      }

      if ($search_query == 'A 0000 ZZZ') {
            $vehicle_info = $registered_vehicles['A 0000 ZZZ'];
      }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Cek Kendaraan</title>

      <?php include '../../include/header.php'; ?>

      <style>
            #overlay {
                  background-color: rgba(0, 0, 0, 0.4);
                  backdrop-filter: blur(8px);
                  opacity: 0;
                  transition: opacity 0.3s;
                  z-index: 40;
            }

            #overlay.show {
                  opacity: 1;
            }
      </style>
</head>

<body class="bg-gray-50">
      <div id="overlay"
            class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40">
      </div>

      <aside id="sidebar"
            class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

            <div class="p-5 border-b bg-blue-900 text-white">
                  <h2 class="text-xl font-bold">GeoSafe</h2>
            </div>

            <ul class="p-5 space-y-5 text-blue-900 font-medium">
                  <li><a href="home_mahasiswa.php" class="block hover:text-blue-500"><i
                                    class="fa-solid fa-house mr-2"></i>
                              Home</a></li>
                  <li><a href="apps_mahasiswa.php" class="block hover:text-blue-500"><i
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
                  <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
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


      <main class="min-h-screen bg-gray-100 pt-10 pb-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">

                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <div class="bg-white p-6 shadow-xl rounded-xl h-fit">
                              <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center">
                                    <i class="fa-solid fa-search mr-2 text-indigo-500"></i> Cari Kendaraan
                              </h3>

                              <form method="POST" action="check_vehicle.php" class="space-y-4">
                                    <div class="mb-4">
                                          <label for="plat_nomor" class="block text-sm font-medium text-gray-700 mb-1">
                                                Plat Nomor Kendaraan (Contoh: B 1234 ABC)
                                          </label>
                                          <input type="text" id="plat_nomor" name="plat_nomor" required
                                                value="<?= htmlspecialchars($search_query); ?>"
                                                class="mt-1 block w-full px-4 py-3 border-2 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-lg uppercase font-bold text-center tracking-wider">
                                    </div>

                                    <button type="submit" name="search_plat"
                                          class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-150 shadow-md">
                                          <i class="fa-solid fa-magnifying-glass mr-2"></i> CEK STATUS KENDARAAN
                                    </button>
                              </form>
                        </div>

                        <div class="bg-white p-6 shadow-xl rounded-xl">
                              <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center">
                                    <i class="fa-solid fa-clipboard-list mr-2 text-indigo-600"></i> Detail Kendaraan
                              </h3>

                              <?php if ($vehicle_info): ?>
                                    <?php
                                    $status_class = 'text-gray-600 bg-gray-100';
                                    $status_icon = 'fa-info-circle';

                                    if (strpos($vehicle_info['status_izin'], 'Aktif') !== false) {
                                          $status_class = 'text-green-800 bg-green-100';
                                          $status_icon = 'fa-check-circle';
                                    } elseif (strpos($vehicle_info['status_izin'], 'Tidak Ditemukan') !== false || strpos($vehicle_info['status_izin'], 'Ilegal') !== false) {
                                          $status_class = 'text-red-800 bg-red-100';
                                          $status_icon = 'fa-times-circle';
                                    }
                                    ?>

                                    <div class="text-center mb-6 p-4 rounded-lg <?= $status_class; ?>">
                                          <i class="fa-solid fa-id-card-clip text-4xl mb-2"></i>
                                          <p class="text-3xl font-extrabold text-blue-950 mb-1">
                                                <?= htmlspecialchars($search_query); ?>
                                          </p>
                                          <p class="text-lg font-semibold <?= $status_class; ?>">
                                                <i class="fa-solid <?= $status_icon; ?> mr-2"></i>
                                                STATUS: <?= htmlspecialchars($vehicle_info['status_izin']); ?>
                                          </p>
                                    </div>

                                    <div class="space-y-3 text-sm">
                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Pemilik Terdaftar:</span>
                                                <span
                                                      class="font-bold text-blue-800 text-right"><?= htmlspecialchars($vehicle_info['pemilik']); ?></span>
                                          </div>
                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Jenis Kendaraan:</span>
                                                <span
                                                      class="text-gray-700"><?= htmlspecialchars($vehicle_info['jenis']); ?></span>
                                          </div>
                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Tipe/Model:</span>
                                                <span
                                                      class="text-gray-700"><?= htmlspecialchars($vehicle_info['tipe']); ?></span>
                                          </div>
                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Area Izin Parkir:</span>
                                                <span
                                                      class="font-bold text-orange-600"><?= htmlspecialchars($vehicle_info['area_izin']); ?></span>
                                          </div>
                                          <div class="pt-3 border-t mt-4">
                                                <span class="font-medium text-gray-600 block mb-1">Catatan Petugas:</span>
                                                <p
                                                      class="p-3 bg-gray-50 rounded-lg text-gray-700 italic border border-gray-200">
                                                      <?= htmlspecialchars($vehicle_info['catatan']); ?>
                                                </p>
                                          </div>
                                    </div>

                              <?php else: ?>
                                    <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                          <i class="fa-solid fa-circle-question text-4xl text-gray-500 mb-3"></i>
                                          <p class="text-md text-gray-600">Hasil verifikasi akan muncul di sini setelah Anda
                                                memasukkan dan mencari Plat Nomor.</p>
                                    </div>
                              <?php endif; ?>
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