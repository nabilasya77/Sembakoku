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


// =========================================================================
// AKTIVITAS BARU: PROSES FITUR AKSI HAPUS SEMUA LOG NOTIFIKASI
// =========================================================================
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_semua') {
    $hapus_log = mysqli_query($koneksi, "DELETE FROM notifikasi");
    if ($hapus_log) {
        echo "<script>window.location='notifikasi.php';</script>";
        exit;
    }
}


// =========================================================================
// SCRIPT DETEKSI & SINKRONISASI NOTIFIKASI OTOMATIS (REAL-TIME)
// =========================================================================

// A. SINKRONISASI PEMBERSIHAN: Hapus notifikasi lama jika barangnya sudah di-restock (Stok >= 5)
$q_aman = mysqli_query($koneksi, "SELECT nama_barang FROM barang WHERE stok >= 5");
if ($q_aman) {
    while ($row_aman = mysqli_fetch_assoc($q_aman)) {
        $nama_aman = mysqli_real_escape_string($koneksi, $row_aman['nama_barang']);
        mysqli_query($koneksi, "DELETE FROM notifikasi WHERE tipe = 'stok_menipis' AND pesan LIKE 'Stok $nama_aman sisa%'");
    }
}

// B. SINKRONISASI PENAMBAHAN: Cek ulang semua produk yang stoknya saat ini di bawah 5
$q_cek = mysqli_query($koneksi, "SELECT * FROM barang WHERE stok < 5");
if ($q_cek) {
    while ($r = mysqli_fetch_assoc($q_cek)) {
        $msg  = "Stok " . $r['nama_barang'] . " sisa " . $r['stok'] . " " . $r['satuan'] . "!";
        $tipe = "stok_menipis";
        
        $cek_notif = mysqli_query($koneksi, "SELECT * FROM notifikasi WHERE pesan = '$msg'");
        if (mysqli_num_rows($cek_notif) == 0) {
            mysqli_query($koneksi, "INSERT INTO notifikasi (pesan, tipe) VALUES ('$msg', '$tipe')");
        }
    }
}

// C. AMBIL DATA NOTIFIKASI TERBARU
$q_notif = mysqli_query($koneksi, "SELECT * FROM notifikasi ORDER BY id DESC");
$total_notif = mysqli_num_rows($q_notif);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Sistem - SembakoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50/50 text-slate-800 antialiased">

    <div class="flex flex-col md:flex-row min-h-screen">
        <div class="md:w-64 flex-shrink-0"></div>

        <main class="flex-1 p-6 md:p-8 mt-16 md:mt-0 overflow-x-hidden">
            
            <div class="mb-6 border-b pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Notifikasi Sistem</h1>
                </div>
                
                <div class="flex items-center gap-2 self-start sm:self-center">
                    <div class="text-xs font-bold <?php echo $total_notif > 0 ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-100 text-slate-600 border border-slate-200'; ?> px-3 py-2 rounded-xl flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-envelope-open-text"></i> Total Log: <?php echo $total_notif; ?> Pesan
                    </div>
                    
                    <?php if ($total_notif > 0): ?>
                        <a href="notifikasi.php?aksi=hapus_semua" onclick="return confirm('Apakah Anda yakin ingin membersihkan seluruh log pemberitahuan saat ini?')" class="text-xs font-bold bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-xl shadow-sm transition duration-150 flex items-center gap-1">
                            <i class="fa-solid fa-trash-can"></i> Bersihkan
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-sm font-bold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-bell text-orange-500"></i> Kotak Masuk Pemberitahuan
                    </span>
                    <span class="text-xs font-semibold bg-emerald-50 text-emerald-600 px-2 py-1 rounded-md">
                        Status: Aktif
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    <?php 
                    if ($total_notif == 0): 
                    ?>
                        <div class="p-12 text-center text-slate-400 font-normal">
                            <div class="inline-flex p-4 bg-emerald-50 text-emerald-500 rounded-full mb-3">
                                <i class="fa-solid fa-shield-heart text-3xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Aman! Tidak ada notifikasi saat ini.</p>
                            <p class="text-xs text-slate-400 mt-1">Semua persediaan barang di tokomu berada di atas batas minimal.</p>
                        </div>
                    <?php 
                    else:
                        while($r_notif = mysqli_fetch_assoc($q_notif)):
                            $bg_icon    = 'bg-red-50 text-red-500';
                            $border_box = 'border-l-4 border-l-red-500';
                            $badge      = 'bg-red-50 text-red-600 border border-red-100';
                            $badge_txt  = 'Stok Tipis';

                            if($r_notif['tipe'] == 'info') {
                                $bg_icon    = 'bg-blue-50 text-blue-500';
                                $border_box = 'border-l-4 border-l-blue-500';
                                $badge      = 'bg-blue-50 text-blue-600 border border-blue-100';
                                $badge_txt  = 'Info';
                            }
                    ?>
                        <div class="p-5 flex items-start space-x-4 hover:bg-slate-50/60 transition duration-150 <?php echo $border_box; ?>">
                            <div class="p-3 <?php echo $bg_icon; ?> rounded-xl mt-0.5 flex-shrink-0 shadow-sm">
                                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 leading-snug tracking-tight">
                                    <?php echo htmlspecialchars($r_notif['pesan']); ?>
                                </p>
                                <span class="text-xs text-slate-400 block mt-1.5 font-medium">
                                    <i class="fa-regular fa-clock mr-1 text-slate-300"></i> 
                                    <?php 
                                        $timestamp = isset($r_notif['created_at']) ? $r_notif['created_at'] : date('Y-m-d H:i:s');
                                        echo date('d M Y, H:i', strtotime($timestamp)); 
                                    ?> WIB
                                </span>
                            </div>
                            
                            <span class="text-[10px] px-2.5 py-1 <?php echo $badge; ?> rounded-lg font-bold uppercase tracking-wider self-center flex-shrink-0 hidden sm:inline-block">
                                <?php echo $badge_txt; ?>
                            </span>
                        </div>
                    <?php 
                        endwhile; 
                    endif; 
                    ?>
                </div>
            </div>

        </main>
    </div>

</body>
</html>