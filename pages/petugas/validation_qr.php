<?php
session_start();

if (!isset($_SESSION['user'])) {
      header('Location: ../../public/login.php');
      exit;
}

$info = null;
$scanned_id = null;

if (isset($_GET['action']) && $_GET['action'] == 'scan') {
      $scanned_id = "DSN1001";

      if (str_starts_with($scanned_id, 'DSN')) {
            $info = [
                  'type' => 'Dosen',
                  'id' => $scanned_id,
                  'nama' => 'Dr. Budi Santoso, M.Kom.',
                  'detail' => 'Kepala Program Studi Teknik Informatika',
                  'foto' => 'https://via.placeholder.com/150/4169E1/FFFFFF?text=Dr+Budi',
                  'warna' => 'blue-600'
            ];
      } else {
            $info = [
                  'type' => 'Mahasiswa',
                  'id' => $scanned_id,
                  'nama' => 'Rizky Fadillah',
                  'detail' => 'Teknik Informatika - Angkatan 2022',
                  'foto' => 'https://via.placeholder.com/150/0000FF/FFFFFF?text=Rizky',
                  'warna' => 'indigo-600'
            ];
      }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Validasi QR</title>

      <?php include '../../include/header.php'; ?>
</head>

<body class="bg-slate-50">
      <div id="overlay"
            class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40">
      </div>

      <aside id="sidebar"
            class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">

            <div class="p-5 border-b bg-blue-950 text-white">
                  <h2 class="text-xl font-bold">GeoSafe</h2>
            </div>

            <ul class="p-5 space-y-5 text-blue-950 font-medium">
                  <li><a href="home_petugas.php" class="block hover:text-blue-500"><i
                                    class="fa-solid fa-house mr-2"></i>
                              Home</a></li>
                  <li><a href="apps_petugas.php" class="block text-blue-900 font-bold border-l-4 border-blue-600 pl-1">
                              <i class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
                  <li><a href="../../public/logout.php" class="block hover:text-red-500">
                              <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
            </ul>
      </aside>

      <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
            <button id="menuBtn" class="md:hidden text-blue-950 text-xl" aria-label="Open menu">
                  <i class="fa-solid fa-bars"></i>
            </button>
            <h1 class="text-2xl font-bold text-blue-950 hidden md:block">GeoSafe</h1>
            <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
                  <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
                  <li><a href="apps_petugas.php"
                              class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a>
                  </li>
                  <li><a href="../../public/logout.php"
                              class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1">
                              <i class="fa-solid fa-right-from-bracket text-sm"></i>
                              <span class="text-sm font-semibold">Logout</span>
                        </a>
                  </li>
            </ul>
      </nav>

      <main class="max-w-5xl mx-auto pt-12 px-4">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                  <div class="lg:col-span-2">
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                              <h3
                                    class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-sm uppercase tracking-wider">
                                    <i class="fa-solid fa-expand text-indigo-500"></i> Scanner Ready
                              </h3>

                              <div
                                    class="aspect-square bg-slate-900 rounded-2xl relative overflow-hidden flex items-center justify-center border-8 border-slate-100">
                                    <div
                                          class="absolute w-full h-0.5 bg-red-500 top-1/2 shadow-[0_0_15px_red] animate-bounce">
                                    </div>
                                    <i class="fa-solid fa-qrcode text-7xl text-white opacity-10"></i>
                              </div>

                              <form method="GET" class="mt-6">
                                    <input type="hidden" name="action" value="scan">
                                    <button type="submit"
                                          class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all active:scale-95 flex items-center justify-center gap-3">
                                          <i class="fa-solid fa-camera"></i> SCAN QR SEKARANG
                                    </button>
                              </form>
                        </div>
                  </div>

                  <div class="lg:col-span-3">
                        <?php if ($info): ?>
                              <div
                                    class="bg-white p-8 rounded-3xl shadow-xl border-t-8 border-<?= $info['warna'] ?> animate-in fade-in slide-in-from-right-4 duration-300">

                                    <div class="flex items-start gap-6 mb-8">
                                          <img src="<?= $info['foto'] ?>"
                                                class="w-24 h-24 rounded-2xl object-cover shadow-lg ring-4 ring-slate-50">
                                          <div>
                                                <span
                                                      class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-tighter mb-2 inline-block">
                                                      <?= $info['type'] ?>
                                                </span>
                                                <h2 class="text-2xl font-black text-slate-800 leading-tight">
                                                      <?= $info['nama'] ?>
                                                </h2>
                                                <p class="text-<?= $info['warna'] ?> font-mono font-bold text-sm">
                                                      <?= $info['id'] ?>
                                                </p>
                                                <p class="text-sm text-slate-400 mt-1"><?= $info['detail'] ?></p>
                                          </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mt-10">
                                          <a href="process_validation.php?id=<?= $info['id'] ?>&type=<?= strtolower($info['type']) ?>&status=masuk"
                                                onclick="tampilkanPopup(event, 'Masuk')"
                                                class="group bg-emerald-500 hover:bg-emerald-600 text-white p-6 rounded-2xl text-center shadow-lg shadow-emerald-100 transition-all active:scale-95 decoration-none">
                                                <i class="fa-solid fa-right-to-bracket text-3xl mb-2"></i>
                                                <span class="block font-black text-sm uppercase tracking-widest">Catat
                                                      Masuk</span>
                                          </a>

                                          <a href="process_validation.php?id=<?= $info['id'] ?>&type=<?= strtolower($info['type']) ?>&status=keluar"
                                                onclick="tampilkanPopup(event, 'Keluar')"
                                                class="group bg-rose-500 hover:bg-rose-600 text-white p-6 rounded-2xl text-center shadow-lg shadow-rose-100 transition-all active:scale-95 decoration-none">
                                                <i class="fa-solid fa-right-from-bracket text-3xl mb-2"></i>
                                                <span class="block font-black text-sm uppercase tracking-widest">Catat
                                                      Keluar</span>
                                          </a>
                                    </div>

                                    <p class="text-center text-slate-300 text-[10px] mt-6 italic">ID Terdeteksi:
                                          <?= date('d M Y, H:i:s') ?>
                                    </p>
                              </div>
                        <?php else: ?>
                              <div
                                    class="bg-slate-100/50 border-2 border-dashed border-slate-200 p-12 rounded-3xl flex flex-col items-center justify-center text-center h-full min-h-[400px]">
                                    <div
                                          class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mb-4">
                                          <i class="fa-solid fa-user-plus text-slate-300"></i>
                                    </div>
                                    <h4 class="text-slate-500 font-bold">Siap Menerima Data</h4>
                                    <p class="text-slate-400 text-xs mt-2">Silakan scan QR Code Dosen atau Mahasiswa melalui
                                          tombol di samping.</p>
                              </div>
                        <?php endif; ?>
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
                        overlay.classList.add("hidden", "opacity-0");
                  } else {
                        overlay.classList.remove("hidden");
                        setTimeout(() => overlay.classList.remove("opacity-0"), 10);
                  }
            }
            menuBtn.addEventListener("click", toggleSidebar);
            overlay.addEventListener("click", toggleSidebar);

            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            function showTab(tabId) {
                  tabContents.forEach(content => {
                        content.style.display = 'none';
                  });
                  tabButtons.forEach(button => {
                        button.classList.remove('active');
                  });

                  document.getElementById(`tab-${tabId}`).style.display = 'block';
                  document.querySelector(`button[data-tab="${tabId}"]`).classList.add('active');
            }

            tabButtons.forEach(button => {
                  button.addEventListener('click', () => {
                        showTab(button.dataset.tab);
                  });
            });

            document.addEventListener('DOMContentLoaded', () => {
                  showTab('lost_found');
            });
      </script>

      <script>
            function tampilkanPopup(event, status) {
                  event.preventDefault();
                  const urlTujuan = event.currentTarget.href;
                  const sekarang = new Date();
                  const jam = sekarang.getHours().toString().padStart(2, '0');
                  const menit = sekarang.getMinutes().toString().padStart(2, '0');
                  const waktuString = jam + ':' + menit;

                  Swal.fire({
                        title: 'Berhasil Dicatat!',
                        html: `Aktivitas <b>${status}</b> telah terekam pada pukul <b>${waktuString} WIB</b>`,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false,
                        borderRadius: '20px'
                  }).then(() => {
                        
                        window.location.href = urlTujuan;
                  });
            }
      </script>

</body>

</html>