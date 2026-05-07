<?php

include "config/koneksi.php";

session_start();

if(!isset($_SESSION['role']) || $_SESSION['role']!='customer'){
    header("location:login.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($conn,
"SELECT products.*, kategori.nama_kategori, brand.nama_brand 
FROM products 
LEFT JOIN kategori ON products.id_kategori=kategori.id_kategori 
LEFT JOIN brand ON products.id_brand=brand.id_brand 
WHERE products.id='$id'");

$row = mysqli_fetch_assoc($data);

$error = "";

if(!$row){
    $error = "Produk tidak ditemukan.";
} elseif($row['stok'] <= 0){
    $error = "Stok habis. Tidak bisa checkout.";
}

if(isset($_POST['checkout'])){

    $id_produk = $_POST['id'];
    $jumlah = $_POST['jumlah'];

    $produk = mysqli_query($conn,
    "SELECT products.*, kategori.nama_kategori, brand.nama_brand 
    FROM products 
    LEFT JOIN kategori ON products.id_kategori=kategori.id_kategori 
    LEFT JOIN brand ON products.id_brand=brand.id_brand 
    WHERE products.id='$id_produk'");

    $p = mysqli_fetch_assoc($produk);
    $stok = $p['stok'];
    $harga = $p['harga'];
    $nama = $p['nama_produk'];

    if($stok <= 0){
        $error = "Stok habis. Tidak bisa checkout.";
    } elseif($jumlah > $stok){
        $error = "Stok tidak cukup.";
    } else {
        $total = $harga * $jumlah;
        $tanggal = date('Y-m-d H:i:s');

        mysqli_query($conn, 
        "INSERT INTO transaksi
        (id_produk,nama_produk,harga,jumlah,total,status,id_user,tanggal)
        VALUES
        ('$id_produk','$nama','$harga','$jumlah','$total','pending','".$_SESSION['id']."','$tanggal')"
        );

        header("Location: dashboard.php");
        exit;
    }
}

if(isset($_POST['tambah_keranjang'])){

    $id_produk = $_POST['id'];
    $jumlah = $_POST['jumlah'];

    $produk = mysqli_query($conn,
    "SELECT products.*, kategori.nama_kategori, brand.nama_brand 
    FROM products 
    LEFT JOIN kategori ON products.id_kategori=kategori.id_kategori 
    LEFT JOIN brand ON products.id_brand=brand.id_brand 
    WHERE products.id='$id_produk'");

    $p = mysqli_fetch_assoc($produk);
    $stok = $p['stok'];

    if($stok <= 0){
        $error = "Stok habis. Tidak bisa tambah ke keranjang.";
    } elseif($jumlah > $stok){
        $error = "Stok tidak cukup.";
    } else {
        // Cek apakah sudah ada di keranjang
        $cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user='".$_SESSION['id']."' AND id_produk='$id_produk'");
        if(mysqli_num_rows($cek) > 0){
            // Update jumlah
            mysqli_query($conn, "UPDATE keranjang SET jumlah=jumlah+$jumlah WHERE id_user='".$_SESSION['id']."' AND id_produk='$id_produk'");
        } else {
            // Insert baru
            mysqli_query($conn, "INSERT INTO keranjang (id_user, id_produk, jumlah) VALUES ('".$_SESSION['id']."', '$id_produk', '$jumlah')");
        }
        header("Location: keranjang.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Produk - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container">
    <div class="breadcrumb mb-4">
        <a href="dashboard.php">Produk</a>
        <span>/</span>
        <span>Checkout</span>
    </div>

    <div class="page-header">
        <h1 class="page-title">Checkout Produk</h1>
    </div>

    <div class="grid-row">
        <!-- Bagian Kiri: Informasi Produk -->
        <div>
            <div class="card">
                <div class="card-header">Detail Produk</div>
                <div class="card-body">
                    <?php if(!empty($row['foto_produk']) && file_exists('uploads/'.$row['foto_produk'])): ?>
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src="uploads/<?php echo $row['foto_produk']; ?>" class="img-product" alt="<?php echo $row['nama_produk']; ?>">
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label">Nama Produk</label>
                        <p class="font-semibold" style="font-size: 16px; color: #0f172a; margin: 0;">
                            <?php echo $row['nama_produk']; ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <p style="color: #334155; margin: 0;">
                            <?php echo !empty($row['nama_kategori']) ? $row['nama_kategori'] : '<span class="text-muted">-</span>'; ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <p style="color: #334155; margin: 0;">
                            <?php echo !empty($row['nama_brand']) ? $row['nama_brand'] : '<span class="text-muted">-</span>'; ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga per Unit</label>
                        <p class="font-semibold text-info" style="font-size: 20px; margin: 0;">
                            Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok Tersedia</label>
                        <p style="margin: 0;">
                            <?php if($row['stok'] > 0): ?>
                                <span class="badge badge-success"><?php echo $row['stok']; ?> buah</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Stok Habis</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan: Form Pembelian -->
        <div>
            <div class="card">
                <div class="card-header">Konfirmasi Pembelian</div>
                <div class="card-body">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger mb-4">
                            <span style="font-size: 18px;">⚠️</span>
                            <div>
                                <strong>Error</strong><br>
                                <?php echo $error; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(empty($error) || ($row && $row['stok'] > 0)): ?>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Jumlah Beli</label>
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input 
                                type="number" 
                                name="jumlah" 
                                class="form-control" 
                                min="1" 
                                max="<?php echo $row['stok']; ?>"
                                value="1"
                                required
                            >
                            <small class="text-muted">Maksimal: <?php echo $row['stok']; ?> buah</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Total Harga</label>
                            <div id="total-harga" class="font-semibold text-info" style="font-size: 24px; padding: 10px; background: #dbeafe; border-radius: 6px; text-align: center;">
                                Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" name="checkout" class="btn btn-success btn-block">
                                ✓ Checkout Langsung
                            </button>
                            <button type="submit" name="tambah_keranjang" class="btn btn-primary btn-block">
                                🛒 Tambah ke Keranjang
                            </button>
                        </div>
                    </form>

                    <script>
                        const harga = <?php echo $row['harga']; ?>;
                        const jumlahInput = document.querySelector('input[name="jumlah"]');
                        const totalHarga = document.getElementById('total-harga');

                        jumlahInput.addEventListener('change', function() {
                            const total = harga * parseInt(this.value);
                            totalHarga.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                        });

                        jumlahInput.addEventListener('input', function() {
                            const total = harga * parseInt(this.value);
                            totalHarga.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
                        });
                    </script>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <span style="font-size: 18px;">❌</span>
                            <div>
                                <strong>Produk Tidak Tersedia</strong><br>
                                Produk ini sedang kehabisan stok.
                            </div>
                        </div>
                        <a href="dashboard.php" class="btn btn-light btn-block">← Kembali ke Produk</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>