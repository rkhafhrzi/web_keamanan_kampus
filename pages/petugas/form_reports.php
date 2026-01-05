<?php
session_start();
require_once '../../include/connection.php';

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
      header('Location: ../../public/login.php');
      exit;
}

$db = Database::getConnection();
$user_id = $_SESSION['user']['id'];

// Logika Tab Aktif
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'new_report';

// Ambil data laporan dari database
$stmt = $db->prepare("SELECT * FROM reports WHERE user_id = :uid ORDER BY generated_at DESC");
$stmt->execute(['uid' => $user_id]);
$all_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Manajemen Laporan Keamanan</title>
      <?php include '../../include/header.php'; ?>
</head>

<body class="bg-gray-50">
      <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden z-40"></div>

      <aside id="sidebar"
            class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform z-50">
            <div class="p-5 border-b bg-blue-950 text-white">
                  <h2 class="text-xl font-bold">GeoSafe</h2>
            </div>
            <ul class="p-5 space-y-5 text-blue-950 font-medium">
                  <li><a href="home_petugas.php"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
                  <li><a href="validation_qr.php" class="font-bold text-blue-600">
                              <i class="fa-solid fa-qrcode mr-2"></i> Scan QR</a></li>
                  <li><a href="../../public/logout.php" class="text-red-600">
                              <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
            </ul>
      </aside>

      <!-- ===== NAVBAR ===== -->
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

      <main class="min-h-screen">
            <section class="w-full pt-8 pb-6 mb-8 border-b-2 border-gray-200 bg-white">
                  <div class="max-w-4xl mx-auto text-center px-6">
                        <i class="fa-solid fa-file-shield text-5xl text-amber-600 mb-3"></i>
                        <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Laporan Keamanan Petugas</h2>
                        <p class="text-gray-600">Catat dan tinjau laporan keamanan harian/mingguan Anda.</p>
                  </div>
            </section>

            <div class="max-w-4xl mx-auto px-6 pb-16">
                  <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-200">

                        <div class="flex bg-gray-100 border-b border-gray-200">
                              <button onclick="window.location.href='?tab=new_report'"
                                    class="w-1/2 py-4 font-bold transition-all <?= $active_tab === 'new_report' ? 'bg-white text-amber-700 border-b-4 border-amber-500' : 'text-gray-500 hover:bg-gray-200' ?>">
                                    <i class="fa-solid fa-plus-circle mr-2"></i> Buat Laporan
                              </button>
                              <button onclick="window.location.href='?tab=detail_laporan'"
                                    class="w-1/2 py-4 font-bold transition-all <?= $active_tab === 'detail_laporan' ? 'bg-white text-amber-700 border-b-4 border-amber-500' : 'text-gray-500 hover:bg-gray-200' ?>">
                                    <i class="fa-solid fa-box-archive mr-2"></i> Detail Laporan
                              </button>
                        </div>

                        <div class="p-6 md:p-8">
                              <?php if ($active_tab === 'new_report'): ?>
                                    <div id="content-new_report">
                                          <form action="process_reports.php?action=create" method="POST" class="space-y-4">
                                                <div class="bg-amber-50 p-6 rounded-lg border border-amber-200 shadow-sm">
                                                      <h4 class="text-amber-800 font-bold mb-4 flex items-center">
                                                            <i class="fa-solid fa-pen-nib mr-2"></i> Formulir Input Laporan
                                                      </h4>

                                                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div>
                                                                  <label class="block text-sm font-bold text-gray-700 mb-1">Tipe
                                                                        Laporan</label>
                                                                  <select name="report_type"
                                                                        class="w-full p-2.5 border rounded-lg focus:ring-2 focus:ring-amber-500 outline-none">
                                                                        <option value="keamanan_harian">Keamanan Harian
                                                                        </option>
                                                                        <option value="akses_ruangan">Akses Ruangan</option>
                                                                        <option value="gerbang">Lalu Lintas Gerbang</option>
                                                                  </select>
                                                            </div>
                                                            <div class="grid grid-cols-2 gap-2">
                                                                  <div>
                                                                        <label
                                                                              class="block text-sm font-bold text-gray-700 mb-1">Mulai</label>
                                                                        <input type="date" name="period_start" required
                                                                              class="w-full p-2.5 border rounded-lg">
                                                                  </div>
                                                                  <div>
                                                                        <label
                                                                              class="block text-sm font-bold text-gray-700 mb-1">Selesai</label>
                                                                        <input type="date" name="period_end" required
                                                                              class="w-full p-2.5 border rounded-lg">
                                                                  </div>
                                                            </div>
                                                      </div>

                                                      <div class="mt-4">
                                                            <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi
                                                                  Laporan</label>
                                                            <textarea name="description" rows="4" required
                                                                  class="w-full p-2.5 border rounded-lg outline-none focus:ring-2 focus:ring-amber-500"
                                                                  placeholder="Tuliskan detail laporan..."></textarea>
                                                      </div>
                                                </div>
                                                <div class="text-right">
                                                      <button type="submit"
                                                            class="bg-amber-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-amber-700 transition-all shadow-md active:scale-95">
                                                            <i class="fa-solid fa-save mr-2"></i> Simpan Laporan
                                                      </button>
                                                </div>
                                          </form>
                                    </div>
                              <?php endif; ?>

                              <?php if ($active_tab === 'detail_laporan'): ?>
                                    <div id="content-detail_laporan" class="space-y-4">
                                          <?php if (empty($all_reports)): ?>
                                                <div class="text-center py-10 text-gray-500">Belum ada data laporan.</div>
                                          <?php else: ?>
                                                <?php foreach ($all_reports as $report): ?>
                                                      <div
                                                            class="flex flex-col md:flex-row items-start md:items-center justify-between p-5 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all gap-4">
                                                            <div class="flex items-center gap-4">
                                                                  <div
                                                                        class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                                                                        <i class="fa-solid fa-file-alt"></i>
                                                                  </div>
                                                                  <div>
                                                                        <h5 class="font-bold text-gray-800 capitalize">
                                                                              <?= str_replace('_', ' ', $report['report_type']) ?>
                                                                        </h5>
                                                                        <p class="text-xs text-gray-500">
                                                                              Periode: <span
                                                                                    class="font-semibold"><?= $report['period_start'] ?></span>
                                                                              s/d <span
                                                                                    class="font-semibold"><?= $report['period_end'] ?></span>
                                                                        </p>
                                                                        <p class="text-sm text-gray-600 mt-1">
                                                                              <?= htmlspecialchars(substr($report['description'], 0, 80)) ?>...
                                                                        </p>
                                                                  </div>
                                                            </div>
                                                            <div class="flex gap-2 w-full md:w-auto border-t md:border-t-0 pt-3 md:pt-0">
                                                                  <button onclick='editReport(<?= json_encode($report) ?>)'
                                                                        class="flex-1 md:flex-none px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                                                        <i class="fa-solid fa-edit"></i>
                                                                  </button>
                                                                  <button onclick="deleteReport(<?= $report['id'] ?>)"
                                                                        class="flex-1 md:flex-none px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                  </button>
                                                            </div>
                                                      </div>
                                                <?php endforeach; ?>
                                          <?php endif; ?>
                                    </div>
                              <?php endif; ?>
                        </div>
                  </div>
            </div>
      </main>

      <script>
            function deleteReport(id) {
                  Swal.fire({
                        title: 'Hapus Laporan?',
                        text: "Data tidak bisa dikembalikan setelah dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                  }).then((result) => {
                        if (result.isConfirmed) {
                              window.location.href = `process_reports.php?action=delete&id=${id}`;
                        }
                  });
            }

            function editReport(data) {
                  Swal.fire({
                        title: '<span class="text-blue-900">Edit Laporan</span>',
                        html: `
                <form id="editForm" action="process_reports.php?action=update" method="POST" class="text-left space-y-3 p-2">
                    <input type="hidden" name="id" value="${data.id}">
                    <div>
                        <label class="text-xs font-bold text-gray-600 uppercase">Tipe Laporan</label>
                        <select name="report_type" class="w-full p-2 border rounded mt-1">
                            <option value="keamanan_harian" ${data.report_type == 'keamanan_harian' ? 'selected' : ''}>Keamanan Harian</option>
                            <option value="akses_ruangan" ${data.report_type == 'akses_ruangan' ? 'selected' : ''}>Akses Ruangan</option>
                            <option value="gerbang" ${data.report_type == 'gerbang' ? 'selected' : ''}>Gerbang</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs font-bold text-gray-600 uppercase">Mulai</label>
                            <input type="date" name="period_start" class="w-full p-2 border rounded mt-1" value="${data.period_start}">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-600 uppercase">Selesai</label>
                            <input type="date" name="period_end" class="w-full p-2 border rounded mt-1" value="${data.period_end}">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600 uppercase">Deskripsi</label>
                        <textarea name="description" rows="4" class="w-full p-2 border rounded mt-1">${data.description}</textarea>
                    </div>
                </form>
            `,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan Perubahan',
                        confirmButtonColor: '#3b82f6',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                              const form = document.getElementById('editForm');
                              if (!form.report_type.value || !form.period_start.value || !form.period_end.value || !form.description.value) {
                                    Swal.showValidationMessage('Semua kolom harus diisi!');
                              }
                              return true;
                        }
                  }).then((result) => {
                        if (result.isConfirmed) {
                              document.getElementById('editForm').submit();
                        }
                  });
            }
      </script>

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