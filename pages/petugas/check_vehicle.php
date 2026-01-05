<?php
session_start();
require_once '../../include/connection.php';

// Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
      header('Location: ../../public/login.php');
      exit;
}

$pdo = Database::getConnection();
$vehicle_info = null;
$search_query = '';

if (isset($_POST['search_plat'])) {
      $search_query = strtoupper(trim($_POST['plat_nomor']));

      try {
            // Query menggunakan kolom nama_lengkap dan nim_nip sesuai data Anda
            $sql = "SELECT v.*, u.nama_lengkap, u.nim_nip 
                FROM vehicles v 
                JOIN users u ON v.user_id = u.id 
                WHERE REPLACE(v.plate_number, ' ', '') = REPLACE(:plat, ' ', '')";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['plat' => $search_query]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                  $vehicle_info = [
                        'status_izin' => ($data['status'] == 'aktif') ? 'Aktif (Terdaftar)' : 'Non-Aktif',
                        'pemilik' => $data['nama_lengkap'] . " (" . $data['nim_nip'] . ")", // Andi Saputra (245530121617)
                        'jenis' => $data['type'],
                        'tipe' => strtoupper($data['brand']) . " - " . $data['color'],
                        'area_izin' => "Lingkungan Kampus UBP",
                        'catatan' => "Data tervalidasi. Silakan cocokkan fisik kendaraan dengan STNK/SIM di bawah.",
                        // Simpan data mentah untuk menampilkan foto
                        'stnk' => $data['stnk_file'],
                        'sim' => $data['ktp_sim_file']
                  ];
            } else {
                  $vehicle_info = [
                        'status_izin' => 'Tidak Ditemukan / Ilegal',
                        'pemilik' => 'TIDAK DIKENAL',
                        'jenis' => '-',
                        'tipe' => '-',
                        'area_izin' => 'DILARANG PARKIR',
                        'catatan' => 'Peringatan: Plat nomor ini tidak terdaftar dalam database GeoSafe!'
                  ];
            }
      } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
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
                  <li><a href="apps_petugas.php"
                              class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a>
                  </li>
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
                        <i class="fa-solid fa-check-to-slot text-5xl text-blue-950 mb-3"></i>
                        <h2 class="text-3xl font-extrabold text-blue-950">Cek Kendaraan Terdaftar</h2>
                        <p class="text-gray-600">Verifikasi data kendaraan yang terdaftar dalam sistem.</p>
                  </div>
            </section>

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

                                    // Logika warna status berdasarkan nilai status_izin
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
                                                <span class="font-bold text-blue-800 text-right">
                                                      <?= htmlspecialchars($vehicle_info['pemilik']); ?>
                                                </span>
                                          </div>

                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Jenis Kendaraan:</span>
                                                <span class="text-gray-700 capitalize">
                                                      <?= htmlspecialchars($vehicle_info['jenis']); ?>
                                                </span>
                                          </div>

                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Tipe/Model:</span>
                                                <span class="text-gray-700">
                                                      <?= htmlspecialchars($vehicle_info['tipe']); ?>
                                                </span>
                                          </div>

                                          <div class="flex justify-between items-center border-b pb-2">
                                                <span class="font-medium text-gray-600">Area Izin Parkir:</span>
                                                <span class="font-bold text-orange-600">
                                                      <?= htmlspecialchars($vehicle_info['area_izin']); ?>
                                                </span>
                                          </div>

                                          <div class="pt-3 border-t mt-4">
                                                <span class="font-medium text-gray-600 block mb-1">Catatan
                                                      Petugas:</span>
                                                <p
                                                      class="p-3 bg-gray-50 rounded-lg text-gray-700 italic border border-gray-200">
                                                      <?= htmlspecialchars($vehicle_info['catatan']); ?>
                                                </p>
                                          </div>
                                    </div>

                              <?php else: ?>
                                    <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                          <i class="fa-solid fa-circle-question text-4xl text-gray-500 mb-3"></i>
                                          <p class="text-md text-gray-600">Hasil verifikasi akan muncul di sini setelah
                                                Anda
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