<?php
include 'Server/koneksi.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM barang WHERE id='$id'");
header("Location: stok.php");
?>