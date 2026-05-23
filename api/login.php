<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex p-4 bg-orange-50 text-orange-500 rounded-full mb-3">
                <i class="fa-solid fa-basket-shopping text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800">Sembako<span class="text-orange-500">Ku</span></h2>
            <p class="text-gray-500 mt-1 text-sm">Selamat datang kembali! Silakan login.</p>
        </div>

        <?php if (isset($_GET['pesan'])): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-base"></i>
                <span>
                    <?php
                        if ($_GET['pesan'] == 'gagal')        echo 'Username atau password salah!';
                        elseif ($_GET['pesan'] == 'belum_login') echo 'Anda harus login terlebih dahulu!';
                        elseif ($_GET['pesan'] == 'logout')   echo 'Berhasil keluar dari sistem.';
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['registrasi']) && $_GET['registrasi'] == 'sukses'): ?>
            <div class="mb-4 p-3 bg-green-50 text-green-600 rounded-xl text-sm border border-green-100 flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-base"></i>
                <span>Registrasi berhasil! Silakan masuk menggunakan akun baru Anda.</span>
            </div>
        <?php endif; ?>

        <!-- ✅ FIX: Path action pakai huruf kecil 'Proses' — sesuaikan dengan nama folder di server -->
        <form action="Proses/prosesLogin.php" method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="username" required placeholder="Masukkan username"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150">
                </div>
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required placeholder="Masukkan password"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150">
                </div>
            </div>
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 transition duration-200">
                Masuk Sistem <i class="fa-solid fa-right-to-bracket ml-1 text-sm"></i>
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Belum punya akun? <a href="register.php" class="text-orange-500 hover:underline font-semibold">Daftar Sekarang</a>
        </p>
    </div>

</body>
</html>