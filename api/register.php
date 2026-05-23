<?php
include 'Server/koneksi.php';

if (isset($_POST['daftar'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $ins = mysqli_query($koneksi, "INSERT INTO user (username, password) VALUES ('$username', '$password')");
    if ($ins) {
        header("Location: login.php?pesan=daftar_sukses");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-96">
        <h2 class="text-2xl font-bold mb-6">Daftar Akun</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="w-full p-3 mb-4 border rounded-xl">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 mb-4 border rounded-xl">
            <button type="submit" name="daftar" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold">Daftar</button>
        </form>
        <p class="mt-4 text-sm">Sudah punya akun? <a href="login.php" class="text-blue-600">Login</a></p>
    </div>
</body>
</html>