<?php 
include 'koneksi.php'; 

if(isset($_POST['simpan'])){
    $nama = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $hbeli = $_POST['harga_beli'];
    $hjual = $_POST['harga_jual'];

    $query = "INSERT INTO barang (nama_barang, kategori, stok, satuan, harga_beli, harga_jual) 
              VALUES ('$nama', '$kategori', '$stok', '$satuan', '$hbeli', '$hjual')";
    
    if(mysqli_query($conn, $query)){
        header("Location: stok.php");
    } else {
        echo "Gagal menambahkan data.";
    }
}
?>
<div class="main-content">
    <div class="table-wrapper" style="max-width: 600px;">
        <h3>Tambah Barang Baru</h3><br>
        <form method="POST">
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="kategori" class="form-control" required>
            </div>
            <div style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>Stok</label>
                    <input type="number" name="stok" class="form-control" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Satuan</label>
                    <input type="text" name="satuan" class="form-control" placeholder="Pcs/Kg/Dus" required>
                </div>
            </div>
            <div class="form-group">
                <label>Harga Beli</label>
                <input type="number" name="harga_beli" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Harga Jual</label>
                <input type="number" name="harga_jual" class="form-control" required>
            </div>
            <button type="submit" name="simpan" class="btn-primary" style="width:100%; margin-top:10px;">Simpan Barang</button>
        </form>
    </div>
</div>