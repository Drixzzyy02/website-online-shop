<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php  

session_start();

if(!isset($_SESSION['role']) || $_SESSION['role']!='customer'){
    header("location:login.php");
    exit;
}

include "config/koneksi.php";
include "includes/navbar.php";

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
$brand = mysqli_query($conn, "SELECT * FROM brand ORDER BY nama_brand ASC");

$where = [];

if (isset($_GET['cari']) && $_GET['cari'] != '') {
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $where[] = "products.nama_produk LIKE '%$cari%'";
}

if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kategori_id = (int)$_GET['kategori'];
    $where[] = "products.id_kategori = $kategori_id";
}

if (isset($_GET['brand']) && $_GET['brand'] != '') {
    $brand_id = (int)$_GET['brand'];
    $where[] = "products.id_brand = $brand_id";
}

$where_sql = '';
if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$data = mysqli_query($conn, "
SELECT products.*, kategori.nama_kategori, brand.nama_brand 
FROM products 
LEFT JOIN kategori ON products.id_kategori=kategori.id_kategori 
LEFT JOIN brand ON products.id_brand=brand.id_brand 
$where_sql
ORDER BY products.id DESC
");  

?>  

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Daftar Produk</h1>
        <p class="page-subtitle">Pilih produk yang ingin Anda beli</p>
    </div>

    <form method="GET" class="mb-4">
        <div class="form-row">
            <input type="text" name="cari" class="form-control" placeholder="Cari produk..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">

            <select name="kategori" class="form-control">
                <option value="">Semua Kategori</option>
                <?php while($k = mysqli_fetch_assoc($kategori)){ ?>
                    <option value="<?php echo $k['id_kategori']; ?>" <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori']) ? 'selected' : ''; ?>>
                        <?php echo $k['nama_kategori']; ?>
                    </option>
                <?php } ?>
            </select>

            <select name="brand" class="form-control">
                <option value="">Semua Brand</option>
                <?php while($b = mysqli_fetch_assoc($brand)){ ?>
                    <option value="<?php echo $b['id_brand']; ?>" <?php echo (isset($_GET['brand']) && $_GET['brand'] == $b['id_brand']) ? 'selected' : ''; ?>>
                        <?php echo $b['nama_brand']; ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <div class="card">
        <div class="card-header flex-between">
            <span>Semua Produk</span>
            <a href="keranjang.php" class="btn btn-primary btn-sm">
                <span>🛒</span> Keranjang
            </a>
        </div>
        
        <div class="card-body">
            <div class="product-grid">
                <?php while($row = mysqli_fetch_assoc($data)){ ?>  
                <div class="product-card">
                    <!-- Gambar Produk -->
                    <div>
                        <?php 
                        if(!empty($row['foto_produk']) && file_exists('uploads/'.$row['foto_produk'])){
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
                            <?php 
                            $category = !empty($row['nama_kategori']) ? $row['nama_kategori'] : '-';
                            $brand = !empty($row['nama_brand']) ? $row['nama_brand'] : '';
                            echo $category . ($brand ? ' / ' . $brand : '');
                            ?>
                        </div>

                        <!-- Rating -->
                        <div class="product-rating">
                            <span class="product-rating-stars">★★★★☆</span>
                            <span class="product-rating-value">4.9</span>
                        </div>

                        <!-- Harga -->
                        <div class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>

                        <!-- Stok -->
                        <div class="product-stock">
                            <?php if($row['stok'] > 0){ ?>
                                <span class="badge badge-success">Stok: <?php echo $row['stok']; ?></span>
                            <?php } else { ?>
                                <span class="badge badge-danger">Stok Habis</span>
                            <?php } ?>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="product-actions">
                            <?php if($row['stok'] > 0){ ?>
                                <a href="checkout.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Beli</a>
                            <?php } else { ?>
                                <button class="btn btn-secondary" disabled>Habis</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>  
            </div>
        </div>
    </div>
</div>

</body>
</html>