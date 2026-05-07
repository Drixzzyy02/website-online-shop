<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - WebKu</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php

session_start();

if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
    header("location:dashboard.php");
    exit;
}

include "config/koneksi.php";
include "includes/navbar.php";

$data = mysqli_query($conn,"
SELECT 
transaksi.*, 
products.nama_produk, 
users.name as username 

FROM transaksi 

LEFT JOIN products 
ON transaksi.id_produk = products.id

LEFT JOIN users 
ON transaksi.id_user = users.id

WHERE transaksi.status!='pending'

ORDER BY transaksi.id_transaksi DESC
");

if(!$data){
    die('Query error konfirmasi.php: ' . mysqli_error($conn));
}

?>

<div class="container">
    <div class="page-header">
        <a href="produk.php" class="breadcrumb">
            <a href="produk.php" class="btn btn-light btn-sm">← Kembali</a>
        </a>
        <h1 class="page-title">Riwayat Transaksi</h1>
        <p class="page-subtitle">Daftar transaksi yang telah diproses</p>
    </div>

    <div class="card">
        <div class="card-header">
            <span><?php echo mysqli_num_rows($data); ?> Transaksi</span>
        </div>

        <?php if(mysqli_num_rows($data) > 0) { ?>
            <div class="table-wrapper">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Produk</th>
                            <th style="width: 100px;">Harga</th>
                            <th style="width: 60px;">Jumlah</th>
                            <th style="width: 100px;">Total</th>
                            <th>Customer</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 

                        while($row = mysqli_fetch_assoc($data)){ 
                        ?>

                        <tr>

                            <td><?php echo $no++; ?></td>

                            <td>
                                <?php 
                                echo !empty($row['nama_produk']) 
                                ? '<strong>' . $row['nama_produk'] . '</strong>'
                                : '<em class="text-muted">Produk sudah dihapus</em>';
                                ?>
                            </td>

                            <td>
                                Rp <?php echo number_format($row['harga'],0,",","."); ?>
                            </td>

                            <td>
                                <?php echo $row['jumlah']; ?>
                            </td>

                            <td>
                                <strong>Rp <?php echo number_format($row['total'],0,",","."); ?></strong>
                            </td>

                            <td>
                                <?php echo $row['username']; ?>
                            </td>

                            <td>
                                <?php 

                                if($row['status']=="disetujui" || $row['status']=="confirmed"){
                                    echo "<span class='badge badge-success'>Disetujui</span>";
                                }
                                else if($row['status']=="ditolak"){
                                    echo "<span class='badge badge-danger'>Ditolak</span>";
                                }

                                ?>
                            </td>

                            <td>
                                <?php 
                                if(isset($row['tanggal']) && !empty($row['tanggal'])){
                                    echo date('d/m/Y H:i', strtotime($row['tanggal']));
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>

                        </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3 class="empty-state-title">Belum ada transaksi</h3>
                    <p class="empty-state-text">Tidak ada transaksi yang telah diproses</p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>