<?php
include '../Server/koneksi.php';

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? '';

if ($aksi == 'tambah') {
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok        = (int)$_POST['stok'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga_beli  = (int)$_POST['harga_beli'];
    $harga_jual  = (int)$_POST['harga_jual'];

    mysqli_query($koneksi, "INSERT INTO barang (nama_barang, kategori, stok, satuan, harga_beli, harga_jual) VALUES ('$nama_barang', '$kategori', '$stok', '$satuan', '$harga_beli', '$harga_jual')");
} 
elseif ($aksi == 'edit') {
    $id          = (int)$_POST['id'];
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok        = (int)$_POST['stok'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga_beli  = (int)$_POST['harga_beli'];
    $harga_jual  = (int)$_POST['harga_jual'];

    mysqli_query($koneksi, "UPDATE barang SET nama_barang='$nama_barang', kategori='$kategori', stok='$stok', satuan='$satuan', harga_beli='$harga_beli', harga_jual='$harga_jual' WHERE id='$id'");
} 
elseif ($aksi == 'hapus') {
    $id = (int)$_GET['id'];
    mysqli_query($koneksi, "DELETE FROM barang WHERE id='$id'");
}

header("Location: ../stok.php");
exit;
?>