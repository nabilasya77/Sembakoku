<?php
session_start();

// 1. PENCARIAN FILE KONEKSI OTOMATIS (Mencegah error huruf besar/kecil di Hosting)
if (file_exists(__DIR__ . '/../Server/koneksi.php')) {
    include __DIR__ . '/../Server/koneksi.php';
} elseif (file_exists(__DIR__ . '/../server/koneksi.php')) {
    include __DIR__ . '/../server/koneksi.php';
} else {
    die("<div style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>
            <strong>FATAL ERROR:</strong> File koneksi.php tidak ditemukan di dalam folder Server maupun server!
         </div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pengamanan data input
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Mencari username di database
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        
        // Memverifikasi kecocokan password
        if (password_verify($password, $row['password'])) {
            
            // 2. KUNCI SUKSES MASUK DASHBOARD (Mendaftarkan session id)
            $_SESSION['id']       = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama']     = $row['nama'];

            // Alihkan ke Dashboard!
            header("Location: ../dashboard.php");
            exit;
        } else {
            // Jika Password Salah
            header("Location: ../login.php?pesan=gagal");
            exit;
        }
    } else {
        // Jika Username tidak ditemukan
        header("Location: ../login.php?pesan=gagal");
        exit;
    }
}
?>