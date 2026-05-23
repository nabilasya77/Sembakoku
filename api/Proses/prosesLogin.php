<?php
session_start(); // Pastikan ini ada di baris paling atas!
include '../Server/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        
        // Memverifikasi password (jika waktu register menggunakan password_hash)
        if (password_verify($password, $row['password'])) {
            
            // --- BAGIAN KRUSIAL / KUNCI PERBAIKAN ---
            $_SESSION['id']       = $row['id'];       // PENTING: Key harus 'id' sesuai pengecekan di dashboard
            $_SESSION['username'] = $row['username']; 
            $_SESSION['nama']     = $row['nama'];
            // ----------------------------------------

            // Alihkan ke dashboard jika sukses
            header("Location: ../dashboard.php");
            exit;
        } else {
            // Password salah
            header("Location: ../login.php?pesan=gagal");
            exit;
        }
    } else {
        // Username tidak ditemukan
        header("Location: ../login.php?pesan=gagal");
        exit;
    }
}