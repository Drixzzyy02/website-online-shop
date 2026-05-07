<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - WebKu Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
    header("location:login.php");
    exit;
}

include "config/koneksi.php";
include "includes/navbar.php";

/* =====================
KONFIRMASI TRANSAKSI
===================== */

if(isset($_GET['konfirmasi'])){
    $id=$_GET['konfirmasi'];

    // Ambil data transaksi untuk kurangi stok
    $transaksi = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi='$id'");
    $t = mysqli_fetch_assoc($transaksi);

    if($t){
        $id_produk = $t['id_produk'];
        $jumlah = $t['jumlah'];

        // Kurangi stok produk
        mysqli_query($conn, "UPDATE products SET stok = stok - $jumlah WHERE id='$id_produk'");

        // Update status transaksi
        mysqli_query($conn,"
        UPDATE transaksi 
        SET status='disetujui'
        WHERE id_transaksi='$id'
        ");
    }

    header("location:produk.php");
    exit;
}

if(isset($_GET['tolak'])){
    $id = $_GET['tolak'];
    mysqli_query($conn,"UPDATE transaksi SET status='ditolak' WHERE id_transaksi='$id'");
    header("location:produk.php");
    exit;
}

/* =====================
AMBIL KATEGORI & BRAND
===================== */

$kategori=mysqli_query($conn,"
SELECT * FROM kategori
ORDER BY nama_kategori ASC
");

$brand=mysqli_query($conn,"
SELECT * FROM brand
ORDER BY nama_brand ASC
");

/* =====================
FILTER PRODUK
===================== */

$where=[];

if(isset($_GET['cari']) && $_GET['cari']!=''){
$cari=$_GET['cari'];
$where[]="products.nama_produk LIKE '%$cari%'";
}

if(isset($_GET['kategori']) && $_GET['kategori']!=''){
$kategori_id=$_GET['kategori'];
$where[]="products.id_kategori='$kategori_id'";
}

if(isset($_GET['brand']) && $_GET['brand']!=''){
$brand_id=$_GET['brand'];
$where[]="products.id_brand='$brand_id'";
}

$where_sql="";

if(count($where)>0){
$where_sql="WHERE ".implode(" AND ",$where);
}

/* =====================
QUERY PRODUK
===================== */

$data=mysqli_query($conn,"
SELECT 
products.*,
IFNULL(kategori.nama_kategori,'-') as nama_kategori,
IFNULL(brand.nama_brand,'-') as nama_brand

FROM products

LEFT JOIN kategori 
ON products.id_kategori=kategori.id_kategori

LEFT JOIN brand
ON products.id_brand=brand.id_brand

$where_sql

ORDER BY products.id DESC
");

/* =====================
TRANSAKSI PENDING
===================== */

$pending=mysqli_query($conn,"
SELECT transaksi.*, products.nama_produk as nama_produk_transaksi
FROM transaksi
LEFT JOIN products ON transaksi.id_produk = products.id
WHERE status='pending'
ORDER BY id_transaksi DESC
");

?>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Kelola Produk</h1>
        <p class="page-subtitle">Manage semua produk di toko Anda</p>
    </div>

    <div class="grid-row">
        <!-- Main Content -->
        <div>
            <div class="card">
                <div class="card-header flex-between">
                    <span>Data Produk</span>
                    <a href="tambah_produk.php" class="btn btn-primary btn-sm">+ Tambah Produk</a>
                </div>
                
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="form-row">
                            <input type="text" name="cari" class="form-control" placeholder="Cari produk..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                            
                            <select name="kategori" class="form-control">
                                <option value="">Semua Kategori</option>
                                <?php
                                // Reset kategori query
                                mysqli_data_seek($kategori, 0);
                                while($k = mysqli_fetch_array($kategori)){ ?>
                                    <option value="<?php echo $k['id_kategori']; ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori']) ? 'selected' : ''; ?>>
                                        <?php echo $k['nama_kategori']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            
                            <select name="brand" class="form-control">
                                <option value="">Semua Brand</option>
                                <?php
                                // Reset brand query
                                mysqli_data_seek($brand, 0);
                                while($b = mysqli_fetch_array($brand)){ ?>
                                    <option value="<?php echo $b['id_brand']; ?>" <?php echo (isset($_GET['brand']) && $_GET['brand'] == $b['id_brand']) ? 'selected' : ''; ?>>
                                        <?php echo $b['nama_brand']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </form>

                    <div class="product-grid">
                        <?php
                        $no = 1;
                        while($row = mysqli_fetch_assoc($data)){
                        ?>
                        <div class="product-card">
                            <!-- Gambar Produk -->
                            <div>
                                <?php 
                                if($row['foto_produk'] && file_exists("uploads/".$row['foto_produk'])){
                                    echo "<img src='uploads/".$row['foto_produk']."' alt='".$row['nama_produk']."' class='product-image'>";
                                } else {
                                    echo "<div class='product-image-empty'>Tidak ada foto</div>";
                                }
                                ?>
                            </div>

                            <!-- Info Produk -->
                            <div class="product-info">
                                <h3 class="product-name"><?php echo $row['nama_produk']; ?></h3>
                                
                                <div class="product-category">
                                    <?php echo $row['nama_kategori'] . ' / ' . $row['nama_brand']; ?>
                                </div>

                                <!-- Harga -->
                                <div class="product-price">Rp <?php echo number_format($row['harga'],0,",","."); ?></div>

                                <!-- Stok -->
                                <div class="product-stock">
                                    <span class="badge" style="background-color: <?php echo ($row['stok'] > 0) ? '#22c55e' : '#ef4444'; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Stok: <?php echo $row['stok']; ?></span>
                                </div>

                                <!-- Tombol Aksi -->
                                <div class="product-actions">
                                    <a href="edit_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                    <a href="hapus_produk.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus produk?')">Hapus</a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

        </div>
    </div>
</div>

        <!-- KANAN TRANSAKSI -->
        <aside>
            <div class="card">
    <div class="card-header">Transaksi Pending</div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($t = mysqli_fetch_assoc($pending)){ ?>
                        <tr>
                            <td><?php echo !empty($t['nama_produk_transaksi']) ? $t['nama_produk_transaksi'] : 'Produk sudah dihapus'; ?></td>
                            <td>Rp <?php echo number_format($t['total'],0,",","."); ?></td>
                            <td>
                                <a class="btn btn-success btn-sm" href="produk.php?konfirmasi=<?php echo $t['id_transaksi']; ?>" onclick="return confirm('Konfirmasi transaksi ini?')">Konfirmasi</a>
                                <a class="btn btn-danger btn-sm" href="produk.php?tolak=<?php echo $t['id_transaksi']; ?>" onclick="return confirm('Tolak transaksi ini?')">Tolak</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
        </aside>
    </div>
</div>
</body>
</html>