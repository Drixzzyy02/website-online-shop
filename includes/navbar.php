<?php
// Navbar Component
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            WebKu
        </div>
        
        <ul class="navbar-menu">
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'customer'): ?>
                <li><a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">Produk</a></li>
                <li><a href="keranjang.php" class="<?php echo $current_page == 'keranjang.php' ? 'active' : ''; ?>">Keranjang</a></li>
                <li><a href="riwayat.php" class="<?php echo $current_page == 'riwayat.php' ? 'active' : ''; ?>">Riwayat</a></li>
                <li><a href="logout.php" class="btn btn-light btn-sm">Logout</a></li>
            <?php else: ?>
                <li><a href="produk.php" class="<?php echo $current_page == 'produk.php' ? 'active' : ''; ?>">Produk</a></li>
                <li><a href="kategori_brand.php" class="<?php echo $current_page == 'kategori_brand.php' ? 'active' : ''; ?>">Kategori & Brand</a></li>
                <li><a href="stok_produk.php" class="<?php echo $current_page == 'stok_produk.php' ? 'active' : ''; ?>">Stok</a></li>
                <li><a href="konfirmasi.php" class="<?php echo $current_page == 'konfirmasi.php' ? 'active' : ''; ?>">Transaksi</a></li>
                <li><a href="logout.php" class="btn btn-light btn-sm">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
