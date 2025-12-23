<?php
session_start();

if (!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) !== 'petugas') {
    header('Location: ../../public/login.php');
    exit;
}

$tamu_data_simulasi = [
    ['id' => 'T00123', 'nama' => 'Bambang Sudarso', 'instansi' => 'PT Jaya Abadi', 'status' => 'Keluar'],
    ['id' => 'T0045', 'nama' => 'Santi Dewi', 'instansi' => 'SMK 1 Karawang', 'status' => 'Masuk'],
    ['id' => 'T0067', 'nama' => 'Joko Permana', 'instansi' => 'Masyarakat Umum', 'status' => 'Keluar'],
    ['id' => 'T0089', 'nama' => 'Rina Amalia', 'instansi' => 'Kementerian Riset', 'status' => 'Keluar'],
];

$history_kunjungan = [
    ['nama' => 'Bambang Sudarso', 'instansi' => 'PT Jaya Abadi', 'tujuan' => 'Riset', 'masuk' => '2025-12-16 10:30', 'keluar' => '2025-12-16 11:45', 'status' => 'Selesai'],
    ['nama' => 'Santi Dewi', 'instansi' => 'SMK 1 Karawang', 'tujuan' => 'Pendaftaran', 'masuk' => '2025-12-16 14:00', 'keluar' => 'N/A', 'status' => 'Aktif'],
    ['nama' => 'Joko Permana', 'instansi' => 'Masyarakat Umum', 'tujuan' => 'Bertemu Dosen X', 'masuk' => '2025-12-15 09:15', 'keluar' => '2025-12-15 10:00', 'status' => 'Selesai'],
];

$tamu_json = json_encode($tamu_data_simulasi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Tamu</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        .tab-content { display: none; }
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
    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-md hidden opacity-0 transition-opacity duration-300 z-40"></div>
    <aside id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg -translate-x-full transition-transform duration-300 z-50 overflow-y-auto">
        <div class="p-5 border-b bg-blue-900 text-white"><h2 class="text-xl font-bold">GeoSafe</h2></div>
        <ul class="p-5 space-y-5 text-blue-900 font-medium">
            <li><a href="home_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-house mr-2"></i> Home</a></li>
            <li><a href="apps_petugas.php" class="block hover:text-blue-500"><i class="fa-solid fa-table-cells mr-2"></i> Apps</a></li>
            <li><a href="../../public/logout.php" class="block hover:text-red-500"><i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="home_petugas.php" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="apps_petugas.php" class="hover:text-blue-600 transition duration-150">Apps</a></li>
            <li><a href="../../public/logout.php" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300 shadow-md flex items-center space-x-1"><i class="fa-solid fa-right-from-bracket text-sm"></i><span class="text-sm font-semibold">Logout</span></a></li>
        </ul>
    </nav>
    <main class="max-w-5xl mx-auto pt-10 pb-20 px-4">
        <header class="mb-8">
            <h2 class="text-3xl font-extrabold text-blue-950">Kelola Kunjungan Tamu</h2>
            <p class="text-gray-500">Pencatatan data masuk dan keluar tamu gedung.</p>
        </header>

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
                        
                        <div id="res_in" class="autocomplete-results absolute w-full bg-white border shadow-2xl rounded-xl mt-2 hidden"></div>
                    </div>

                    <form action="process_tamu.php" method="POST" class="mt-6">
                        <input type="hidden" name="action" value="checkin">
                        <input type="hidden" id="id_in" name="id_tamu" required>
                        <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold hover:bg-green-700 shadow-lg transition">
                            KONFIRMASI MASUK
                        </button>
                    </form>
                </div>
            </div>

            <div id="tab-checkout" class="tab-content">
                <div class="max-w-lg mx-auto py-6">
                    <h3 class="text-xl font-bold text-red-800 mb-2">Pencatatan Keluar</h3>
                    <p class="text-sm text-gray-500 mb-6">Hanya menampilkan tamu yang saat ini berada di dalam gedung.</p>
                    
                    <div class="relative">
                        <input type="text" id="search_out" placeholder="Cari nama tamu aktif..." 
                               class="w-full border-2 border-red-100 rounded-xl p-4 focus:border-red-500 outline-none transition bg-red-50">
                        <div id="res_out" class="autocomplete-results absolute w-full bg-white border shadow-2xl rounded-xl mt-2 hidden"></div>
                    </div>

                    <form action="process_tamu.php" method="POST" class="mt-6">
                        <input type="hidden" name="action" value="checkout">
                        <input type="hidden" id="id_out" name="id_tamu" required>
                        <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-xl font-bold hover:bg-red-700 shadow-lg transition">
                            KONFIRMASI KELUAR
                        </button>
                    </form>
                </div>
            </div>

            <div id="tab-input" class="tab-content text-center py-10">
                <i class="fa-solid fa-address-book text-6xl text-blue-200 mb-4"></i>
                <h3 class="text-2xl font-bold text-blue-900">Tamu Belum Terdaftar?</h3>
                <p class="text-gray-500 mb-8">Daftarkan data identitas tamu baru untuk mendapatkan ID Kunjungan.</p>
                <button id="btnOpenModal" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 shadow-md">
                    Isi Formulir Tamu
                </button>
            </div>

            <div id="tab-history" class="tab-content">
                <h3 class="text-xl font-bold mb-6 text-gray-800">Riwayat Hari Ini</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm border-b">
                                <th class="p-4">NAMA / INSTANSI</th>
                                <th class="p-4">TUJUAN</th>
                                <th class="p-4">MASUK</th>
                                <th class="p-4">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history_kunjungan as $log): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="font-bold text-blue-900"><?= $log['nama'] ?></div>
                                    <div class="text-xs text-gray-400"><?= $log['instansi'] ?></div>
                                </td>
                                <td class="p-4 text-sm"><?= $log['tujuan'] ?></td>
                                <td class="p-4 text-sm text-gray-500"><?= $log['masuk'] ?></td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $log['status'] === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' ?>">
                                        <?= $log['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <div id="modalInput" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-blue-900 p-4 text-white flex justify-between">
                <h4 class="font-bold">Registrasi Tamu Baru</h4>
                <button id="btnCloseModal"><i class="fa-solid fa-times"></i></button>
            </div>
            <form action="process_tamu.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="register">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full border-b-2 p-2 outline-none focus:border-blue-900">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Instansi</label>
                    <input type="text" name="instansi" required class="w-full border-b-2 p-2 outline-none focus:border-blue-900">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase">Tujuan</label>
                    <textarea name="tujuan" required class="w-full border-b-2 p-2 outline-none focus:border-blue-900" rows="2"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-900 text-white py-3 rounded-xl font-bold mt-4">SIMPAN & CETAK ID</button>
            </form>
        </div>
    </div>

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

            input.addEventListener('input', function() {
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
    </script>
</body>
</html>