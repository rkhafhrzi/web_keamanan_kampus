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
<nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center">
    <button id="menuBtn" class="md:hidden text-blue-950 text-xl">
        <i class="fa-solid fa-bars"></i>
    </button>
    <h1 class="text-2xl font-bold text-blue-950 hidden md:block">Validasi QR Akses</h1>
</nav>

<!-- ===== MAIN ===== -->
<main class="max-w-5xl mx-auto pt-12 px-4">
<div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

<!-- ===== CAMERA ===== -->
<div class="lg:col-span-2">
    <div class="bg-white p-6 rounded-3xl shadow border">
        <h3 class="font-bold text-slate-700 mb-4 uppercase text-sm">Scan via Kamera</h3>

        <button id="btnStart"
            class="w-full mb-4 bg-blue-900 text-white py-3 rounded-xl font-bold">
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
            <input type="file"
                   name="qr_image"
                   accept="image/*"
                   required
                   class="w-full mb-4 border rounded p-2">

            <button class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold">
                Upload & Validasi
            </button>
        </form>
    </div>
</div>

<!-- ===== RESULT ===== -->
<div class="lg:col-span-3">
    <div id="resultBox"
        class="hidden bg-white p-8 rounded-3xl shadow-xl border-t-8">

        <h2 id="resultTitle" class="text-2xl font-black mb-2"></h2>
        <p id="resultMessage" class="text-sm text-slate-600"></p>
        <p id="resultTime" class="text-xs text-slate-400 mt-4"></p>
    </div>

    <div id="emptyState"
        class="bg-slate-100/50 border-2 border-dashed p-12 rounded-3xl text-center">
        <h4 class="text-slate-500 font-bold">Menunggu QR</h4>
        <p class="text-xs text-slate-400 mt-2">
            Scan kamera atau upload QR Code
        </p>
    </div>
</div>

</div>
</main>

<footer class="w-full py-6 text-center text-gray-100 bg-gradient-to-b from-gray-600 to-blue-950 mt-10">
    <p class="text-sm">© 2025 GeoSafe</p>
</footer>

<!-- ===== SCRIPT ===== -->
<script>
const btnStart = document.getElementById('btnStart');
const resultBox = document.getElementById('resultBox');
const emptyState = document.getElementById('emptyState');
const title = document.getElementById('resultTitle');
const message = document.getElementById('resultMessage');
const timeEl = document.getElementById('resultTime');

function showResult(data) {
    emptyState.classList.add('hidden');
    resultBox.classList.remove('hidden');

    const now = new Date().toLocaleString('id-ID');
    resultBox.className = 'bg-white p-8 rounded-3xl shadow-xl border-t-8';

    if (data.message.includes('diizinkan')) {
        resultBox.classList.add('border-green-500');
        title.textContent = 'AKSES DIIZINKAN';
        title.className = 'text-2xl font-black text-green-600';
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
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('../../controllers/QRController.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(showResult);
});
</script>

</body>
</html>