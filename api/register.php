<?php
include 'Server/koneksi.php';
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Memeriksa username menggunakan tabel 'users'
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $pesan = "username_ada";
    } else {
        // Memasukkan data baru menggunakan tabel 'users' yang sama
        $query = "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password')";
        if (mysqli_query($koneksi, $query)) {
            // FIX: Ubah parameter di sini agar sesuai dengan file login.php
            header("Location: login.php?registrasi=sukses");
            exit;
        } else {
            $pesan = "gagal";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-flex p-4 bg-orange-50 text-orange-500 rounded-full mb-3">
                <i class="fa-solid fa-user-plus text-4xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800">Daftar Akun</h2>
            <p class="text-gray-500 mt-1 text-sm">Buat akun pengelola tokomu sekarang.</p>
        </div>

        <?php if ($pesan == 'username_ada'): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-600 text-xs font-semibold rounded-xl border border-red-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i> Username sudah terdaftar, gunakan yang lain!
            </div>
        <?php elseif ($pesan == 'gagal'): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-600 text-xs font-semibold rounded-xl border border-red-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i> Terjadi kesalahan teknis pendaftaran.
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Contoh: Budi Santoso" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Username</label>
                <input type="text" name="username" required placeholder="Contoh: budis123" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1">Password Baru</label>
                <input type="password" name="password" required placeholder="Buat kata sandi minimal 6 karakter" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-150">
            </div>
            <button type="submit" class="w-full mt-2 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-orange-500/20 hover:shadow-orange-500/30 transition duration-200">
                Registrasi Akun <i class="fa-solid fa-user-check ml-1 text-sm"></i>
            </button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun? <a href="login.php" class="text-orange-500 font-bold hover:underline">Masuk disini</a>
        </p>
    </div>
</body>
</html>