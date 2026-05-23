<?php
session_start();

// Menggunakan __DIR__ agar path-nya absolut dan tidak mudah meleset
// PASTIKAN folder kamu bernama "Server" (huruf S besar). Jika kecil, ubah menjadi 'server'
include __DIR__ . '/../Server/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
$user  = mysqli_fetch_assoc($query);

if ($user) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['id']       = $user['id'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['username'] = $user['username'];
        
        // Cukup mundur 1 folder (../) untuk kembali ke file utama di dalam /api/
        header("Location: ../dashboard.php");
        exit;
    }
}

// Cukup mundur 1 folder untuk memanggil login.php
header("Location: ../login.php?pesan=gagal");
exit;
?>