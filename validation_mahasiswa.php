<?php
session_start();

// Perbaikan logika session: Cek apakah session ada, jika ada cek role-nya (asumsi dalam array)
if (!isset($_SESSION['user'])) {
    header('Location: ../../public/login.php');
    exit;
}

$mahasiswa_info = null;
$scanned_data = null;

// Logika: Data hanya akan terisi jika parameter 'action' ada di URL
if (isset($_GET['action']) && $_GET['action'] == 'scan') {
    $scanned_data = "MHS12345678";
    $mahasiswa_info = [
        'nim' => $scanned_data,
        'nama' => 'Rizky Fadillah',
        'prodi' => 'Teknik Informatika',
        'status_kehadiran' => 'Belum Masuk',
        'foto_profil' => 'https://via.placeholder.com/150/0000FF/FFFFFF?text=Rizky',
        'waktu_terakhir_masuk' => '18-12-2025 08:00',
        'waktu_terakhir_keluar' => '18-12-2025 17:00',
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Validasi Mahasiswa - GeoSafe</title>
    <?php include '../../include/header.php'; ?>
</head>

<body class="bg-gray-50">
    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between z-30">
        <h1 class="text-2xl font-bold text-blue-900">GeoSafe</h1>
        <a href="../../public/logout.php"
            class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Logout</a>
    </nav>

    <main class="min-h-screen bg-gray-100 pt-10 pb-16 px-4">
        <div class="max-w-5xl mx-auto">

            <?php if (isset($_SESSION['success_message'])): ?>
                <div
                    class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-md flex justify-between items-center">
                    <p><strong>Sukses:</strong> <?= $_SESSION['success_message']; ?></p>
                    <button onclick="this.parentElement.remove()" class="font-bold">&times;</button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                <div class="lg:col-span-2 bg-white p-6 shadow-xl rounded-2xl h-fit border border-gray-100">
                    <h3 class="text-lg font-bold text-blue-950 mb-4 flex items-center">
                        <i class="fa-solid fa-camera mr-2 text-indigo-500"></i> Pindai Kode QR
                    </h3>

                    <div class="relative mb-6 group">
                        <div
                            class="w-full h-64 bg-slate-900 border-4 border-gray-200 flex items-center justify-center rounded-2xl relative overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center z-10">
                                <div class="w-full h-1 bg-red-500 shadow-[0_0_15px_red] animate-pulse"></div>
                            </div>
                            <i class="fa-solid fa-qrcode text-8xl text-white opacity-20"></i>
                        </div>
                        <p class="text-center text-xs text-gray-400 mt-3 italic tracking-wide uppercase">Menunggu input
                            QR code...</p>
                    </div>

                    <form method="GET" action="validation_mahasiswa.php">
                        <input type="hidden" name="action" value="scan">
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white px-4 py-4 rounded-xl font-bold hover:bg-indigo-700 transition duration-200 shadow-lg active:scale-95 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-expand"></i> SIMULASI SCAN QR
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-3">
                    <?php if ($mahasiswa_info): ?>
                        <div
                            class="bg-white p-8 shadow-xl rounded-2xl border-t-8 border-blue-900 animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <div class="flex flex-col md:flex-row items-center gap-6 mb-8">
                                <img src="<?= $mahasiswa_info['foto_profil']; ?>" alt="Profil"
                                    class="w-32 h-32 rounded-2xl object-cover border-4 border-blue-50 border-gray-100 shadow-md">
                                <div class="text-center md:text-left">
                                    <h2 class="text-3xl font-black text-blue-950 leading-tight">
                                        <?= htmlspecialchars($mahasiswa_info['nama']); ?></h2>
                                    <p class="text-blue-600 font-mono font-bold tracking-widest">
                                        <?= htmlspecialchars($mahasiswa_info['nim']); ?></p>
                                    <span
                                        class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full uppercase italic">
                                        <?= htmlspecialchars($mahasiswa_info['prodi']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Status Kehadiran</p>
                                    <p class="text-lg font-bold text-orange-600 italic">
                                        <?= $mahasiswa_info['status_kehadiran']; ?></p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Terakhir Terdeteksi</p>
                                    <p class="text-sm font-semibold text-gray-700">
                                        <?= $mahasiswa_info['waktu_terakhir_masuk']; ?></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <a href="process_validation.php?type=mahasiswa&id=<?= $scanned_data; ?>&status=masuk"
                                    class="bg-green-500 text-white p-4 rounded-2xl font-black text-center hover:bg-green-600 shadow-lg transition-transform active:scale-95 flex flex-col items-center">
                                    <i class="fa-solid fa-right-to-bracket text-2xl mb-1"></i> CATAT MASUK
                                </a>
                                <a href="process_validation.php?type=mahasiswa&id=<?= $scanned_data; ?>&status=keluar"
                                    class="bg-red-500 text-white p-4 rounded-2xl font-black text-center hover:bg-red-600 shadow-lg transition-transform active:scale-95 flex flex-col items-center">
                                    <i class="fa-solid fa-right-from-bracket text-2xl mb-1"></i> CATAT KELUAR
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div
                            class="bg-white p-12 shadow-xl rounded-2xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-center h-full min-h-[450px]">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <i class="fa-solid fa-id-card-clip text-4xl text-gray-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-400">Menunggu Hasil Pindai</h4>
                            <p class="text-gray-400 mt-2 max-w-xs text-sm">Informasi mahasiswa akan muncul di sini setelah
                                petugas berhasil memindai QR code.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>
</body>

</html>