<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori & Brand - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php

include 'config/koneksi.php';
session_start();

if($_SESSION['role']!='admin'){
    header("location:dashboard.php");
    exit;
}

include "includes/navbar.php";

/* =====================
HAPUS KATEGORI
===================== */

if(isset($_GET['hapus_kategori'])){

$id=$_GET['hapus_kategori'];

mysqli_query($conn,"DELETE FROM kategori 
WHERE id_kategori='$id'");

header("Location:kategori_brand.php");

}

/* =====================
HAPUS BRAND
===================== */

if(isset($_GET['hapus_brand'])){

$id=$_GET['hapus_brand'];

mysqli_query($conn,"DELETE FROM brand 
WHERE id_brand='$id'");

header("Location:kategori_brand.php");

}

/* =====================
EDIT KATEGORI
===================== */

if(isset($_GET['edit_kategori'])){

$id=$_GET['edit_kategori'];

$data=mysqli_query($conn,"SELECT * FROM kategori 
WHERE id_kategori='$id'");

$edit_kategori=mysqli_fetch_array($data);

}

/* =====================
EDIT BRAND
===================== */

if(isset($_GET['edit_brand'])){

$id=$_GET['edit_brand'];

$data=mysqli_query($conn,"SELECT * FROM brand 
WHERE id_brand='$id'");

$edit_brand=mysqli_fetch_array($data);

}

/* =====================
SIMPAN KATEGORI
===================== */

if(isset($_POST['simpan_kategori'])){

$nama=$_POST['nama_kategori'];
$id=$_POST['id_kategori'];

if($id==""){

mysqli_query($conn,"INSERT INTO kategori 
VALUES(NULL,'$nama')");

}else{

mysqli_query($conn,"UPDATE kategori SET
nama_kategori='$nama'
WHERE id_kategori='$id'");

}

header("Location:kategori_brand.php");

}

/* =====================
SIMPAN BRAND
===================== */

if(isset($_POST['simpan_brand'])){

$nama=$_POST['nama_brand'];
$id=$_POST['id_brand'];

if($id==""){

mysqli_query($conn,"INSERT INTO brand 
VALUES(NULL,'$nama')");

}else{

mysqli_query($conn,"UPDATE brand SET
nama_brand='$nama'
WHERE id_brand='$id'");

}

header("Location:kategori_brand.php");

}

?>

<div class="container">
    <div class="page-header">
        <a href="produk.php" class="breadcrumb">
            <a href="produk.php" class="btn btn-light btn-sm">← Kembali</a>
        </a>
        <h1 class="page-title">Kelola Kategori & Brand</h1>
    </div>

    <div class="grid-2">
        <!-- ================= KATEGORI ================= -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span>Kategori</span>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden"
                        name="id_kategori"
                        value="<?php echo isset($edit_kategori)?$edit_kategori['id_kategori']:''; ?>">

                        <div class="form-group">
                            <input type="text"
                            class="form-control"
                            name="nama_kategori"
                            placeholder="Nama kategori"
                            value="<?php echo isset($edit_kategori)?$edit_kategori['nama_kategori']:''; ?>"
                            required>
                        </div>

                        <button type="submit" name="simpan_kategori" class="btn btn-primary w-100">
                            <?php
                            if(isset($edit_kategori)){
                            echo "Update Kategori";
                            }else{
                            echo "Tambah Kategori";
                            }
                            ?>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <span>Daftar Kategori</span>
                </div>
                <div class="table-wrapper">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $no=1;

                            $data=mysqli_query($conn,"SELECT * FROM kategori");

                            while($d=mysqli_fetch_array($data)){

                            ?>

                            <tr>

                                <td><?php echo $no++; ?></td>

                                <td><?php echo $d['nama_kategori']; ?></td>

                                <td>
                                    <div class="btn-group">
                                        <a href="kategori_brand.php?edit_kategori=<?php echo $d['id_kategori']; ?>" class="btn btn-edit btn-sm">✏️ Edit</a>
                                        <a href="kategori_brand.php?hapus_kategori=<?php echo $d['id_kategori']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Yakin hapus kategori ini?')">🗑️ Hapus</a>
                                    </div>
                                </td>

                            </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ================= BRAND ================= -->
        <div>
            <div class="card">
                <div class="card-header">
                    <span>Brand</span>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden"
                        name="id_brand"
                        value="<?php echo isset($edit_brand)?$edit_brand['id_brand']:''; ?>">

                        <div class="form-group">
                            <input type="text"
                            class="form-control"
                            name="nama_brand"
                            placeholder="Nama brand"
                            value="<?php echo isset($edit_brand)?$edit_brand['nama_brand']:''; ?>"
                            required>
                        </div>

                        <button type="submit" name="simpan_brand" class="btn btn-primary w-100">
                            <?php
                            if(isset($edit_brand)){
                            echo "Update Brand";
                            }else{
                            echo "Tambah Brand";
                            }
                            ?>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <span>Daftar Brand</span>
                </div>
                <div class="table-wrapper">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $no=1;

                            $data=mysqli_query($conn,"SELECT * FROM brand");

                            while($d=mysqli_fetch_array($data)){

                            ?>

                            <tr>

                                <td><?php echo $no++; ?></td>

                                <td><?php echo $d['nama_brand']; ?></td>

                                <td>
                                    <div class="btn-group">
                                        <a href="kategori_brand.php?edit_brand=<?php echo $d['id_brand']; ?>" class="btn btn-edit btn-sm">✏️ Edit</a>
                                        <a href="kategori_brand.php?hapus_brand=<?php echo $d['id_brand']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Yakin hapus brand ini?')">🗑️ Hapus</a>
                                    </div>
                                </td>

                            </tr>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>