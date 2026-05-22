<?php
include '../Server/koneksi.php';

$nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

// Cek ketersediaan username
$cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
if (mysqli_num_rows($cek_user) > 0) {
    header("Location: ../register.php?pesan=username_ada");
    exit;
}

// Enkripsi password secara aman
$password_hashed = password_hash($password, PASSWORD_BCRYPT);

$query = mysqli_query($koneksi, "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password_hashed')");

if ($query) {
    header("Location: ../login.php?registrasi=sukses");
} else {
    echo "Registrasi Gagal: " . mysqli_error($koneksi);
}
?>