<?php
include "config/koneksi.php";

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM products WHERE id='$id'");

header("Location: produk.php");