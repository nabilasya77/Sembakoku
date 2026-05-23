<?php
include 'Server/koneksi.php';

if (isset($_COOKIE['login']) && $_COOKIE['login'] === "true") {
    header("Location: dashboard.php");
    exit;
}

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $pesan = "username_ada";
    } else {
        $query = "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password')";
        if (mysqli_query($koneksi, $query)) {
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
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">

<div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6">Daftar Akun</h2>

    <?php if ($pesan == 'username_ada'): ?>
        <p class="text-red-500 mb-4 text-sm">Username sudah terdaftar!</p>
    <?php elseif ($pesan == 'gagal'): ?>
        <p class="text-red-500 mb-4 text-sm">Terjadi kesalahan saat registrasi.</p>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full p-3 border rounded-xl">
        <input type="text" name="username" placeholder="Username" required class="w-full p-3 border rounded-xl">
        <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded-xl">
        <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded-xl font-bold">Daftar</button>
    </form>

    <p class="mt-4 text-sm">Sudah punya akun? <a href="login.php" class="text-blue-600">Login</a></p>
</div>

</body>
</html>