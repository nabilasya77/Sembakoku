<?php
// 1. Pastikan Session Dimulai Lebih Awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Hubungkan ke Database
include 'Server/koneksi.php';

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
// 4. Hubungkan ke Sidebar & Header Navigasi
include 'sidebar.php';



// Card 1: Total Penjualan Hari Ini
$penjualan_hari_ini_q = mysqli_query($koneksi, "SELECT SUM(total_bayar) AS total FROM penjualan WHERE DATE(tanggal) = CURDATE()");
$penjualan_hari_ini   = mysqli_fetch_assoc($penjualan_hari_ini_q)['total'] ?? 0;

// Card 2: Jumlah Transaksi Hari Ini
$transaksi_hari_ini_q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM penjualan WHERE DATE(tanggal) = CURDATE()");
$transaksi_hari_ini   = mysqli_fetch_assoc($transaksi_hari_ini_q)['total'] ?? 0;

// Card 3: Total Keuntungan Hari Ini (Laba Bersih = Harga Jual - Harga Beli)
$keuntungan_hari_ini_q = mysqli_query($koneksi, "
    SELECT SUM(dp.jumlah * (b.harga_jual - b.harga_beli)) AS total 
    FROM detail_penjualan dp 
    JOIN barang b ON dp.barang_id = b.id 
    JOIN penjualan p ON dp.penjualan_id = p.id 
    WHERE DATE(p.tanggal) = CURDATE()
");
$keuntungan_hari_ini   = mysqli_fetch_assoc($keuntungan_hari_ini_q)['total'] ?? 0;

// Card 4: Stok Hampir Habis (Peringatan di bawah 10 pcs)
$stok_hampir_habis_q = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang WHERE stok < 10");
$stok_hampir_habis   = mysqli_fetch_assoc($stok_hampir_habis_q)['total'] ?? 0;


// ==========================================
// 6. GENERATE DATA GRAFIK PENJUALAN 7 HARI TERAKHIR
// ==========================================
$hari_labels = [];
$omset_hari  = [];

// Looping untuk memastikan seluruh 7 hari terakhir muncul secara berurutan meskipun omsetnya 0
for ($i = 6; $i >= 0; $i--) {
    $tgl_raw   = date('Y-m-d', strtotime("-$i days"));
    $tgl_label = date('d M', strtotime("-$i days")); // Contoh: "22 Mei"
    
    $hari_labels[] = $tgl_label;
    
    // Ambil data penjualan di tanggal tersebut
    $q_omset = mysqli_query($koneksi, "SELECT SUM(total_bayar) AS total FROM penjualan WHERE DATE(tanggal) = '$tgl_raw'");
    $d_omset = mysqli_fetch_assoc($q_omset);
    $omset_hari[] = (int)($d_omset['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Utama - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased">
    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="md:w-64 flex-shrink-0"></div>

        <main class="flex-1 p-6 md:p-8 mt-16 md:mt-0 overflow-x-hidden">
            
            <div class="mb-6 border-b pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Beranda</h1>
                    <p class="text-sm text-slate-500">Memantau aktivitas penjualan kasir, grafik harian, dan ringkasan stok toko sembako.</p>
                </div>
                <div class="text-sm font-semibold bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl border border-emerald-100 self-start sm:self-center">
                    <i class="fa-regular fa-calendar-days mr-1.5"></i> Tanggal: <?php echo date('d M Y'); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Total Penjualan Hari Ini</span>
                        <span class="text-xl font-black text-slate-900">Rp <?php echo number_format($penjualan_hari_ini, 0, ',', '.'); ?></span>
                    </div>
                    <div class="p-3.5 bg-orange-50 text-orange-500 rounded-xl">
                        <i class="fa-solid fa-money-bill-wave text-xl"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Jumlah Transaksi</span>
                        <span class="text-xl font-black text-slate-900"><?php echo $transaksi_hari_ini; ?> <span class="text-xs font-normal text-slate-400">Transaksi</span></span>
                    </div>
                    <div class="p-3.5 bg-blue-50 text-blue-500 rounded-xl">
                        <i class="fa-solid fa-receipt text-xl"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Total Keuntungan</span>
                        <span class="text-xl font-black text-emerald-600">Rp <?php echo number_format($keuntungan_hari_ini, 0, ',', '.'); ?></span>
                    </div>
                    <div class="p-3.5 bg-emerald-50 text-emerald-500 rounded-xl">
                        <i class="fa-solid fa-arrow-trend-up text-xl"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between <?php echo ($stok_hampir_habis > 0) ? 'bg-rose-50/30 border-rose-200' : ''; ?>">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Stok Hampir Habis</span>
                        <span class="text-xl font-black <?php echo ($stok_hampir_habis > 0) ? 'text-rose-600' : 'text-slate-900'; ?>"><?php echo $stok_hampir_habis; ?> <span class="text-xs font-normal text-slate-400">Item</span></span>
                    </div>
                    <div class="p-3.5 <?php echo ($stok_hampir_habis > 0) ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500'; ?> rounded-xl">
                        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <h3 class="font-bold text-slate-900 flex items-center gap-2 mb-4">
                            <i class="fa-solid fa-bolt text-amber-500"></i> Menu Navigasi Cepat
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <a href="penjualan.php" class="p-4 bg-slate-50 border border-slate-100 rounded-xl hover:border-orange-200 hover:bg-orange-50/30 transition text-center group">
                                <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-700 block">Kasir Baru</span>
                            </a>
                            <a href="stok.php" class="p-4 bg-slate-50 border border-slate-100 rounded-xl hover:border-blue-200 hover:bg-blue-50/30 transition text-center group">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-700 block">Data Stok</span>
                            </a>
                            <a href="laporan.php" class="p-4 bg-slate-50 border border-slate-100 rounded-xl hover:border-emerald-200 hover:bg-emerald-50/30 transition text-center group">
                                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-700 block">Laporan Laba</span>
                            </a>
                            <a href="notifikasi.php" class="p-4 bg-slate-50 border border-slate-100 rounded-xl hover:border-rose-200 hover:bg-rose-50/30 transition text-center group">
                                <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-105 transition">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-700 block">Notifikasi</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-chart-column text-emerald-500"></i> Grafik Penjualan 7 Hari Terakhir
                            </h3>
                            <span class="text-xs text-slate-400 font-medium">Statistik Harian</span>
                        </div>
                        <div class="w-full relative" style="height: 280px;">
                            <canvas id="grafikMingguanHijau"></canvas>
                        </div>
                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between h-full min-h-[420px]">
                    <div>
                        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-slate-500"></i> Transaksi Terbaru
                            </h3>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold text-[10px] rounded-md uppercase tracking-wider">Terbaru</span>
                        </div>

                        <div class="overflow-y-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-400 text-[10px] uppercase font-semibold tracking-wider">
                                        <th class="p-3 pl-5 text-center w-12">ID</th>
                                        <th class="p-3">Waktu</th>
                                        <th class="p-3 text-right pr-5">Total Bayar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                                    <?php
                                    $recent_transactions_q = mysqli_query($koneksi, "SELECT * FROM penjualan ORDER BY id DESC LIMIT 6");
                                    if (mysqli_num_rows($recent_transactions_q) == 0):
                                    ?>
                                        <tr>
                                            <td colspan="3" class="p-8 text-center text-slate-400 font-normal">Belum ada data transaksi tersimpan.</td>
                                        </tr>
                                    <?php
                                    else:
                                        while ($tx = mysqli_fetch_assoc($recent_transactions_q)):
                                    ?>
                                        <tr class="hover:bg-slate-50/80 transition duration-150">
                                            <td class="p-3 text-center text-slate-400">#<?php echo $tx['id']; ?></td>
                                            <td class="p-3 text-slate-500"><?php echo date('d M, H:i', strtotime($tx['tanggal'])); ?> WIB</td>
                                            <td class="p-3 text-right pr-5 text-emerald-600 font-bold">Rp <?php echo number_format($tx['total_bayar'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    endif; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                        <a href="laporan.php" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition flex items-center justify-center gap-1">
                            Lihat Laporan Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        const ctxMingguan = document.getElementById('grafikMingguanHijau').getContext('2d');
        new Chart(ctxMingguan, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($hari_labels); ?>, // Berisi tanggal lengkap (Contoh: "22 Mei")
                datasets: [{
                    label: 'Pendapatan Toko (Rp)',
                    data: <?php echo json_encode($omset_hari); ?>,
                    backgroundColor: '#10b981', // Hijau Emerald (Sesuai request gambar acuan)
                    hoverBackgroundColor: '#059669',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: { size: 10 }
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '500' } }
                    }
                }
            }
        });
    </script>
</body>
</html>