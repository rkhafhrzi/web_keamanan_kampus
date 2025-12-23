<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'petugas') {
     if (!isset($_SESSION['user'])) {
          header('Location: ../../public/login.php');
          exit;
     }
}

$scanned_data = "DSN1001"; 
$dosen_info = null;

if (isset($_GET['action']) && $_GET['action'] == 'scan') {
     $dosen_info = [
          'id_pegawai' => $scanned_data,
          'nama' => 'Dr. Budi Santoso, M.Kom.',
          'jabatan' => 'Kepala Program Studi Teknik Informatika',
          'status_kehadiran' => 'Belum Masuk', 
          'foto_profil' => 'https://via.placeholder.com/150/4169E1/FFFFFF?text=Budi+S', 
          'waktu_terakhir_masuk' => 'N/A',
          'waktu_terakhir_keluar' => 'N/A',
     ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <title>Validasi Dosen/Staf - Petugas</title>

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
                              <i class="fa-solid fa-camera mr-2 text-indigo-500"></i> Pindai Kode QR
                         </h3>

                         <div class="relative mb-6">
                              <div
                                   class="w-full h-64 bg-gray-200 border-4 border-dashed border-gray-400 flex items-center justify-center rounded-lg relative overflow-hidden">
                                   <i class="fa-solid fa-scanner-gun text-6xl text-gray-500 opacity-60"></i>
                                   <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-full h-1 bg-red-500 animate-pulse"></div>
                                   </div>
                              </div>
                              <p class="text-center text-sm text-gray-500 mt-2">Arahkan kamera ke QR Code Dosen.</p>
                         </div>

                         <form method="GET" action="validation_dosen.php" class="space-y-4">
                              <input type="hidden" name="action" value="scan">

                              <button type="submit"
                                   class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-150 shadow-md">
                                   <i class="fa-solid fa-search mr-2"></i> SIMULASI SCAN QR
                              </button>
                         </form>

                         <?php if ($dosen_info): ?>
                              <div class="mt-6 space-y-3">
                                   <h4 class="font-bold text-lg text-blue-950">Pilih Aksi:</h4>
                                   <div class="grid grid-cols-2 gap-3">
                                        <a href="process_validation.php?type=dosen&id=<?= $scanned_data; ?>&status=masuk"
                                             class="text-center block bg-green-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-600 transition duration-150 shadow-md">
                                             <i class="fa-solid fa-sign-in-alt mr-2"></i> CATAT MASUK
                                        </a>
                                        <a href="process_validation.php?type=dosen&id=<?= $scanned_data; ?>&status=keluar"
                                             class="text-center block bg-red-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-600 transition duration-150 shadow-md">
                                             <i class="fa-solid fa-sign-out-alt mr-2"></i> CATAT KELUAR
                                        </a>
                                   </div>
                              </div>
                         <?php endif; ?>
                    </div>

                    <div class="bg-white p-6 shadow-xl rounded-xl">
                         <h3 class="text-xl font-bold text-blue-950 mb-4 border-b pb-2 flex items-center">
                              <i class="fa-solid fa-address-card mr-2 text-blue-600"></i> Detail Dosen
                         </h3>

                         <?php if ($dosen_info): ?>
                              <div class="text-center mb-6">
                                   <img src="<?= $dosen_info['foto_profil']; ?>" alt="Foto Profil"
                                        class="w-24 h-24 rounded-full mx-auto mb-3 border-4 border-blue-200 shadow-lg">
                                   <p class="text-2xl font-extrabold text-blue-950">
                                        <?= htmlspecialchars($dosen_info['nama']); ?></p>
                                   <p class="text-md text-gray-600 font-semibold">
                                        ID Pegawai: <?= htmlspecialchars($dosen_info['id_pegawai']); ?></p>
                              </div>

                              <div class="space-y-3">
                                   <div class="flex justify-between items-center border-b pb-2">
                                        <span class="font-medium text-gray-600">Jabatan/Unit:</span>
                                        <span class="font-bold text-blue-800 text-right"><?= htmlspecialchars($dosen_info['jabatan']); ?></span>
                                   </div>
                                   <div class="flex justify-between items-center border-b pb-2">
                                        <span class="font-medium text-gray-600">Status Kehadiran:</span>
                                        <span class="font-bold text-lg
                                        <?php
                                             echo ($dosen_info['status_kehadiran'] == 'Masuk') ? 'text-green-600' :
                                                  (($dosen_info['status_kehadiran'] == 'Keluar') ? 'text-red-600' : 'text-orange-600');
                                        ?>
                                        ">
                                             <?= htmlspecialchars($dosen_info['status_kehadiran']); ?>
                                        </span>
                                   </div>
                                   <div class="flex justify-between items-center border-b pb-2">
                                        <span class="font-medium text-gray-600">Waktu Masuk Terakhir:</span>
                                        <span class="text-sm text-gray-700"><?= htmlspecialchars($dosen_info['waktu_terakhir_masuk']); ?></span>
                                   </div>
                                   <div class="flex justify-between items-center">
                                        <span class="font-medium text-gray-600">Waktu Keluar Terakhir:</span>
                                        <span class="text-sm text-gray-700"><?= htmlspecialchars($dosen_info['waktu_terakhir_keluar']); ?></span>
                                   </div>
                              </div>

                              <a href="log_history.php?id=<?= $scanned_data; ?>&type=dosen"
                                   class="mt-6 w-full text-center inline-block bg-blue-950 text-white px-4 py-2 text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150">
                                   Lihat Riwayat Log Penuh <i class="fa-solid fa-clock-rotate-left ml-2"></i>
                              </a>

                         <?php else: ?>
                              <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                   <i class="fa-solid fa-info-circle text-4xl text-gray-500 mb-3"></i>
                                   <p class="text-md text-gray-600">Silakan lakukan pindai QR Code Dosen/Staf untuk
                                        menampilkan informasi.</p>
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