<?php
session_start();

// ✅ Validasi login sebelum proses apapun
if (!isset($_SESSION['id'])) {
    header("Location: ../login.php?pesan=belum_login");
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

// ✅ Hanya proses jika POST dan aksi = checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_POST['aksi'] ?? '') == 'checkout') {

    // ✅ Pastikan keranjang tidak kosong
    if (empty($_SESSION['keranjang'])) {
        echo "<script>alert('Keranjang masih kosong!'); window.location='../penjualan.php';</script>";
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        // ✅ Buat nomor faktur unik berdasarkan timestamp
        $no_faktur  = "TRX-" . date('Ymd') . "-" . time();
        $kasir_id   = (int)$_SESSION['id'];
        $total      = 0;

        // ✅ Insert header transaksi dulu dengan total sementara 0
        mysqli_query($koneksi, "INSERT INTO penjualan (no_faktur, tanggal, total_bayar, kasir_id) 
                                VALUES ('$no_faktur', NOW(), 0, $kasir_id)");
        $penjualan_id = (int)mysqli_insert_id($koneksi);

        if ($penjualan_id === 0) {
            throw new Exception("Gagal membuat header transaksi.");
        }

        // ✅ Proses setiap item di keranjang
        foreach ($_SESSION['keranjang'] as $barang_id => $item) {
            $barang_id = (int)$barang_id;
            $jumlah    = (int)$item['jumlah'];
            $harga     = (int)$item['harga'];
            $subtotal  = (int)$item['subtotal'];

            // Kurangi stok barang
            mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id");

            // Insert detail transaksi
            mysqli_query($koneksi, "INSERT INTO detail_penjualan (penjualan_id, barang_id, jumlah, harga_satuan, subtotal) 
                                    VALUES ($penjualan_id, $barang_id, $jumlah, $harga, $subtotal)");

            $total += $subtotal;
        }

        // ✅ Update total bayar sesuai hitungan real
        mysqli_query($koneksi, "UPDATE penjualan SET total_bayar = $total WHERE id = $penjualan_id");

        mysqli_commit($koneksi);

        // ✅ Kosongkan keranjang setelah sukses
        unset($_SESSION['keranjang']);

        echo "<script>alert('Transaksi Berhasil! Total: Rp ' + Number($total).toLocaleString('id-ID')); window.location='../penjualan.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "<script>alert('Transaksi Gagal: " . addslashes($e->getMessage()) . "'); window.location='../penjualan.php';</script>";
    }
}
?>