<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
    header('Location: ../../public/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Validasi QR</title>
    <?php include '../../include/header.php'; ?>

    <!-- QR CAMERA -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>

<body class="bg-slate-50">

    <!-- ===== SIDEBAR ===== -->
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

    <!-- ===== MAIN ===== -->
    <main class="max-w-5xl mx-auto pt-6 px-4">
        <section class="w-full pt-8 pb-6 mb-6 border-b-2 border-gray-200">
            <div class="max-w-4xl mx-auto text-center px-6">
                <i class="fa-solid fa-user-check text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950">Validasi QR Code</h2>
                <p class="text-gray-600">Pengecekan dan konfirmasi data kunjungan secara digital.</p>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            <!-- ===== CAMERA ===== -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-3xl shadow border">
                    <h3 class="font-bold text-slate-700 mb-4 uppercase text-sm">Scan via Kamera</h3>

                    <button id="btnStart" class="w-full mb-4 bg-blue-900 text-white py-3 rounded-xl font-bold">
                        Aktifkan Kamera
                    </button>

                    <div id="reader" class="rounded-2xl overflow-hidden border min-h-[260px] bg-black"></div>
                </div>

                <!-- ===== UPLOAD QR ===== -->
                <div class="bg-white p-6 rounded-3xl shadow border mt-6">
                    <h3 class="font-bold text-slate-700 mb-3 uppercase text-sm">
                        Upload QR (Alternatif)
                    </h3>

                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="file" name="qr_image" accept="image/*" required
                            class="w-full mb-4 border rounded p-2">

                        <button class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold">
                            Upload & Validasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- ===== RESULT ===== -->
            <div class="lg:col-span-3">
                <div id="resultBox" class="hidden bg-white p-8 rounded-3xl shadow-xl border-t-8">

                    <h2 id="resultTitle" class="text-2xl font-black mb-2"></h2>
                    <p id="resultMessage" class="text-sm text-slate-600"></p>
                    <p id="resultTime" class="text-xs text-slate-400 mt-4"></p>
                </div>

                <div id="actionButtons" class="mt-6 hidden flex gap-4">
                    <button onclick="logGate('masuk')"
                        class="flex-1 bg-emerald-600 text-white py-3 rounded-xl font-bold">
                        Catat Masuk
                    </button>
                    <button onclick="logGate('keluar')" class="flex-1 bg-red-600 text-white py-3 rounded-xl font-bold">
                        Catat Keluar
                    </button>
                </div>

                <div id="ownerInfo" class="mt-6 hidden text-sm text-slate-700"></div>

                <div id="emptyState" class="bg-slate-100/50 border-2 border-dashed p-12 rounded-3xl text-center">
                    <h4 class="text-slate-500 font-bold">Menunggu QR</h4>
                    <p class="text-xs text-slate-400 mt-2">
                        Scan kamera atau upload QR Code
                    </p>
                </div>
            </div>

        </div>
    </main>

    <footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
        <p class="text-sm">Contact: support@GeoSafe.com</p>
        <p class="text-xs opacity-80 mt-1">© 2025 GeoSafe</p>
    </footer>

    <!-- ===== SCRIPT ===== -->
    <script>
        const btnStart = document.getElementById('btnStart');
        const resultBox = document.getElementById('resultBox');
        const emptyState = document.getElementById('emptyState');
        const title = document.getElementById('resultTitle');
        const message = document.getElementById('resultMessage');
        const timeEl = document.getElementById('resultTime');
        let scannedUser = null;
        let scannedRoom = null;

        function showResult(data) {
            emptyState.classList.add('hidden');
            resultBox.classList.remove('hidden');

            document.getElementById('actionButtons').classList.add('hidden');
            document.getElementById('ownerInfo').classList.add('hidden');

            const now = new Date().toLocaleString('id-ID');
            resultBox.className = 'bg-white p-8 rounded-3xl shadow-xl border-t-8';

            if (data.status === 'diizinkan') {
                resultBox.classList.add('border-green-500');
                title.textContent = 'AKSES DIIZINKAN';
                title.className = 'text-2xl font-black text-green-600';

                scannedUser = data.user;
                scannedRoom = data.room_id;

                document.getElementById('actionButtons').classList.remove('hidden');
            } else {
                resultBox.classList.add('border-red-500');
                title.textContent = 'AKSES DITOLAK';
                title.className = 'text-2xl font-black text-red-600';
            }

            message.textContent = data.message;
            timeEl.textContent = 'Waktu: ' + now;
        }

        /* ===== CAMERA SCAN ===== */
        btnStart.onclick = async () => {
            btnStart.disabled = true;
            const scanner = new Html5Qrcode("reader");

            try {
                await scanner.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 250 },
                    (decodedText) => {
                        fetch('../../controllers/QRController.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: decodedText
                        })
                            .then(res => res.json())
                            .then(showResult);
                    }
                );
            } catch {
                alert('Kamera tidak tersedia, gunakan upload QR');
                btnStart.disabled = false;
            }
        };

        /* ===== UPLOAD QR ===== */
        document.getElementById('uploadForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('../../controllers/QRController.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(showResult);
        });

        function logGate(direction) {
            fetch('../../controllers/GateLogController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: scannedUser.id,
                    room_id: scannedRoom,
                    direction: direction
                })
            })
                .then(res => res.json())
                .then(data => {

                    const isMasuk = direction === 'masuk';
                    const badgeColor = isMasuk ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
                    const borderColor = isMasuk ? 'border-emerald-500' : 'border-red-500';
                    const icon = isMasuk ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket';
                    const label = isMasuk ? 'MASUK' : 'KELUAR';

                    const ownerInfo = document.getElementById('ownerInfo');

                    ownerInfo.innerHTML = `
            <div class="animate-fadeIn bg-white border-l-8 ${borderColor}
                        rounded-2xl shadow-lg p-6 space-y-4">

                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-blue-700"></i>
                        Data Pemilik QR
                    </h3>

                    <span class="px-3 py-1 rounded-full text-xs font-bold ${badgeColor}">
                        <i class="fa-solid ${icon} mr-1"></i>
                        ${label}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-slate-400"></i>
                        <div>
                            <div class="text-xs text-slate-400">Email</div>
                            <div class="font-semibold text-slate-800">
                                ${data.user.email}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-door-open text-slate-400"></i>
                        <div>
                            <div class="text-xs text-slate-400">Arah Akses</div>
                            <div class="font-semibold text-slate-800">
                                ${label}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t text-xs text-slate-400">
                    Dicatat oleh sistem GeoSafe • ${new Date().toLocaleString('id-ID')}
                </div>
            </div>
        `;

                    ownerInfo.classList.remove('hidden');
                    document.getElementById('actionButtons').classList.add('hidden');
                });
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

</body>

</html>