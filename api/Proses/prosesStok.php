<?php
// ✅ Pencarian koneksi otomatis
if (file_exists(__DIR__ . '/../Server/koneksi.php')) {
    include __DIR__ . '/../Server/koneksi.php';
} elseif (file_exists(__DIR__ . '/../server/koneksi.php')) {
    include __DIR__ . '/../server/koneksi.php';
} else {
    die("FATAL ERROR: File koneksi.php tidak ditemukan!");
}

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

if ($aksi == 'tambah') {
    // ✅ Sanitasi semua string input
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori    = mysqli_real_escape_string($koneksi, trim($_POST['kategori']));
    $satuan      = mysqli_real_escape_string($koneksi, trim($_POST['satuan']));

    // ✅ Cast numerik ke integer agar aman
    $stok       = (int)$_POST['stok'];
    $harga_beli = (int)$_POST['harga_beli'];
    $harga_jual = (int)$_POST['harga_jual'];

    mysqli_query($koneksi, "INSERT INTO barang (nama_barang, kategori, stok, satuan, harga_beli, harga_jual) 
                            VALUES ('$nama_barang', '$kategori', $stok, '$satuan', $harga_beli, $harga_jual)");

} elseif ($aksi == 'edit') {
    // ✅ Cast ID ke integer
    $id          = (int)$_POST['id'];
    $nama_barang = mysqli_real_escape_string($koneksi, trim($_POST['nama_barang']));
    $kategori    = mysqli_real_escape_string($koneksi, trim($_POST['kategori']));
    $satuan      = mysqli_real_escape_string($koneksi, trim($_POST['satuan']));
    $stok        = (int)$_POST['stok'];
    $harga_beli  = (int)$_POST['harga_beli'];
    $harga_jual  = (int)$_POST['harga_jual'];

    mysqli_query($koneksi, "UPDATE barang 
                            SET nama_barang='$nama_barang', kategori='$kategori', stok=$stok, 
                                satuan='$satuan', harga_beli=$harga_beli, harga_jual=$harga_jual 
                            WHERE id = $id");

} elseif ($aksi == 'hapus') {
    // ✅ Cast ID ke integer (tidak pakai string di WHERE)
    $id = (int)$_GET['id'];
    if ($id > 0) {
        mysqli_query($koneksi, "DELETE FROM barang WHERE id = $id");
    }
}

header("Location: ../stok.php");
exit;
?>