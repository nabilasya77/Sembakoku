<?php
session_start();
include '../Server/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
$user  = mysqli_fetch_assoc($query);

if ($user) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['id']       = $user['id'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../../dashboard.php"); // ← fix di sini
        exit;
    }
}

header("Location: ../../api/login.php?pesan=gagal"); // ← dan di sini
exit;
?>