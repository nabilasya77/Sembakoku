<?php
include 'Server/koneksi.php';

// Pengecekan login menggunakan COOKIE
if (!isset($_COOKIE['login']) || $_COOKIE['login'] !== "true") {
    header("Location: login.php?pesan=belum_login");
    exit;
}

// Proses Simpan Tambah / Edit Barang
if (isset($_POST['simpan'])) {
    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok        = (int)$_POST['stok'];
    $satuan      = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga_beli  = (int)$_POST['harga_beli'];
    $harga_jual  = (int)$_POST['harga_jual'];

    if ($_POST['id_barang'] == "") {
        // FIX: Generate id manual karena DB tidak support AUTO_INCREMENT
        $last_q   = mysqli_query($koneksi, "SELECT MAX(id) as max_id FROM barang");
        $last_row = mysqli_fetch_assoc($last_q);
        $new_id   = ($last_row['max_id'] !== null ? (int)$last_row['max_id'] : 0) + 1;

        $query = "INSERT INTO barang (id, nama_barang, kategori, stok, satuan, harga_beli, harga_jual)
                  VALUES ('$new_id', '$nama_barang', '$kategori', '$stok', '$satuan', '$harga_beli', '$harga_jual')";
    } else {
        $id_barang = (int)$_POST['id_barang'];
        $query = "UPDATE barang SET nama_barang='$nama_barang', kategori='$kategori', stok='$stok', satuan='$satuan', harga_beli='$harga_beli', harga_jual='$harga_jual' WHERE id='$id_barang'";
    }
    mysqli_query($koneksi, $query);
    header("Location: stok.php");
    exit;
}

$edit_data = ['id' => '', 'nama_barang' => '', 'kategori' => '', 'stok' => '', 'satuan' => '', 'harga_beli' => '', 'harga_jual' => ''];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_edit = (int)$_GET['id'];
    $q_edit  = mysqli_query($koneksi, "SELECT * FROM barang WHERE id = '$id_edit'");
    if (mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}

include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Barang - SembakoKu</title>
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
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Stok Produk</h1>
                <p class="text-sm text-slate-500">Kelola kuantitas ketersediaan komoditas barang dagang, harga modal, nilai harga jual toko.</p>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid <?php echo $edit_data['id'] ? 'fa-pen-to-square text-orange-500' : 'fa-circle-plus text-green-500'; ?>"></i>
                        <?php echo $edit_data['id'] ? 'Edit Barang' : 'Tambah Barang Baru'; ?>
                    </h2>
                    <form action="stok.php" method="POST" class="space-y-4">
                        <input type="hidden" name="id_barang" value="<?php echo $edit_data['id']; ?>">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Nama Produk</label>
                            <input type="text" name="nama_barang" required value="<?php echo htmlspecialchars($edit_data['nama_barang']); ?>" placeholder="Minyak Goreng 1L" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:outline-none focus:border-orange-500 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Kategori</label>
                                <input type="text" name="kategori" required value="<?php echo htmlspecialchars($edit_data['kategori']); ?>" placeholder="Sembako" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Satuan</label>
                                <input type="text" name="satuan" required value="<?php echo htmlspecialchars($edit_data['satuan']); ?>" placeholder="Pcs" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Jumlah Stok</label>
                            <input type="number" name="stok" required value="<?php echo $edit_data['stok']; ?>" placeholder="0" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Harga Beli (Rp)</label>
                                <input type="number" name="harga_beli" required value="<?php echo $edit_data['harga_beli']; ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-slate-400 mb-1.5">Harga Jual (Rp)</label>
                                <input type="number" name="harga_jual" required value="<?php echo $edit_data['harga_jual']; ?>" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" name="simpan" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 rounded-xl transition text-sm">Simpan Produk</button>
                            <?php if ($edit_data['id']): ?>
                            <a href="stok.php" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition text-sm">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-slate-400 text-[11px] uppercase font-semibold">
                                <th class="p-4 pl-6 text-center w-12">No</th>
                                <th class="p-4">Nama Barang</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4 text-center">Stok</th>
                                <th class="p-4">Harga Beli</th>
                                <th class="p-4 text-orange-600">Harga Jual</th>
                                <th class="p-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                            <?php
                            $no  = 1;
                            $res = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id DESC");
                            while ($row = mysqli_fetch_assoc($res)):
                            ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 pl-6 text-center text-slate-400"><?php echo $no++; ?></td>
                                <td class="p-4 font-bold text-slate-900"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                <td class="p-4"><span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded-lg"><?php echo htmlspecialchars($row['kategori']); ?></span></td>
                                <td class="p-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo ($row['stok'] < 10) ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'; ?>">
                                        <?php echo $row['stok'] . ' ' . $row['satuan']; ?>
                                    </span>
                                </td>
                                <td class="p-4 text-slate-500">Rp <?php echo number_format($row['harga_beli'], 0, ',', '.'); ?></td>
                                <td class="p-4 text-orange-600 font-bold">Rp <?php echo number_format($row['harga_jual'], 0, ',', '.'); ?></td>
                                <td class="p-4 text-center flex justify-center gap-2">
                                    <a href="stok.php?action=edit&id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="hapus.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus produk ini?')" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash-can"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>