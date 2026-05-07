<?php

include "config/koneksi.php";

session_start();

if(!isset($_SESSION['role']) || $_SESSION['role']!='customer'){
    header("location:login.php");
    exit;
}

// Handle hapus item
if(isset($_GET['hapus'])){
    $id_keranjang = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id='$id_keranjang' AND id_user='".$_SESSION['id']."'");
    header("Location: keranjang.php");
    exit;
}

// Handle checkout semua
if(isset($_POST['checkout_semua'])){
    $keranjang = mysqli_query($conn, "SELECT keranjang.*, products.nama_produk, products.harga, products.stok FROM keranjang LEFT JOIN products ON keranjang.id_produk=products.id WHERE keranjang.id_user='".$_SESSION['id']."'");

    $error = "";
    while($item = mysqli_fetch_assoc($keranjang)){
        if($item['jumlah'] > $item['stok']){
            $error = "Stok tidak cukup untuk ".$item['nama_produk'];
            break;
        }
    }

    if(empty($error)){
        mysqli_data_seek($keranjang, 0); // Reset pointer
        $tanggal = date('Y-m-d H:i:s');
        while($item = mysqli_fetch_assoc($keranjang)){
            $total = $item['harga'] * $item['jumlah'];
            mysqli_query($conn, 
            "INSERT INTO transaksi
            (id_produk,nama_produk,harga,jumlah,total,status,id_user,tanggal)
            VALUES
            ('".$item['id_produk']."','".$item['nama_produk']."','".$item['harga']."','".$item['jumlah']."','$total','pending','".$_SESSION['id']."','$tanggal')"
            );

            // Kurangi stok
            mysqli_query($conn, "UPDATE products SET stok=stok-".$item['jumlah']." WHERE id='".$item['id_produk']."'");
        }

        // Hapus semua dari keranjang
        mysqli_query($conn, "DELETE FROM keranjang WHERE id_user='".$_SESSION['id']."'");

        header("Location: dashboard.php");
        exit;
    }
}

$data = mysqli_query($conn, "
SELECT keranjang.*, products.nama_produk, products.harga, products.foto_produk, kategori.nama_kategori, brand.nama_brand 
FROM keranjang 
LEFT JOIN products ON keranjang.id_produk=products.id 
LEFT JOIN kategori ON products.id_kategori=kategori.id_kategori 
LEFT JOIN brand ON products.id_brand=brand.id_brand 
WHERE keranjang.id_user='".$_SESSION['id']."'
");

$total_keseluruhan = 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="container">
    <div class="breadcrumb mb-4">
        <a href="dashboard.php">Produk</a>
        <span>/</span>
        <span>Keranjang</span>
    </div>

    <div class="page-header">
        <h1 class="page-title">Keranjang Belanja</h1>
        <p class="page-subtitle">Kelola produk yang ingin Anda beli</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger mb-4">
            <span style="font-size: 18px;">⚠️</span>
            <div>
                <strong>Perhatian</strong><br>
                <?php echo $error; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if(mysqli_num_rows($data) > 0): ?>

    <div class="grid-row">
        <!-- Tabel Keranjang -->
        <div>
            <div class="card">
                <div class="card-header">Item di Keranjang</div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reset data pointer
                                mysqli_data_seek($data, 0);
                                while($row = mysqli_fetch_assoc($data)){ 
                                    $total = $row['harga'] * $row['jumlah'];
                                    $total_keseluruhan += $total;
                                ?>
                                <tr>
                                    <td>
                                        <?php 
                                        if(!empty($row['foto_produk']) && file_exists('uploads/'.$row['foto_produk'])){
                                            echo "<img src='uploads/".$row['foto_produk']."' class='img-thumbnail' alt='".$row['nama_produk']."'>";
                                        } else {
                                            echo "<div style='width:60px;height:60px;background:#e2e8f0;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:12px;'>-</div>";
                                        }
                                        ?>
                                    </td>
                                    <td class="font-medium"><?php echo $row['nama_produk']; ?></td>
                                    <td><?php echo !empty($row['nama_kategori']) ? $row['nama_kategori'] : '<span class="text-muted">-</span>'; ?></td>
                                    <td class="text-info">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge badge-info"><?php echo $row['jumlah']; ?></span>
                                    </td>
                                    <td class="font-semibold">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                                    <td>
                                        <a href="keranjang.php?hapus=<?php echo $row['id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Hapus dari keranjang?')">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembelian -->
        <div>
            <form method="POST" class="card">
                <div class="card-header">Ringkasan Pembelian</div>
                <div class="card-body">
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span class="text-muted">Subtotal:</span>
                            <span class="font-semibold">Rp <?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span class="text-muted">PPN (0%):</span>
                            <span class="font-semibold">Rp 0</span>
                        </div>
                        <div style="border-top: 2px solid #e2e8f0; padding-top: 10px; display: flex; justify-content: space-between;">
                            <span class="font-semibold text-info">Total Pembayaran:</span>
                            <span class="font-bold text-info" style="font-size: 20px;">Rp <?php echo number_format($total_keseluruhan, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <button type="submit" name="checkout_semua" class="btn btn-success btn-block">
                        ✓ Checkout Semua
                    </button>
                    <a href="dashboard.php" class="btn btn-light btn-block mt-3">
                        ← Lanjut Belanja
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php else: ?>

    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-state-icon">🛒</div>
                <h3 class="empty-state-title">Keranjang Anda Kosong</h3>
                <p class="empty-state-text">Mulai berbelanja sekarang dan tambahkan produk favoritmu ke keranjang.</p>
                <a href="dashboard.php" class="btn btn-primary">
                    → Lihat Produk
                </a>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

</body>
</html>