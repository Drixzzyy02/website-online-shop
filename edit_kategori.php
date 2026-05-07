<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
session_start();

if($_SESSION['role']!='admin'){
    header("location:dashboard.php");
    exit;
}

include 'config/koneksi.php';
include "includes/navbar.php";

$id=$_GET['id'];

$data=mysqli_query($conn,"SELECT * FROM kategori 
WHERE id_kategori='$id'");

$d=mysqli_fetch_array($data);

if(isset($_POST['update'])){

$nama=$_POST['nama_kategori'];

mysqli_query($conn,"UPDATE kategori SET
nama_kategori='$nama'
WHERE id_kategori='$id'");

header("Location:kategori_brand.php");

}
?>

<div class="container">
    <div class="page-header">
        <a href="kategori_brand.php" class="breadcrumb">
            <a href="kategori_brand.php" class="btn btn-light btn-sm">← Kembali</a>
        </a>
        <h1 class="page-title">Edit Kategori</h1>
    </div>

    <div class="card" style="max-width: 500px;">
        <div class="card-header">
            <span>Form Edit Kategori</span>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text"
                    class="form-control"
                    name="nama_kategori"
                    value="<?php echo $d['nama_kategori']; ?>"
                    required>
                </div>

                <div class="card-footer">
                    <button type="submit" name="update" class="btn btn-primary">Update Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>