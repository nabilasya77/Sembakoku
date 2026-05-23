<?php
include 'Server/koneksi.php';

// Jika sudah login, lempar ke dashboard
if (isset($_COOKIE['login']) && $_COOKIE['login'] == "true") {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // Gunakan md5() jika di database Anda pakai md5

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");
    $user  = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        // SET COOKIE (Masa berlaku 24 jam)
        setcookie('login', 'true', time() + 86400, '/');
        setcookie('id', $user['id'], time() + 86400, '/');
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Login</h2>
        <?php if(isset($error)) echo "<p class='text-red-500 mb-4 text-sm'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="w-full p-3 mb-4 border rounded-xl">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 mb-4 border rounded-xl">
            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold">Masuk</button>
        </form>
        <p class="mt-4 text-sm">Belum punya akun? <a href="register.php" class="text-blue-600">Daftar</a></p>
    </div>
</body>
</html>
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

        <h2 class="text-3xl font-bold text-gray-800">
            Sembako<span class="text-orange-500">Ku</span>
        </h2>

        <p class="text-gray-500 mt-1 text-sm">
            Selamat datang kembali! Silakan login.
        </p>
    </div>

    <?php if (isset($_GET['pesan'])): ?>
        <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100">
            <?php
                if ($_GET['pesan'] == 'gagal') {
                    echo 'Username atau password salah!';
                } elseif ($_GET['pesan'] == 'belum_login') {
                    echo 'Anda harus login terlebih dahulu!';
                } elseif ($_GET['pesan'] == 'logout') {
                    echo 'Berhasil logout!';
                }
            ?>
        </div>
    <?php endif; ?>

    <form action="proses/prosesLogin.php" method="POST" class="space-y-5">

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Username
            </label>

            <input
                type="text"
                name="username"
                required
                class="w-full border border-gray-300 rounded-xl px-4 py-3"
                placeholder="Masukkan username">
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Password
            </label>

            <input
                type="password"
                name="password"
                required
                class="w-full border border-gray-300 rounded-xl px-4 py-3"
                placeholder="Masukkan password">
        </div>

        <button
            type="submit"
            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-xl">
            Login
        </button>

    </form>

</div>

</body>
</html>