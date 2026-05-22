<?php
include 'Server/koneksi.php';
include 'sidebar.php';

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Logika 1: Tambah Item Ke Keranjang Belanja Sementara
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah_keranjang') {
    $barang_id = (int)$_POST['barang_id'];
    $jumlah    = (int)$_POST['jumlah'];

    $barang_q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id = '$barang_id'");
    $brg      = mysqli_fetch_assoc($barang_q);

    if ($brg && $brg['stok'] >= $jumlah) {
        if (isset($_SESSION['keranjang'][$barang_id])) {
            $_SESSION['keranjang'][$barang_id]['jumlah']   += $jumlah;
            $_SESSION['keranjang'][$barang_id]['subtotal'] = $_SESSION['keranjang'][$barang_id]['jumlah'] * $brg['harga_jual'];
        } else {
            $_SESSION['keranjang'][$barang_id] = [
                'nama'     => $brg['nama_barang'],
                'harga'    => $brg['harga_jual'],
                'jumlah'   => $jumlah,
                'subtotal' => $brg['harga_jual'] * $jumlah
            ];
        }
        echo "<script>window.location='penjualan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal! Stok barang tidak mencukupi kebutuhan input.'); window.location='penjualan.php';</script>";
        exit;
    }
}

// Logika 2: Reset / Hapus Item Keranjang Tunggal
if (isset($_GET['hapus_item'])) {
    $id_del = (int)$_GET['hapus_item'];
    unset($_SESSION['keranjang'][$id_del]);
    header("Location: penjualan.php");
    exit;
}

// Logika 3: Checkout Simpan Transaksi Permanen
if (isset($_POST['aksi']) && $_POST['aksi'] == 'checkout' && !empty($_SESSION['keranjang'])) {
    $total_bayar = 0;
    foreach ($_SESSION['keranjang'] as $item) {
        $total_bayar += $item['subtotal'];
    }
    $tanggal_sekarang = date('Y-m-d H:i:s');

    // Insert ke tabel master penjualan
    $ins_penjualan = mysqli_query($koneksi, "INSERT INTO penjualan (tanggal, total_bayar) VALUES ('$tanggal_sekarang', '$total_bayar')");
    $penjualan_id  = mysqli_insert_id($koneksi);

    if ($ins_penjualan) {
        foreach ($_SESSION['keranjang'] as $b_id => $item) {
            $jml = $item['jumlah'];
            // Insert ke tabel detail transaksi
            mysqli_query($koneksi, "INSERT INTO detail_penjualan (penjualan_id, barang_id, jumlah, subtotal) VALUES ('$penjualan_id', '$b_id', '$jml', '".$item['subtotal']."')");
            // Potong Stok Produk Otomatis
            mysqli_query($koneksi, "UPDATE barang SET stok = stok - $jml WHERE id = '$b_id'");
        }
        $_SESSION['keranjang'] = []; // Kosongkan keranjang belanja
        echo "<script>alert('Transaksi penjualan sukses disimpan!'); window.location='penjualan.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased">
    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="md:w-64 flex-shrink-0"></div>

        <main class="flex-1 p-6 md:p-8 mt-16 md:mt-0 overflow-x-hidden">
            <div class="mb-6 border-b pb-4">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Transaksi Kasir</h1>
                <p class="text-sm text-slate-500">Pilih barang dagangan, tentukan jumlah kuantitas beli, dan lakukan pembayaran langsung kasir.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="font-bold text-lg text-slate-900"><i class="fa-solid fa-cart-plus text-orange-500 mr-1"></i> Pilih Barang</h3>
                    <form action="penjualan.php" method="POST" class="space-y-4">
                        <input type="hidden" name="aksi" value="tambah_keranjang">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Barang Produk</label>
                            <select name="barang_id" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 text-sm">
                                <option value="">-- Pilih Produk --</option>
                                <?php
                                $get_b = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
                                while ($b = mysqli_fetch_assoc($get_b)) {
                                    echo "<option value='".$b['id']."'>".$b['nama_barang']." (Stok: ".$b['stok']." ".$b['satuan'].") - Rp ".number_format($b['harga_jual'],0,',','.')."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Kuantitas Kebutuhan</label>
                            <input type="number" name="jumlah" min="1" required value="1" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 text-sm">
                        </div>
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-4 rounded-xl transition text-sm">
                            <i class="fa-solid fa-plus mr-1"></i> Tambahkan
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100"><h3 class="font-bold text-slate-900">Keranjang Belanja</h3></div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50 text-slate-400 text-[11px] uppercase font-semibold">
                                    <th class="p-4 pl-6 text-center w-12">No</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Harga Satuan</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4">Subtotal</th>
                                    <th class="p-4 text-center w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                                <?php
                                $no_k = 1; $total_akhir = 0; $total_item = 0;
                                if(empty($_SESSION['keranjang'])):
                                ?>
                                    <tr><td colspan="6" class="p-8 text-center text-slate-400 font-normal">Keranjang belanja masih kosong.</td></tr>
                                <?php
                                else:
                                    foreach ($_SESSION['keranjang'] as $key_id => $item):
                                        $total_akhir += $item['subtotal'];
                                        $total_item  += $item['jumlah'];
                                ?>
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="p-4 text-center text-slate-400 font-normal"><?php echo $no_k++; ?></td>
                                        <td class="p-4 font-bold text-slate-900"><?php echo htmlspecialchars($item['nama']); ?></td>
                                        <td class="p-4 text-slate-500">Rp <?php echo number_format($item['harga'],0,',','.'); ?></td>
                                        <td class="p-4 text-center"><?php echo $item['jumlah']; ?></td>
                                        <td class="p-4 text-orange-600 font-bold">Rp <?php echo number_format($item['subtotal'],0,',','.'); ?></td>
                                        <td class="p-4 text-center">
                                            <a href="penjualan.php?hapus_item=<?php echo $key_id; ?>" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash-can"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(!empty($_SESSION['keranjang'])): ?>
                    <div class="p-5 bg-gray-50 border-t flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <span class="text-xs text-gray-400 uppercase font-semibold block">Total Pembayaran</span>
                            <span class="text-2xl font-black text-gray-900">Rp <?php echo number_format($total_akhir,0,',','.'); ?></span>
                            <span class="text-xs text-gray-500 block">(Total: <?php echo $total_item; ?> Item)</span>
                        </div>
                        <form action="penjualan.php" method="POST">
                            <input type="hidden" name="aksi" value="checkout">
                            <button type="submit" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-extrabold py-3 px-8 rounded-xl text-sm shadow-md transition">
                                <i class="fa-solid fa-circle-check mr-1"></i> Selesaikan Transaksi
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>