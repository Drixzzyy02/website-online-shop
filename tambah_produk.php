<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - WebKu</title>
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

/* AMBIL KATEGORI */
$kategori=mysqli_query($conn,"
    SELECT * FROM kategori
    ORDER BY nama_kategori ASC
");

/* AMBIL BRAND */
$brand=mysqli_query($conn,"
    SELECT * FROM brand
    ORDER BY nama_brand ASC
");

/* SIMPAN PRODUK */
if(isset($_POST['simpan'])){
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $id_kategori = $_POST['id_kategori'];
    $id_brand = $_POST['id_brand'];

    // proses upload foto
    if(isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] === 0){
        $foto = $_FILES['foto_produk']['name'];
        $tmp = $_FILES['foto_produk']['tmp_name'];
        $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if(in_array($ext, $allowed)){
            $fotobaru = time().'_'.$foto; // nama unik
            $path = "uploads/".$fotobaru;

            if(move_uploaded_file($tmp, $path)){
                mysqli_query($conn, "
                    INSERT INTO products
                    VALUES(
                        NULL,
                        '$nama',
                        '$harga',
                        '$stok',
                        '$id_kategori',
                        '$id_brand',
                        '$fotobaru'
                    )
                ");
                header("location:produk.php");
                exit;
            } else {
                $error = "Gagal upload foto!";
            }
        } else {
            $error = "Format foto harus JPG, JPEG, PNG, atau GIF!";
        }
    } else {
        $error = "Silahkan pilih foto produk!";
    }
}
?>

<div class="container">
    <div class="page-header">
        <a href="produk.php" class="breadcrumb">
            <a href="produk.php" class="btn btn-light btn-sm">← Kembali</a>
        </a>
        <h1 class="page-title">Tambah Produk</h1>
    </div>

    <?php if(isset($error)){ ?>
        <div class="alert alert-danger">
            <span><?php echo $error; ?></span>
        </div>
    <?php } ?>

    <div class="card">
        <div class="card-header">
            <span>Form Tambah Produk</span>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" name="nama_produk" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" class="form-control" name="stok" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="id_kategori" required>
                            <option value="">Pilih kategori</option>
                            <?php while($k=mysqli_fetch_array($kategori)){ ?>
                                <option value="<?php echo $k['id_kategori']; ?>">
                                    <?php echo $k['nama_kategori']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <select class="form-control" name="id_brand" required>
                            <option value="">Pilih brand</option>
                            <?php while($b=mysqli_fetch_array($brand)){ ?>
                                <option value="<?php echo $b['id_brand']; ?>">
                                    <?php echo $b['nama_brand']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Produk</label>
                    <input type="file" class="form-control" name="foto_produk" accept="image/*" required>
                </div>

                <div class="card-footer">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>