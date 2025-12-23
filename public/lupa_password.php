<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password</title>
    <?php include '../include/header.php'; ?>
    <style>
        .step-content { display: none; }
        .step-content.active { display: block; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <section class="min-h-screen bg-gradient-to-b from-blue-950 to-gray-600 flex flex-col items-center justify-center px-6">
        
        <div class="text-center mb-8">
            <h2 id="step-title" class="text-3xl md:text-4xl font-bold text-gray-100 mb-2">Lupa Password</h2>
            <p id="step-desc" class="text-sm md:text-base font-light text-gray-200 max-w-xs mx-auto">
                Masukkan email Anda untuk menerima kode verifikasi.
            </p>
        </div>

        <div class="w-full max-w-sm md:max-w-md">
            <div id="step-1" class="step-content active fade-in space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-100 mb-2 ml-1">Alamat Email</label>
                    <div class="flex items-center bg-gray-100/90 backdrop-blur-sm rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-blue-400 transition-all">
                        <i class="fa-solid fa-envelope text-gray-500"></i>
                        <div class="h-5 w-px bg-gray-300 mx-3"></div>
                        <input type="email" id="input-email" class="w-full bg-transparent focus:outline-none text-gray-900" placeholder="nama@email.com">
                    </div>
                </div>
                <button onclick="sendEmail()" id="btn-send" class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-500 shadow-xl transition active:scale-95 flex items-center justify-center gap-2">
                    <span>Kirim Kode Verifikasi</span>
                </button>
            </div>

            <div id="step-2" class="step-content fade-in space-y-6">
                <div class="bg-blue-50/10 border border-blue-400/30 p-4 rounded-xl text-center mb-4">
                    <p class="text-xs text-blue-100">Kode verifikasi telah dikirim ke alamat email:</p>
                    <p id="display-email" class="font-bold text-white text-sm mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100 mb-2 ml-1">Masukkan Kode</label>
                    <div class="flex items-center bg-gray-100/90 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-blue-400 transition-all">
                        <i class="fa-solid fa-shield-dots text-gray-500"></i>
                        <div class="h-5 w-px bg-gray-300 mx-3"></div>
                        <input type="text" id="input-code" maxlength="6" class="w-full bg-transparent focus:outline-none text-gray-900 tracking-[0.5em] font-bold text-center" placeholder="******">
                    </div>
                </div>
                <button onclick="verifyCode()" class="w-full bg-green-600 text-white py-3.5 rounded-xl font-bold hover:bg-green-500 shadow-xl transition active:scale-95">
                    Verifikasi Kode
                </button>
                <button onclick="goToStep(1)" class="w-full text-gray-300 text-sm hover:text-white flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Ganti Email
                </button>
            </div>

            <div id="step-3" class="step-content fade-in space-y-6">
                <form action="../actions/process_reset.php" method="POST" class="space-y-5">
                    <input type="hidden" name="email_final" id="email-final">
                    <div>
                        <label class="block text-sm font-medium text-gray-100 mb-2 ml-1">Password Baru</label>
                        <div class="flex items-center bg-gray-100/90 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-blue-400">
                            <i class="fa-solid fa-lock text-gray-500"></i>
                            <div class="h-5 w-px bg-gray-300 mx-3"></div>
                            <input type="password" name="new_password" class="w-full bg-transparent focus:outline-none text-gray-900" placeholder="Minimal 8 karakter" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-100 mb-2 ml-1">Konfirmasi Password Baru</label>
                        <div class="flex items-center bg-gray-100/90 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-blue-400">
                            <i class="fa-solid fa-check-double text-gray-500"></i>
                            <div class="h-5 w-px bg-gray-300 mx-3"></div>
                            <input type="password" name="confirm_password" class="w-full bg-transparent focus:outline-none text-gray-900" placeholder="Ulangi password baru" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold hover:bg-blue-500 shadow-xl transition active:scale-95">
                        Simpan & Update Password
                    </button>
                </form>
            </div>

            <div class="text-center mt-8">
                <a href="login.php" class="text-sm text-gray-300 hover:text-white transition-colors duration-200">
                    Kembali ke Login
                </a>
            </div>
        </div>
    </section>

    <script>
        function goToStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            const target = document.getElementById('step-' + step);
            target.classList.add('active');

            const title = document.getElementById('step-title');
            const desc = document.getElementById('step-desc');

            if (step === 1) {
                title.innerText = "Lupa Password";
                desc.innerText = "Masukkan email Anda untuk menerima kode verifikasi.";
            } else if (step === 2) {
                title.innerText = "Verifikasi Kode";
                desc.innerText = "Silahkan cek kotak masuk email Anda.";
            } else if (step === 3) {
                title.innerText = "Password Baru";
                desc.innerText = "Silahkan buat password baru untuk akun Anda.";
            }
        }

        function sendEmail() {
            const email = document.getElementById('input-email').value;
            const btn = document.getElementById('btn-send');

            if (!email.includes('@')) {
                alert("Masukkan alamat email yang valid!");
                return;
            }

            btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i> Mengirim...';
            btn.disabled = true;

            setTimeout(() => {
                document.getElementById('display-email').innerText = email;
                document.getElementById('email-final').value = email;
                
                btn.innerHTML = 'Kirim Kode Verifikasi';
                btn.disabled = false;
                
                goToStep(2);
            }, 1500);
        }

        function verifyCode() {
            const code = document.getElementById('input-code').value;
            if (code.length < 4) {
                alert("Kode verifikasi tidak valid!");
                return;
            }

            goToStep(3);
        }
    </script>
</body>
</html>