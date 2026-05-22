<?php
// 1. Pastikan Session Dimulai Lebih Awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Hubungkan ke Database
include 'Server/koneksi.php';

// 3. Validasi Login - Jika belum login, arahkan ke halaman login
if (!isset($_SESSION['id'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}

// 4. Hubungkan ke Sidebar & Header Navigasi
include 'sidebar.php';


// Ambil Akumulasi Total Omset Pendapatan Kotor Selama Ini
$total_omset_q = mysqli_query($koneksi, "SELECT SUM(total_bayar) AS total FROM penjualan");
$total_omset   = mysqli_fetch_assoc($total_omset_q)['total'] ?? 0;

// Ambil Akumulasi Pengeluaran Modal (HPP) berdasarkan riwayat detail transaksi
$total_modal_q = mysqli_query($koneksi, "SELECT SUM(dp.jumlah * b.harga_beli) AS total FROM detail_penjualan dp JOIN barang b ON dp.barang_id = b.id");
$total_modal   = mysqli_fetch_assoc($total_modal_q)['total'] ?? 0;

// Rumus Laba Bersih
$total_laba = $total_omset - $total_modal;


// ==========================================
// 6. GENERATE DATA GRAFIK PENJUALAN HARIAN (7 HARI TERAKHIR)
// ==========================================
$hari_tanggal_labels = [];
$omset_rupiah_hari   = [];

// Array bantuan untuk menerjemahkan nama hari ke Bahasa Indonesia
$nama_hari = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu'
];

// Ambil data 7 hari terakhir ke belakang
for ($i = 6; $i >= 0; $i--) {
    $tgl_raw      = date('Y-m-d', strtotime("-$i days"));
    
    // Format label: "Hari, Tanggal Bulan" (Contoh: "Senin, 18 Mei")
    $hari_eng     = date('l', strtotime("-$i days"));
    $hari_indo    = $nama_hari[$hari_eng] ?? $hari_eng;
    $tgl_format   = date('d M', strtotime("-$i days"));
    
    $hari_tanggal_labels[] = $hari_indo . ", " . $tgl_format;
    
    // Ambil data penjualan di tanggal tersebut dari database
    $q_omset = mysqli_query($koneksi, "SELECT SUM(total_bayar) AS total FROM penjualan WHERE DATE(tanggal) = '$tgl_raw'");
    $d_omset = mysqli_fetch_assoc($q_omset);
    $omset_rupiah_hari[] = (int)($d_omset['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - SembakoKu</title>
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
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Laporan Penjualan </h1>
                    <p class="text-sm text-slate-500"></p>
                </div>
                <div class="text-sm font-semibold bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl border border-emerald-100 self-start sm:self-center">
                    <i class="fa-solid fa-chart-line mr-1.5"></i> Pembaruan: Real-time
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Total Pendapatan (Omset)</span>
                        <span class="text-xl font-black text-slate-900">Rp <?php echo number_format($total_omset, 0, ',', '.'); ?></span>
                    </div>
                    <div class="p-3.5 bg-orange-50 text-orange-500 rounded-xl">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Total Modal Barang (HPP)</span>
                        <span class="text-xl font-black text-slate-900">Rp <?php echo number_format($total_modal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="p-3.5 bg-slate-100 text-slate-500 rounded-xl">
                        <i class="fa-solid fa-boxes-packing text-xl"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Total Laba Bersih</span>
                        <span class="text-xl font-black text-emerald-600">Rp <?php echo number_format($total_laba, 0, ',', '.'); ?></span>
                    </div>
                    <div class="p-3.5 bg-emerald-50 text-emerald-500 rounded-xl">
                        <i class="fa-solid fa-money-bill-trend-up text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b pb-4 mb-4 gap-2">
                    <div>
                        <h3 class="font-bold text-slate-900 flex items-center gap-2 text-lg">
                            <i class="fa-solid fa-chart-column text-emerald-500"></i> Grafik Omset Penjualan Berkala
                        </h3>
                        <p class="text-xs text-slate-400">Menampilkan statistik omset harian (Hari, Tanggal) dalam format mata uang Rupiah (Rp).</p>
                    </div>
                    <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-md self-start sm:self-center">7 Hari Terakhir</span>
                </div>
                
                <div class="w-full relative" style="height: 340px;">
                    <canvas id="laporanChartDinamis"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h3 class="font-bold text-slate-900 flex items-center gap-2 text-lg">
                            <i class="fa-solid fa-list-check text-emerald-500"></i> Ringkasan Penjualan Terperinci
                        </h3>
                        <p class="text-xs text-slate-400">Daftar keseluruhan nota transaksi kasir yang sukses tercatat di dalam sistem.</p>
                    </div>
                    <div class="text-xs font-semibold bg-white border px-3 py-1.5 rounded-xl text-slate-600 shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-receipt text-emerald-500"></i> Riwayat Transaksi
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="p-4 pl-6 text-center w-24">ID Nota</th>
                                <th class="p-4">Hari & Tanggal Transaksi</th>
                                <th class="p-4 text-center">Jumlah Produk</th>
                                <th class="p-4 text-right">Total Bayar</th>
                                <th class="p-4 text-right pr-6">Keuntungan Bersih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                            <?php
                            // Query mengambil riwayat penjualan lengkap beserta total item & keuntungan per transaksi secara real-time
                            $query_ringkasan = mysqli_query($koneksi, "
                                SELECT p.id, p.tanggal, p.total_bayar,
                                       SUM(dp.jumlah) AS total_qty,
                                       SUM(dp.jumlah * (b.harga_jual - b.harga_beli)) AS laba_bersih_nota
                                FROM penjualan p
                                LEFT JOIN detail_penjualan dp ON p.id = dp.penjualan_id
                                LEFT JOIN barang b ON dp.barang_id = b.id
                                GROUP BY p.id
                                ORDER BY p.tanggal DESC
                            ");

                            if (mysqli_num_rows($query_ringkasan) == 0):
                            ?>
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-slate-400 font-normal">
                                        <i class="fa-regular fa-folder-open text-3xl block mb-2 text-slate-300"></i>
                                        Belum ditemukan adanya riwayat data ringkasan penjualan.
                                    </td>
                                </tr>
                            <?php
                            else:
                                while ($row = mysqli_fetch_assoc($query_ringkasan)):
                                    $hari_nota_eng  = date('l', strtotime($row['tanggal']));
                                    $hari_nota_indo = $nama_hari[$hari_nota_eng] ?? $hari_nota_eng;
                                    $tgl_nota_indo  = date('d M Y, H:i', strtotime($row['tanggal']));
                            ?>
                                <tr class="hover:bg-slate-50/60 transition duration-150">
                                    <td class="p-4 text-center font-bold text-slate-400">#<?php echo $row['id']; ?></td>
                                    <td class="p-4">
                                        <span class="block text-slate-900"><?php echo $hari_nota_indo . ", " . $tgl_nota_indo; ?> WIB</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold">
                                            <?php echo $row['total_qty'] ?? 0; ?> Pcs
                                        </span>
                                    </td>
                                    <td class="p-4 text-right text-slate-900 font-bold">
                                        Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="p-4 text-right pr-6 text-emerald-600 font-black">
                                        Rp <?php echo number_format($row['laba_bersih_nota'] ?? 0, 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            endif; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <script>
        const ctxLaporan = document.getElementById('laporanChartDinamis').getContext('2d');
        new Chart(ctxLaporan, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($hari_tanggal_labels); ?>, // Format: "Senin, 18 Mei"
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: <?php echo json_encode($omset_rupiah_hari); ?>, // Nilai Pendapatan Toko
                    backgroundColor: '#10b981', // Hijau Emerald sesuai gambar acuan
                    hoverBackgroundColor: '#059669',
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let val = context.raw;
                                return ' Pendapatan: Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            // Format rupiah pada Sumbu Y grafik
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            },
                            font: { size: 10, weight: '500' },
                            color: '#64748b'
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { 
                            font: { size: 11, weight: '600' },
                            color: '#334155'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>