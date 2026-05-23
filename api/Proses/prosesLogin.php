<?php
include __DIR__ . '/../Server/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");

if ($query && mysqli_num_rows($query) > 0) {

    $row = mysqli_fetch_assoc($query);

    if (password_verify($password, $row['password'])) {
        // Set Cookie berlaku 1 jam (3600 detik)
        setcookie("login", "true", time() + 3600, "/");
        setcookie("id", $row['id'], time() + 3600, "/");
        setcookie("username", $row['username'], time() + 3600, "/");
        setcookie("nama", $row['nama'], time() + 3600, "/");

        header("Location: ../dashboard.php");
        exit;
    } else {
        header("Location: ../login.php?pesan=gagal");
        exit;
    }

} else {
    header("Location: ../login.php?pesan=gagal");
    exit;
}
?>