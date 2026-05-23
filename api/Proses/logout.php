<?php
session_start();

// ✅ Hapus semua data session dengan aman
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// ✅ FIX: File ini ada di folder Proses/, maka naik satu level dengan ../
header("Location: ../login.php?pesan=logout");
exit;
?>