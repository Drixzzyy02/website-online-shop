<?php

include 'config/koneksi.php';

$id=$_GET['id'];

mysqli_query($conn,"DELETE FROM brand 
WHERE id_brand='$id'");

header("Location:kategori_brand.php");

?>