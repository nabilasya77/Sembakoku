<?php

include __DIR__ . '/../Server/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");

if ($query && mysqli_num_rows($query) > 0) {

    $row = mysqli_fetch_assoc($query);

    if (password_verify($password, $row['password'])) {

        setcookie("login", "true", time() + 3600, "/");
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