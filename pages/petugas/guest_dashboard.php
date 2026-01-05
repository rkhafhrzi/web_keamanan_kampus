<?php
session_start();
require_once '../../include/connection.php';

// Proteksi Halaman: Pastikan hanya petugas yang bisa akses
if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'petugas') {
    header('Location: ../../public/login.php');
    exit;
}

$pdo = Database::getConnection();

/**
 * 1. LOGIKA UNTUK AUTOCOMPLETE (PENCARIAN TAMU)
 * Menyiapkan data JSON agar bisa dibaca oleh script JavaScript di bawah
 */
$tamu_list = [];
try {
    $stmt_all = $pdo->query("SELECT id, name, institution, status FROM guests");
    $all_guests = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

    foreach ($all_guests as $g) {
        // Logika status agar sinkron dengan fungsi setupSearch() di JavaScript Anda
        // JS mengharapkan status 'Keluar' untuk tab Masuk, dan 'Masuk' untuk tab Keluar
        $js_status = ($g['status'] == 'checked_in') ? 'Masuk' : 'Keluar';

        $tamu_list[] = [
            'id' => $g['id'],
            'nama' => $g['name'],
            'instansi' => $g['institution'],
            'status' => $js_status
        ];
    }
} catch (PDOException $e) {
    // Jika error, tamu_list tetap kosong agar tidak merusak halaman
}
$tamu_json = json_encode($tamu_list);

/**
 * 2. LOGIKA UNTUK TABEL RIWAYAT
 * Mengambil data dari database dan memetakan statusnya untuk tampilan
 */
