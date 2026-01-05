<?php
session_start();
require_once '../include/connection.php';

// Pastikan error reporting tidak mengganggu header redirect jika tidak ada error fatal
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (!isset($_SESSION['user'])) {
    header('Location: ../public/login.php'); // Sesuaikan path jika perlu
    exit;
}

$pdo = Database::getConnection();
$user = $_SESSION['user'];
$user_id = $user['id'];
$user_role = $user['role'] ?? 'default';
$role_slug = strtolower(trim($user_role));

// Tambahkan nama folder sebelum nama file
// Hasilnya akan menjadi: mahasiswa/home_mahasiswa.php
$home_path = "{$role_slug}/home_{$role_slug}.php";
$apps_path = "{$role_slug}/apps_{$role_slug}.php";

$vehicle_info = null;
$is_registered = false;
$error_msg = null;

// --- LOGIKA 1: AMBIL DATA DARI DATABASE ---
try {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE user_id = :uid LIMIT 1");
    $stmt->execute(['uid' => $user_id]);
    $vehicle_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle_info) {
        $is_registered = true;
    }
} catch (PDOException $e) {
    $error_msg = "Error Database: " . $e->getMessage();
}

// Tentukan tab aktif
$active_tab = $is_registered ? 'info' : 'register';

// --- LOGIKA 2: PROSES POST (SIMPAN/UPDATE/HAPUS) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // A. PROSES HAPUS
        if (isset($_POST['action']) && $_POST['action'] === 'delete_vehicle') {
            $v_id = $_POST['vehicle_id'];

            if ($vehicle_info) {
                // Gunakan nama kolom yang benar: ktp_sim_file
                if (!empty($vehicle_info['stnk_file']))
                    @unlink("../uploads/vehicles/" . $vehicle_info['stnk_file']);
                if (!empty($vehicle_info['ktp_sim_file']))
                    @unlink("../uploads/vehicles/" . $vehicle_info['ktp_sim_file']);
            }

            $stmt_del = $pdo->prepare("DELETE FROM vehicles WHERE id = :vid AND user_id = :uid");
            $stmt_del->execute(['vid' => $v_id, 'uid' => $user_id]);

            header("Location: registration_vehicle.php");
            exit;
        }

        // B. PROSES SIMPAN ATAU UPDATE
        if (isset($_POST['plat_nomor'])) {
            $plat = strtoupper(trim($_POST['plat_nomor']));
            $jenis = $_POST['jenis'];

            // Mengambil nama file lama dari database menggunakan kolom yang benar
            $stnk_name = $is_registered ? ($vehicle_info['stnk_file'] ?? '') : '';
            $sim_name = $is_registered ? ($vehicle_info['ktp_sim_file'] ?? '') : '';

            $upload_dir = "../uploads/vehicles/";
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0777, true);

            // Upload STNK
            if (isset($_FILES['stnk']) && $_FILES['stnk']['error'] === 0) {
                $ext = pathinfo($_FILES['stnk']['name'], PATHINFO_EXTENSION);
                $new_stnk = "STNK_" . $user_id . "_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['stnk']['tmp_name'], $upload_dir . $new_stnk)) {
                    if ($is_registered && !empty($stnk_name))
                        @unlink($upload_dir . $stnk_name);
                    $stnk_name = $new_stnk;
                }
            }

            // Upload SIM/KTP
            if (isset($_FILES['sim']) && $_FILES['sim']['error'] === 0) {
                $ext = pathinfo($_FILES['sim']['name'], PATHINFO_EXTENSION);
                $new_sim = "SIM_" . $user_id . "_" . time() . "." . $ext;
                if (move_uploaded_file($_FILES['sim']['tmp_name'], $upload_dir . $new_sim)) {
                    // Hapus file lama jika sedang dalam mode edit (is_registered)
                    if ($is_registered && !empty($sim_name))
                        @unlink($upload_dir . $sim_name);
                    $sim_name = $new_sim;
                }
            }

            if ($is_registered) {
                $sql = "UPDATE vehicles SET plate_number = :plat, type = :jenis, stnk_file = :stnk, ktp_sim_file = :sim WHERE user_id = :uid";
            } else {
                $sql = "INSERT INTO vehicles (user_id, plate_number, type, stnk_file, ktp_sim_file, status, brand, color) 
                        VALUES (:uid, :plat, :jenis, :stnk, :sim, 'aktif', '-', '-')";
            }

            $stmt_action = $pdo->prepare($sql);
            $stmt_action->execute([
                'uid' => $user_id,
                'plat' => $plat,
                'jenis' => $jenis,
                'stnk' => $stnk_name,
                'sim' => $sim_name
            ]);

            header("Location: registration_vehicle.php");
            exit;
        }
    } catch (PDOException $e) {
        $error_msg = "Gagal memproses data: " . $e->getMessage();
    }
}
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
            <li><a href="<?= $apps_path ?>" class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a></li>
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
                <i class="fa-solid fa-id-card text-5xl text-blue-950 mb-3"></i>
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
                        <i class="fa-solid fa-file-pen mr-2"></i>
                        <?= $is_registered ? 'Edit Data' : 'Registrasi Baru' ?>
                    </button>

                    <button id="tab-info" data-tab="info" class="tab-button w-1/2 py-3 text-center text-sm sm:text-lg font-semibold border-b-4 transition duration-200 focus:outline-none
                    <?= $active_tab == 'info' ? 'active' : 'border-transparent text-gray-500 hover:text-blue-950' ?>">
                        <i class="fa-solid fa-circle-info mr-2"></i> Status Kendaraan
                    </button>
                </div>

                <div class="p-6">
                    <div id="content-register" class="tab-content"
                        style="display: <?= $active_tab == 'register' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">
                            <?= $is_registered ? 'Perbarui Informasi Kendaraan' : 'Formulir Pendaftaran & Berkas' ?>
                        </h3>

                        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-blue-800 mb-3">
                                    <i class="fa-solid fa-car-side mr-2"></i> Detail Kendaraan
                                </h4>

                                <label for="plat_nomor" class="block text-sm font-medium text-gray-700 mb-1">Plat
                                    Nomor</label>
                                <input type="text" id="plat_nomor" name="plat_nomor" required
                                    value="<?= $is_registered ? htmlspecialchars($vehicle_info['plate_number']) : '' ?>"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition mb-3"
                                    placeholder="Contoh: B 1234 XYZ">

                                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis
                                    Kendaraan</label>
                                <select id="jenis" name="jenis" required
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition">
                                    <option value="">Pilih Jenis</option>
                                    <option value="motor" <?= ($is_registered && $vehicle_info['type'] == 'motor') ? 'selected' : '' ?>>Sepeda Motor</option>
                                    <option value="mobil" <?= ($is_registered && $vehicle_info['type'] == 'mobil') ? 'selected' : '' ?>>Mobil</option>
                                </select>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-blue-800 mb-3">
                                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Berkas Pendukung
                                </h4>

                                <label for="stnk" class="block text-sm font-medium text-gray-700 mb-1">
                                    Foto STNK
                                    <?= empty($vehicle_info['stnk_file']) ? '(Wajib)' : '(Sudah Ada - Opsional)' ?>
                                </label>
                                <input type="file" id="stnk" name="stnk" <?= empty($vehicle_info['stnk_file']) ? 'required' : '' ?> accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200 mb-3" />

                                <label for="sim" class="block text-sm font-medium text-gray-700 mb-1">
                                    Foto KTP/SIM
                                    <?= empty($vehicle_info['sim_file']) ? '(Wajib)' : '(Sudah Ada - Opsional)' ?>
                                </label>
                                <input type="file" id="sim" name="sim" <?= empty($vehicle_info['sim_file']) ? 'required' : '' ?> accept=".jpg,.jpeg,.png,.pdf"
                                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200" />
                            </div>

                            <div class="text-right pt-4 flex justify-end gap-3">
                                <?php if ($is_registered): ?>
                                    <button type="button" onclick="document.getElementById('tab-info').click()"
                                        class="px-6 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition">Batal</button>
                                <?php endif; ?>

                                <button type="submit"
                                    class="inline-flex items-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-lg text-white bg-blue-950 hover:bg-blue-800 transition duration-150 transform hover:-translate-y-0.5">
                                    <i class="fa-solid <?= $is_registered ? 'fa-save' : 'fa-paper-plane' ?> mr-2"></i>
                                    <?= $is_registered ? 'Simpan Perubahan' : 'Daftarkan Kendaraan' ?>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="content-info" class="tab-content"
                        style="display: <?= $active_tab == 'info' ? 'block' : 'none' ?>;">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Status & Detail Kendaraan</h3>

                        <?php if ($is_registered): ?>
                            <div class="p-6 bg-green-50 rounded-lg border border-green-200 space-y-6">
                                <div class="flex justify-between items-center">
                                    <p class="text-xl font-bold text-green-700">
                                        <i class="fa-solid fa-check-circle mr-2"></i>Status: Terverifikasi
                                    </p>
                                    <span
                                        class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold uppercase tracking-widest">
                                        <?= htmlspecialchars($vehicle_info['status']); ?>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-5 rounded-xl shadow-sm border">
                                    <div>
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Plat Nomor</p>
                                        <p class="text-2xl font-black text-blue-900">
                                            <?= htmlspecialchars($vehicle_info['plate_number']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Jenis Kendaraan
                                        </p>
                                        <p class="text-lg font-bold text-gray-700 capitalize">
                                            <?= htmlspecialchars($vehicle_info['type']); ?>
                                        </p>
                                    </div>

                                    <div class="border-t pt-4">
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">File STNK
                                        </p>
                                        <?php if (!empty($vehicle_info['stnk_file'])): ?>
                                            <a href="../uploads/vehicles/<?= $vehicle_info['stnk_file'] ?>" target="_blank">
                                                <img src="../uploads/vehicles/<?= $vehicle_info['stnk_file'] ?>"
                                                    class="h-32 w-full object-cover rounded-lg border hover:opacity-80 transition"
                                                    alt="STNK">
                                            </a>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-500 italic">Tidak ada file</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="border-t pt-4">
                                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">File
                                            SIM/KTP</p>
                                        <?php if (!empty($vehicle_info['ktp_sim_file'])): ?>
                                            <a href="../uploads/vehicles/<?= $vehicle_info['ktp_sim_file'] ?>" target="_blank">
                                                <img src="../uploads/vehicles/<?= $vehicle_info['ktp_sim_file'] ?>"
                                                    class="h-32 w-full object-cover rounded-lg border hover:opacity-80 transition"
                                                    alt="SIM/KTP">
                                            </a>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-500 italic">Tidak ada file</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-3 pt-4 border-t border-green-200">
                                    <button onclick="enableEditMode()"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-lg transition shadow-md transform hover:-translate-y-0.5">
                                        <i class="fa-solid fa-pen-to-square mr-2"></i> Edit Data
                                    </button>

                                    <form action="" method="POST" class="flex-1 sm:flex-none"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kendaraan ini?')">
                                        <input type="hidden" name="action" value="delete_vehicle">
                                        <input type="hidden" name="vehicle_id" value="<?= $vehicle_info['id'] ?>">
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition shadow-md transform hover:-translate-y-0.5">
                                            <i class="fa-solid fa-trash-can mr-2"></i> Hapus
                                        </button>
                                    </form>
                                </div>

                                <p class="text-[10px] text-gray-400 italic mt-2">* Menghapus data akan membatalkan izin
                                    parkir Anda secara otomatis.</p>
                            </div>

                        <?php else: ?>
                            <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed border-gray-300">
                                <i class="fa-solid fa-triangle-exclamation text-4xl text-gray-600 mb-3"></i>
                                <p class="text-lg text-blue-950 font-semibold">Data Registrasi Belum Ada</p>
                                <p class="text-sm text-gray-600 mt-2 max-w-md mx-auto">Silakan daftar pada menu **Registrasi
                                    Baru**.</p>
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

        function enableEditMode() {
            // 1. Pindah ke tab register
            document.getElementById('tab-register').click();

            // 2. Ubah judul form agar user tahu ini sedang edit
            document.querySelector('#content-register h3').innerText = "Edit Data Kendaraan";

            // 3. Isi form dengan data yang sudah ada (opsional, jika ingin otomatis)
            // Anda bisa menambahkan logic PHP untuk mengisi 'value' di input HTML jika $vehicle_info ada

            // 2. Fokuskan kursor ke input plat nomor agar user bisa langsung ngetik
            document.getElementById('plat_nomor').focus();

            // 3. (Opsional) Scroll ke atas agar user melihat formnya
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

    </script>
</body>

</html>