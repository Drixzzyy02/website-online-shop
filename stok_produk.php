<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Stok - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
session_start();

if($_SESSION['role']!='admin'){
    header("location:dashboard.php");
    exit;
}

include "config/koneksi.php";
include "includes/navbar.php";

$produk = mysqli_query($conn,"SELECT * FROM products ORDER BY nama_produk ASC");

if(isset($_POST['proses'])){

$id = $_POST['id_produk'];
$jumlah = $_POST['jumlah'];
$aksi = $_POST['aksi'];

$data = mysqli_query($conn,"SELECT * FROM products WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

$stok_lama = $row['stok'];

if($aksi == "tambah"){
$stok_baru = $stok_lama + $jumlah;
}

if($aksi == "kurang"){
$stok_baru = $stok_lama - $jumlah;
}

mysqli_query($conn,"UPDATE products SET stok='$stok_baru' WHERE id='$id'");

header("Location: produk.php");
exit;

}
?>

<div class="container">
    <div class="page-header">
        <a href="produk.php" class="breadcrumb">
            <a href="produk.php" class="btn btn-light btn-sm">← Kembali</a>
        </a>
        <h1 class="page-title">Kelola Stok Produk</h1>
        <p class="page-subtitle">Tambah atau kurangi stok produk Anda</p>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-header">
            <span>Form Ubah Stok</span>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Pilih Produk</label>
                    <select class="form-control" name="id_produk" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php while($p = mysqli_fetch_assoc($produk)){ ?>
                            <option value="<?php echo $p['id']; ?>">
                                <?php echo $p['nama_produk'] . ' (Stok: ' . $p['stok'] . ')'; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" min="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tindakan</label>
                        <select class="form-control" name="aksi" required>
                            <option value="tambah">Tambah Stok</option>
                            <option value="kurang">Kurangi Stok</option>
                        </select>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" name="proses" class="btn btn-primary">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>