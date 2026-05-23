<?php
session_start();

// ✅ Hanya proses jika request dari form (method POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

// ✅ Pencarian koneksi otomatis (toleran huruf besar/kecil folder)
if (file_exists(__DIR__ . '/../Server/koneksi.php')) {
    include __DIR__ . '/../Server/koneksi.php';
} elseif (file_exists(__DIR__ . '/../server/koneksi.php')) {
    include __DIR__ . '/../server/koneksi.php';
} else {
    die("<div style='color:red; font-family:sans-serif; text-align:center; margin-top:50px;'>
            <strong>FATAL ERROR:</strong> File koneksi.php tidak ditemukan!
         </div>");
}

// ✅ Sanitasi input
$username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
$password = trim($_POST['password']);

// ✅ Validasi tidak boleh kosong
if (empty($username) || empty($password)) {
    header("Location: ../login.php?pesan=gagal");
    exit;
}

// ✅ Cari user berdasarkan username
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' LIMIT 1");

if ($query && mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);

    // ✅ Verifikasi password dengan password_verify() — cocok dengan password_hash() di register
    if (password_verify($password, $row['password'])) {

        // ✅ Regenerasi session ID untuk keamanan (mencegah session fixation)
        session_regenerate_id(true);

        // ✅ Simpan data user ke session
        $_SESSION['id']       = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['nama']     = $row['nama'];

        // ✅ Redirect ke dashboard
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
?>