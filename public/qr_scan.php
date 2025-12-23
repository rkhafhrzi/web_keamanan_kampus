<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Scan QR</title>
    <?php include '../include/header.php'; ?>
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

<div class="bg-white p-6 rounded-xl shadow-xl w-96">
    <h2 class="text-lg font-bold mb-4 text-center">Scan QR Akses</h2>
    <div id="reader"></div>
</div>

<script>
const scanner = new Html5Qrcode("reader");

scanner.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    (decodedText) => {
        fetch('../controllers/QRController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: decodedText
        })
        .then(res => res.json())
        .then(data => alert(data.message))
        .catch(() => alert('Gagal memproses QR'));
        
        scanner.stop();
    }
);
</script>

</body>
</html>
