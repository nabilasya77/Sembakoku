<?php

session_start();

include __DIR__ . '/../Server/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$username = mysqli_real_escape_string(
    $koneksi,
    trim($_POST['username'])
);

$password = $_POST['password'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE username='$username' LIMIT 1"
);

if ($query && mysqli_num_rows($query) > 0) {

    $row = mysqli_fetch_assoc($query);

    if (password_verify($password, $row['password'])) {

        session_regenerate_id(true);

        $_SESSION['id']       = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama']     = $row['nama'];

        header("Location: ../dashboard.php");
        exit;
    }
}

header("Location: ../login.php?pesan=gagal");
exit;

?>