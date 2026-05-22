<?php
session_start();
include '../Server/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['aksi'] == 'checkout') {
    mysqli_begin_transaction($koneksi);
    try {
        $no_faktur = "TRX-" . time();
        $total = 0;
        
        // Simpan ke tabel penjualan
        mysqli_query($koneksi, "INSERT INTO penjualan (no_faktur, tanggal, total_bayar, kasir_id) VALUES ('$no_faktur', NOW(), 0, ".$_SESSION['id'].")");
        $penjualan_id = mysqli_insert_id($koneksi);

        // Proses item di keranjang
        foreach($_SESSION['keranjang'] as $id => $item) {
            mysqli_query($koneksi, "UPDATE barang SET stok = stok - {$item['jumlah']} WHERE id = $id");
            mysqli_query($koneksi, "INSERT INTO detail_penjualan (penjualan_id, barang_id, jumlah, harga_satuan, subtotal) VALUES ($penjualan_id, $id, {$item['jumlah']}, {$item['harga']}, {$item['subtotal']})");
            $total += $item['subtotal'];
        }

        // Update total bayar
        mysqli_query($koneksi, "UPDATE penjualan SET total_bayar = $total WHERE id = $penjualan_id");
        
        mysqli_commit($koneksi);
        unset($_SESSION['keranjang']);
        echo "<script>alert('Transaksi Berhasil!'); window.location='../penjualan.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "<script>alert('Gagal!'); window.location='../penjualan.php';</script>";
    }
}
?>