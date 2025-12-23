<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Aplikasi</title>
    <?php include '../include/header.php'; ?>
</head>

<body class="bg-gray-100">
    <section class="min-h-screen bg-gradient-to-b from-blue-950 to-gray-600 flex flex-col items-center justify-center px-6"> 
        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-100 mb-1">
                Selamat Datang!
            </h2>
            <h5 class="text-sm md:text-base font-light text-gray-200">
                Silahkan Login untuk mengakses sistem.
            </h5>
        </div>

        <div class="w-full max-w-sm md:max-w-md"> <?php if (!empty($error)): ?> 
            <div class="bg-red-500 text-white p-4 rounded-xl text-sm mb-6 flex items-center shadow-lg animate-pulse">
                <i class="fa-solid fa-triangle-exclamation mr-3"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form action="../actions/process_login.php" method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-100 mb-2 ml-1">Email</label>
                    <div class="flex items-center border border-transparent bg-gray-100/90 backdrop-blur-sm rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-blue-400 focus-within:bg-white transition-all">
                        <i class="fa-solid fa-envelope text-gray-500"></i>
                        <div class="h-5 w-px bg-gray-300 mx-3"></div>
                        <input type="email" id="email" name="email" class="w-full bg-transparent focus:outline-none text-gray-900 placeholder-gray-400" placeholder="nama@email.com" required>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-100 mb-2 ml-1">Password</label>
                    <div class="flex items-center border border-transparent bg-gray-100/90 backdrop-blur-sm rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-blue-400 focus-within:bg-white transition-all">
                        <i class="fa-solid fa-lock text-gray-500"></i>
                        <div class="h-5 w-px bg-gray-300 mx-3"></div>
                        <input type="password" id="password" name="password" class="w-full bg-transparent focus:outline-none text-gray-900 placeholder-gray-400" placeholder="Masukkan password anda" required>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-950 text-white py-3.5 rounded-xl font-bold hover:bg-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition duration-300 shadow-xl active:scale-95">
                        Login
                    </button>
                </div>
            </form>

            <div class="text-center mt-8">
                <a href="lupa_password.php" class="text-sm text-gray-300 hover:text-white transition-colors duration-200 underline-offset-4 hover:underline">
                    Lupa password?
                </a>
            </div>
        </div>

    </section>
</body>

</html>