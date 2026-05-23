<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
 
<aside class="w-64 bg-gray-900 text-white fixed h-full hidden md:flex flex-col z-40 border-r border-gray-800 shadow-md">
    <div class="p-5 border-b border-gray-800 flex items-center space-x-3 text-orange-500 font-bold text-2xl tracking-wider">
        <i class="fa-solid fa-basket-shopping text-2xl"></i>
        <span>Sembako<span class="text-white">Ku</span></span>
    </div>
    
    <div class="px-4 py-3 bg-gray-800/40 text-xs font-semibold text-gray-400 tracking-wider uppercase">
        Menu Utama
    </div>
 
    <nav class="flex-1 px-3 space-y-1">
        <a href="dashboard.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo ($current_page == 'dashboard.php') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
            <i class="fa-solid fa-chart-pie mr-3 text-base"></i> Dashboard
        </a>
        <a href="penjualan.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo ($current_page == 'penjualan.php') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
            <i class="fa-solid fa-cash-register mr-3 text-base"></i> Penjualan (POS)
        </a>
        <a href="stok.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo ($current_page == 'stok.php') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
            <i class="fa-solid fa-boxes-stacked mr-3 text-base"></i> Stok Barang
        </a>
        <a href="laporan.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo ($current_page == 'laporan.php') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
            <i class="fa-solid fa-file-invoice-dollar mr-3 text-base"></i> Laporan Laba
        </a>
        <a href="notifikasi.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 <?php echo ($current_page == 'notifikasi.php') ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'text-gray-300 hover:bg-gray-800 hover:text-white'; ?>">
            <i class="fa-solid fa-bell mr-3 text-base"></i> Notifikasi
        </a>
    </nav>
 
    <div class="p-4 border-t border-gray-800">
        <a href="Proses/logout.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition duration-150">
            <i class="fa-solid fa-right-from-bracket mr-3 text-base"></i> Keluar Sistem
        </a>
    </div>
</aside>
 
 
<header class="md:hidden fixed top-0 left-0 w-full h-16 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-4 z-50 shadow-md">
    <div class="flex items-center space-x-2 text-orange-500 font-bold text-xl">
        <i class="fa-solid fa-basket-shopping"></i>
        <span>Sembako<span class="text-white">Ku</span></span>
    </div>
    <div>
        <button id="hamburger-btn" class="p-2 text-gray-300 hover:text-white focus:outline-none transition">
            <i class="fa-solid fa-bars text-xl" id="hamburger-icon"></i>
        </button>
    </div>
</header>
 
<div id="mobile-nav" class="hidden md:hidden fixed top-16 left-0 w-full bg-gray-900 border-b border-gray-800 z-50 shadow-xl transition-all duration-300">
    <nav class="p-4 space-y-2">
        <a href="dashboard.php" class="block px-4 py-2.5 rounded-lg text-sm <?php echo ($current_page == 'dashboard.php') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
            <i class="fa-solid fa-chart-pie mr-2"></i> Dashboard
        </a>
        <a href="penjualan.php" class="block px-4 py-2.5 rounded-lg text-sm <?php echo ($current_page == 'penjualan.php') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
            <i class="fa-solid fa-cash-register mr-2"></i> Penjualan (POS)
        </a>
        <a href="stok.php" class="block px-4 py-2.5 rounded-lg text-sm <?php echo ($current_page == 'stok.php') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
            <i class="fa-solid fa-boxes-stacked mr-2"></i> Stok Barang
        </a>
        <a href="laporan.php" class="block px-4 py-2.5 rounded-lg text-sm <?php echo ($current_page == 'laporan.php') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
            <i class="fa-solid fa-file-invoice-dollar mr-2"></i> Laporan Laba
        </a>
        <a href="notifikasi.php" class="block px-4 py-2.5 rounded-lg text-sm <?php echo ($current_page == 'notifikasi.php') ? 'bg-orange-500 text-white' : 'text-gray-300 hover:bg-gray-800'; ?>">
            <i class="fa-solid fa-bell mr-2"></i> Notifikasi
        </a>
        <div class="border-t border-gray-800 my-2 pt-2">
            <a href="Proses/logout.php" class="block px-4 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-500/10">
                <i class="fa-solid fa-right-from-bracket mr-2"></i> Keluar
            </a>
        </div>
    </nav>
</div>
 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('hamburger-btn');
        const nav = document.getElementById('mobile-nav');
        const icon = document.getElementById('hamburger-icon');
 
        btn.addEventListener('click', function() {
            nav.classList.toggle('hidden');
            if (nav.classList.contains('hidden')) {
                icon.className = "fa-solid fa-bars text-xl";
            } else {
                icon.className = "fa-solid fa-xmark text-xl";
            }
        });
    });
</script>