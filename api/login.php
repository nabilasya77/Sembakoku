<?php
include 'Server/koneksi.php';

// Jika sudah login, lempar ke dashboard
if (isset($_COOKIE['login']) && $_COOKIE['login'] == "true") {
    header("Location: dashboard.php"); // Diubah dari index.php
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; 

    // Asumsi tabel di database adalah 'users'. Jika 'user', hapus huruf 's' nya.
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    $user  = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        // SET COOKIE (Masa berlaku 24 jam)
        setcookie('login', 'true', time() + 86400, '/');
        setcookie('id', $user['id'], time() + 86400, '/');
        
        header("Location: dashboard.php"); // Diubah dari index.php
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