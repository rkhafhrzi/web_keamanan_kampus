<?php
session_start();

require_once '../include/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../../public/login.php');
    exit;
}

$user = $_SESSION['user'];
$user_role = $user['role'] ?? 'default';
$role_slug = strtolower($user_role);

$home_path = "{$role_slug}/home_{$role_slug}.php";
$apps_path = "{$role_slug}/apps_{$role_slug}.php";

$active_tab = $_GET['tab'] ?? 'new_report';

/**
 * =========================
 * DATA TRACKING LAPORAN USER
 * =========================
 */
$db = Database::getConnection();
$stmt = $db->prepare("
    SELECT *
    FROM lost_items
    WHERE reporter_id = :uid
    ORDER BY created_at DESC
");
$stmt->execute(['uid' => $user['id']]);
$reports = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Keamanan</title>

    <?php include '../include/header.php'; ?>

    <style>
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
            <li><a href="<?= $home_path ?>" class="block hover:text-blue-500"><i
                        class="fa-solid fa-house mr-2"></i>Home</a></li>
            <li><a href="<?= $apps_path ?>" class="block hover:text-blue-500"><i
                        class="fa-solid fa-table-cells mr-2"></i>Apps</a></li>
            <li><a href="../public/logout.php" class="block hover:text-red-500"><i
                        class="fa-solid fa-right-from-bracket mr-2"></i>Logout</a></li>
        </ul>
    </aside>

    <nav class="w-full bg-white shadow-md py-5 px-10 flex items-center justify-between md:justify-center gap-16 z-30">
        <button id="menuBtn" class="md:hidden text-blue-900 text-xl" aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <h1 class="text-2xl font-bold text-blue-900 hidden md:block">GeoSafe</h1>
        <ul class="hidden md:flex space-x-10 text-blue-900 font-medium items-center">
            <li><a href="<?= $home_path ?>" class="hover:text-blue-600 transition duration-150">Home</a></li>
            <li><a href="<?= $apps_path ?>" class="hover:text-blue-600 transition duration-150">Apps</a></li>
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
        <section class="w-full pt-8 pb-6 mb-8 border-b-2 border-gray-200">
            <div class="max-w-4xl mx-auto text-center px-6">
                <i class="fa-solid fa-file-invoice text-5xl text-blue-950 mb-3"></i>
                <h2 class="text-3xl font-extrabold text-blue-950 mb-1">Laporan Keamanan</h2>
                <p class="text-gray-600">Ajukan laporan insiden dan lacak status laporan Anda.</p>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-6 pb-16">
            <div class="bg-white shadow-xl rounded-xl">

                <div class="flex border-b border-gray-300">
                    <button data-tab="new_report"
                        class="tab-button w-1/2 py-3 font-semibold <?= $active_tab === 'new_report' ? 'active' : '' ?>">
                        <i class="fa-solid fa-flag mr-2"></i> Buat Laporan Baru
                    </button>

                    <button data-tab="tracking"
                        class="tab-button w-1/2 py-3 font-semibold <?= $active_tab === 'tracking' ? 'active' : '' ?>">
                        <i class="fa-solid fa-map-pin mr-2"></i> Status Laporan
                    </button>
                </div>

                <div class="p-6">

                    <!-- FORM (DESAIN TIDAK DIUBAH) -->
                    <div id="content-new_report" class="tab-content"
                        style="<?= $active_tab === 'new_report' ? 'block' : 'display:none' ?>">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">
                            Formulir Pengajuan Laporan (Barang Hilang/Ditemukan)
                        </h3>

                        <form action="../public/lost_item.php" method="POST" class="space-y-6"
                            enctype="multipart/form-data">

                            <div class="bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center">
                                    <i class="fa-solid fa-location-dot mr-2"></i> Detail Insiden
                                </h4>

                                <label class="block text-sm font-medium">Judul Laporan</label>
                                <input type="text" name="judul" required class="w-full p-2 border rounded-lg">

                                <label class="block text-sm font-medium mt-3">Deskripsi Lengkap</label>
                                <textarea name="deskripsi" rows="4" required
                                    class="w-full p-2 border rounded-lg"></textarea>
                            </div>

                            <div class="bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm">
                                <h4 class="text-lg font-semibold text-red-700 mb-3 flex items-center">
                                    <i class="fa-solid fa-camera mr-2"></i> Upload Bukti (Opsional)
                                </h4>

                                <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.mp4,.mov"
                                    class="w-full p-2 border rounded-lg">
                            </div>

                            <div class="text-right pt-4">
                                <button type="submit"
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Ajukan Laporan
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- TRACKING -->
                    <div id="content-tracking" class="tab-content"
                        style="<?= $active_tab === 'tracking' ? 'block' : 'display:none' ?>">
                        <h3 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-2">Lacak Status Laporan Anda</h3>

                        <?php if (empty($reports)): ?>
                            <div class="text-center py-12 bg-gray-100 rounded-lg border border-dashed">
                                <i class="fa-solid fa-inbox text-4xl text-gray-500 mb-3"></i>
                                <p class="font-semibold">Belum Ada Laporan</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($reports as $r): ?>
                                    <div class="p-4 border-l-4 rounded shadow
<?= $r['status'] === 'hilang' ? 'border-red-600' : 'border-green-600' ?>">
                                        <div class="font-bold"><?= htmlspecialchars($r['item_name']) ?></div>
                                        <div class="text-sm"><?= htmlspecialchars($r['description']) ?></div>

                                        <?php if ($r['evidence_image']): ?>
                                            <img src="/uploads/lost_items/<?= htmlspecialchars($r['evidence_image']) ?>"
                                                class="w-28 mt-2 rounded shadow">
                                        <?php endif; ?>

                                        <span class="inline-block mt-2 px-3 py-1 text-xs rounded-full
    <?= $r['status'] === 'hilang' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                            <?= strtoupper($r['status']) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        </section>
    </main>

    <script>
        const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.style.display = 'none');
                tab.classList.add('active');
                document.getElementById(`content-${target}`).style.display = 'block';
            });
        });
    </script>

</body>

</html>