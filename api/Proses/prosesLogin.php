<?php

require_once __DIR__ . '/../Server/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /api/login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM user WHERE username=? LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $username
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {

    $row = mysqli_fetch_assoc($result);

    if (password_verify(
        $password,
        $row['password']
    )) {

        setcookie(
            'login',
            'true',
            time()+3600,
            '/'
        );

        setcookie(
            'id',
            $row['id'],
            time()+3600,
            '/'
        );

        setcookie(
            'username',
            $row['username'],
            time()+3600,
            '/'
        );

        setcookie(
            'nama',
            $row['nama'],
            time()+3600,
            '/'
        );

        header(
            'Location: /api/dashboard.php'
        );

        exit;
    }
}

header(
    'Location: /api/login.php?pesan=gagal'
);

exit;