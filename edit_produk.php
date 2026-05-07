<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - WebKu</title>
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

$id = $_GET['id'];

/* AMBIL DATA PRODUK */
$data = mysqli_query($conn,"
SELECT * FROM products 
WHERE id='$id'
");

$d = mysqli_fetch_assoc($data);

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

/* UPDATE */
if(isset($_POST['update'])){

$nama = $_POST['nama'];
$harga = $_POST['harga'];
$stok = $_POST['stok'];
$id_kategori = $_POST['id_kategori'];
$id_brand = $_POST['id_brand'];

$foto_lama = $d['foto_produk'];

$foto_baru = $foto_lama; // default

// proses upload foto baru jika ada
if(isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] === 0){
    $foto = $_FILES['foto_produk']['name'];
    $tmp = $_FILES['foto_produk']['tmp_name'];
    $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];

    if(in_array($ext, $allowed)){
        $fotobaru = time().'_'.$foto; // nama unik
        $path = "uploads/".$fotobaru;

        if(move_uploaded_file($tmp, $path)){
            $foto_baru = $fotobaru;
            // hapus foto lama jika ada dan bukan default
            if($foto_lama && file_exists("uploads/".$foto_lama)){
                unlink("uploads/".$foto_lama);
            }
        } else {
            $error = "Gagal upload foto!";
        }
    } else {
        $error = "Format foto harus JPG, JPEG, PNG, atau GIF!";
    }
}

if(!isset($error)){
    mysqli_query($conn,"
    UPDATE products SET 

    nama_produk='$nama',
    harga='$harga',
    stok='$stok',
    id_kategori='$id_kategori',
    id_brand='$id_brand',
    foto_produk='$foto_baru'

    WHERE id='$id'
    ");

    header("Location: produk.php");
}

}
?>

<div class="container">
    <div class="page-header">
        <a href="produk.php" class="breadcrumb">
            <a href="produk.php" class="btn btn-light btn-sm">← Kembali</a>
        </a>
        <h1 class="page-title">Edit Produk</h1>
    </div>

    <?php if(isset($error)){ ?>
        <div class="alert alert-danger">
            <span><?php echo $error; ?></span>
        </div>
    <?php } ?>

    <div class="card">
        <div class="card-header">
            <span>Form Edit Produk</span>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" name="nama" value="<?php echo $d['nama_produk']; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" value="<?php echo $d['harga']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" class="form-control" name="stok" value="<?php echo $d['stok']; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="id_kategori" required>
                            <?php
                            while($k=mysqli_fetch_array($kategori)){
                            ?>
                            <option 
                            value="<?php echo $k['id_kategori']; ?>"
                            <?php
                            if($d['id_kategori']==$k['id_kategori']){
                            echo "selected";
                            }
                            ?>
                            >
                            <?php echo $k['nama_kategori']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Brand</label>
                        <select class="form-control" name="id_brand" required>
                            <?php
                            while($b=mysqli_fetch_array($brand)){
                            ?>
                            <option 
                            value="<?php echo $b['id_brand']; ?>"
                            <?php
                            if($d['id_brand']==$b['id_brand']){
                            echo "selected";
                            }
                            ?>
                            >
                            <?php echo $b['nama_brand']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Produk Saat Ini</label>
                    <?php 
                    if($d['foto_produk'] && file_exists("uploads/".$d['foto_produk'])){
                        echo "<img src='uploads/".$d['foto_produk']."' class='img-product' style='margin-bottom: 15px;'>";
                    } else {
                        echo "<p class='text-muted'>Tidak ada foto</p>";
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Ganti Foto Produk (opsional)</label>
                    <input type="file" class="form-control" name="foto_produk" accept="image/*">
                </div>

                <div class="card-footer">
                    <button type="submit" name="update" class="btn btn-primary">Update Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>