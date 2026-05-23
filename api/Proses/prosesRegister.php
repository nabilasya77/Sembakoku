<?php
// ✅ Hanya proses jika request dari form (method POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit;
}

// ✅ Pencarian koneksi otomatis
if (file_exists(__DIR__ . '/../Server/koneksi.php')) {
    include __DIR__ . '/../Server/koneksi.php';
} elseif (file_exists(__DIR__ . '/../server/koneksi.php')) {
    include __DIR__ . '/../server/koneksi.php';
} else {
    die("FATAL ERROR: File koneksi.php tidak ditemukan!");
}

// ✅ Sanitasi dan trim input
$nama     = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
$username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
$password = trim($_POST['password']);

// ✅ Validasi tidak boleh kosong
if (empty($nama) || empty($username) || empty($password)) {
    header("Location: ../register.php?pesan=kosong");
    exit;
}

// ✅ Validasi panjang password minimal 6 karakter
if (strlen($password) < 6) {
    header("Location: ../register.php?pesan=password_pendek");
    exit;
}

// ✅ Cek apakah username sudah dipakai
$cek_user = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username' LIMIT 1");
if (mysqli_num_rows($cek_user) > 0) {
    header("Location: ../register.php?pesan=username_ada");
    exit;
}

// ✅ Hash password dengan PASSWORD_BCRYPT (aman, cocok dengan password_verify di login)
$password_hashed = password_hash($password, PASSWORD_BCRYPT);

// ✅ Simpan ke database
$query = mysqli_query($koneksi, "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password_hashed')");

if ($query) {
    header("Location: ../login.php?registrasi=sukses");
    exit;
} else {
    // Tampilkan error MySQL untuk debugging
    die("Registrasi Gagal: " . mysqli_error($koneksi));
}
?>