$history_kunjungan = [];
try {
    // Mengambil semua data tanpa filter tanggal agar Ahmad & Siti muncul
    $stmt_hist = $pdo->query("SELECT * FROM guests ORDER BY created_at DESC");
    $history_db = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

    foreach ($history_db as $log) {
        // Pemetaan status untuk badge di tabel
        $status_label = 'Booking'; // Untuk status 'approved'
        if ($log['status'] === 'checked_in') {
            $status_label = 'Aktif';
        } elseif ($log['status'] === 'checked_out') {
            $status_label = 'Selesai';
        }

        $history_kunjungan[] = [
            'nama' => $log['name'],
            'instansi' => $log['institution'],
            'tujuan' => $log['purpose'],
            // Menampilkan jam masuk, jika NULL tampilkan "-"
            'masuk' => $log['check_in'] ? date('H:i', strtotime($log['check_in'])) : '-',
            'status' => $status_label
        ];
    }
} catch (PDOException $e) {
    // Jika error, riwayat kosong
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Tamu</title>
    <?php include '../../include/header.php'; ?>
    <style>
        .tab-content {
            display: none;
        }

        .tab-button.active {
            border-bottom: 3px solid #1E3A8A;
            color: #1E3A8A;
            font-weight: 700;
            background-color: #F8FAFC;
        }

        .autocomplete-results {
            max-height: 250px;
            overflow-y: auto;
            z-index: 100;
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
            <li><a href="home_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i>
                    Home</a></li>
            <li><a href="apps_petugas.php" class="block hover:text-blue-500"><i
                        class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../../public/logout.php" class="block hover:text-red-500"><i
                        class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu"><i
                class="fa-solid fa-bars"></i></button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_petugas.php"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 transition duration-150">Apps</a>
            </li>
            <li><a href="../../public/logout.php"
                    class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1"><i
                        class="fa-solid fa-right-from-bracket text-sm"></i><span
                        class="text-sm font-semibold">Logout</span></a></li>
        </ul>
    </nav>

    <?php if (isset($_GET['msg'])): ?>
        <div class="max-w-5xl mx-auto mt-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm border border-green-200">
            <i class="fa-solid fa-circle-check mr-2"></i>
            <?php
            if ($_GET['msg'] == 'register_success')
                echo "Data tamu berhasil didaftarkan!";
            if ($_GET['msg'] == 'checkin_success')
                echo "Tamu berhasil dicatat masuk!";
            if ($_GET['msg'] == 'checkout_success')
                echo "Tamu berhasil dicatat keluar!";
            ?>
        </div>
    <?php endif; ?>

    <main class="max-w-5xl mx-auto pt-6 pb-20 px-4">
        <section class="w-full bg-gray-50 pt-8 pb-6 mb-8 border-b-2 border-gray-200">
            <div class="max-w-4xl mx-auto text-center px-6">
                <i class="fa-solid fa-people-roof text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950">Kelola Kunjungan Tamu</h2>
                <p class="text-gray-600">Pencatatan data masuk dan keluar tamu gedung.</p>
            </div>
        </section>

        <div class="bg-white p-2 rounded-t-xl shadow-sm border-b flex overflow-x-auto">
            <button data-tab="input" class="tab-button flex-1 py-4 text-gray-500 transition duration-200">
                <i class="fa-solid fa-user-plus mr-2"></i>Input Data Tamu
            </button>
            <button data-tab="checkin" class="tab-button flex-1 py-4 text-gray-500 transition duration-200">
                <i class="fa-solid fa-sign-in-alt mr-2"></i>Catat Masuk
            </button>
            <button data-tab="checkout" class="tab-button flex-1 py-4 text-gray-500 transition duration-200">
                <i class="fa-solid fa-sign-out-alt mr-2"></i>Catat Keluar
            </button>
            <button data-tab="history" class="tab-button flex-1 py-4 text-gray-500 transition duration-200">
                <i class="fa-solid fa-clock-rotate-left mr-2"></i>Riwayat
            </button>
        </div>

        <div class="bg-white p-6 shadow-xl rounded-b-xl min-h-[400px]">

            <div id="tab-checkin" class="tab-content">
                <div class="max-w-lg mx-auto py-6">
                    <h3 class="text-xl font-bold text-blue-800 mb-2">Pencatatan Masuk</h3>
                    <p class="text-sm text-gray-500 mb-6">Cari tamu yang sudah terdaftar untuk check-in.</p>

                    <div class="relative">
                        <label class="block text-sm font-semibold mb-2">Nama Tamu</label>
                        <input type="text" id="search_in" placeholder="Ketik minimal 3 huruf..."
                            class="w-full border-2 border-gray-200 rounded-xl p-4 focus:border-blue-500 outline-none transition">

                        <div id="res_in"
                            class="autocomplete-results absolute w-full bg-white border shadow-2xl rounded-xl mt-2 hidden">
                        </div>
                    </div>

                    <form action="process_guest.php" method="POST" class="mt-6">
                        <input type="hidden" name="action" value="checkin">
                        <input type="hidden" id="id_in" name="id_tamu" required>
                        <button type="submit"
                            class="w-full bg-green-600 text-white py-4 rounded-xl font-bold hover:bg-green-700 shadow-lg transition">
                            KONFIRMASI MASUK
                        </button>
                    </form>
                </div>
            </div>

            <div id="tab-checkout" class="tab-content">
                <div class="max-w-lg mx-auto py-6">
                    <h3 class="text-xl font-bold text-red-800 mb-2">Pencatatan Keluar</h3>
                    <p class="text-sm text-gray-500 mb-6">Hanya menampilkan tamu yang saat ini berada di dalam gedung.
                    </p>

                    <div class="relative">
                        <input type="text" id="search_out" placeholder="Cari nama tamu aktif..."
                            class="w-full border-2 border-red-100 rounded-xl p-4 focus:border-red-500 outline-none transition bg-red-50">
                        <div id="res_out"
                            class="autocomplete-results absolute w-full bg-white border shadow-2xl rounded-xl mt-2 hidden">
                        </div>
                    </div>

                    <form action="process_guest.php" method="POST" class="mt-6">
                        <input type="hidden" name="action" value="checkout">
                        <input type="hidden" id="id_out" name="id_tamu" required>
                        <button type="submit"
                            class="w-full bg-red-600 text-white py-4 rounded-xl font-bold hover:bg-red-700 shadow-lg transition">
                            KONFIRMASI KELUAR
                        </button>
                    </form>
                </div>
            </div>

            <div id="tab-input" class="tab-content text-center py-10">
                <i class="fa-solid fa-address-book text-6xl text-blue-200 mb-4"></i>
                <h3 class="text-2xl font-bold text-blue-900">Tamu Belum Terdaftar?</h3>
                <p class="text-gray-500 mb-8">Daftarkan data identitas tamu baru untuk mendapatkan ID Kunjungan.</p>
                <button id="btnOpenModal"
                    class="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 shadow-md">
                    Isi Formulir Tamu
                </button>
            </div>

            <div id="tab-history" class="tab-content">
                <h3 class="text-xl font-bold mb-6 text-gray-800">Riwayat Kunjungan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm border-b">
                                <th class="p-4">NAMA / INSTANSI</th>
                                <th class="p-4">TUJUAN</th>
                                <th class="p-4">MASUK</th>
                                <th class="p-4">STATUS</th>
                                <th class="p-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history_db as $log): ?>
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-4">
                                        <div class="font-bold text-blue-900"><?= htmlspecialchars($log['name']) ?></div>
                                        <div class="text-xs text-gray-400"><?= htmlspecialchars($log['institution']) ?>
                                        </div>
                                    </td>
                                    <td class="p-4 text-sm"><?= htmlspecialchars($log['purpose']) ?></td>
                                    <td class="p-4 text-sm text-gray-500">
                                        <?= $log['check_in'] ? date('H:i', strtotime($log['check_in'])) : '-' ?>
                                    </td>
                                    <td class="p-4 text-sm">
                                        <?php
                                        $status_class = ($log['status'] === 'checked_in') ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600';
                                        echo "<span class='px-3 py-1 rounded-full text-xs font-bold $status_class'>" . strtoupper($log['status']) . "</span>";
                                        ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <button onclick="editTamu(<?= htmlspecialchars(json_encode($log)) ?>)"
                                                class="text-amber-600 hover:text-amber-800">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <a href="process_tamu.php?action=delete&id=<?= $log['id'] ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus riwayat ini?')"
                                                class="text-red-600 hover:text-red-800">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="modalEdit" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-amber-600 p-4 text-white flex justify-between">
                <h4 class="font-bold">Edit Data Kunjungan</h4>
                <button onclick="closeModalEdit()"><i class="fa-solid fa-times"></i></button>
            </div>
            <form action="process_tamu.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_tamu" id="edit_id">

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" required
                        class="w-full border-b-2 p-2 outline-none focus:border-amber-600">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Instansi</label>
                    <input type="text" name="instansi" id="edit_instansi" required
                        class="w-full border-b-2 p-2 outline-none focus:border-amber-600">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Tujuan</label>
                    <textarea name="tujuan" id="edit_tujuan" required
                        class="w-full border-b-2 p-2 outline-none focus:border-amber-600" rows="2"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-amber-600 text-white py-3 rounded-xl font-bold mt-4 shadow-lg hover:bg-amber-700 transition">SIMPAN
                    PERUBAHAN</button>
            </form>
        </div>
    </div>

    <div id="modalInput" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-blue-900 p-4 text-white flex justify-between">
                <h4 class="font-bold">Registrasi Tamu Baru</h4>
                <button id="btnCloseModal"><i class="fa-solid fa-times"></i></button>
            </div>
            <form action="process_guest.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="register">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="nama" required
                        class="w-full border-b-2 p-2 outline-none focus:border-blue-900">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Instansi</label>
                    <input type="text" name="instansi" required
                        class="w-full border-b-2 p-2 outline-none focus:border-blue-900">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Tujuan</label>
                    <textarea name="tujuan" required class="w-full border-b-2 p-2 outline-none focus:border-blue-900"
                        rows="2"></textarea>
                </div>
                <button type="submit"
                    class="w-full bg-blue-900 text-white py-3 rounded-xl font-bold mt-4">SIMPAN</button>
            </form>
        </div>
    </div>
</body>

<footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
    <p class="text-sm">Contact: support@GeoSafe.com</p>
    <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe. All rights reserved.</p>
</footer>

<script>
    const tamuData = <?= $tamu_json; ?>;

    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));

        document.getElementById(`tab-${tabId}`).style.display = 'block';
        document.querySelector(`button[data-tab="${tabId}"]`).classList.add('active');
    }

    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', () => showTab(btn.dataset.tab));
    });

    const modal = document.getElementById('modalInput');
    document.getElementById('btnOpenModal').onclick = () => modal.classList.replace('hidden', 'flex');
    document.getElementById('btnCloseModal').onclick = () => modal.classList.replace('flex', 'hidden');

    function setupSearch(inputId, resultsId, hiddenId, statusTarget) {
        const input = document.getElementById(inputId);
        const results = document.getElementById(resultsId);
        const hidden = document.getElementById(hiddenId);

        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            results.innerHTML = '';

            if (query.length < 2) {
                results.classList.add('hidden');
                return;
            }

            const filtered = tamuData.filter(t =>
                t.nama.toLowerCase().includes(query) && t.status === statusTarget
            );

            if (filtered.length > 0) {
                filtered.forEach(t => {
                    const div = document.createElement('div');
                    div.className = 'p-4 hover:bg-blue-50 cursor-pointer border-b text-sm';
                    div.innerHTML = `<strong>${t.nama}</strong> <span class="text-gray-400 text-xs ml-2">${t.id}</span><br>
                                         <small class="text-blue-500">${t.instansi}</small>`;
                    div.onclick = () => {
                        input.value = t.nama + ' (' + t.id + ')';
                        hidden.value = t.id;
                        results.classList.add('hidden');
                    };
                    results.appendChild(div);
                });
                results.classList.remove('hidden');
            } else {
                results.innerHTML = '<div class="p-4 text-xs text-gray-400">Tamu tidak ditemukan...</div>';
                results.classList.remove('hidden');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        showTab('checkin');
        setupSearch('search_in', 'res_in', 'id_in', 'Keluar');
        setupSearch('search_out', 'res_out', 'id_out', 'Masuk');
    });

    window.onclick = (e) => {
        if (!e.target.matches('.autocomplete-results div') && !e.target.matches('input')) {
            document.querySelectorAll('.autocomplete-results').forEach(el => el.classList.add('hidden'));
        }
    };

    function editTamu(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nama').value = data.name;
        document.getElementById('edit_instansi').value = data.institution;
        document.getElementById('edit_tujuan').value = data.purpose;

        document.getElementById('modalEdit').classList.replace('hidden', 'flex');
    }

    function closeModalEdit() {
        document.getElementById('modalEdit').classList.replace('flex', 'hidden');
    }
</script>

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


</html>