<?php
// ✅ FIX 1: Sertakan koneksi dari folder Server
include 'Server/koneksi.php';

// ✅ FIX 2: Cast ke integer agar aman dari SQL Injection
$id = (int)$_GET['id'];

if ($id > 0) {
    mysqli_query($koneksi, "DELETE FROM barang WHERE id = $id");
}

header("Location: stok.php");
exit;
?>