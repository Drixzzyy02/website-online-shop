<?php

$conn = mysqli_connect("localhost","root","","webku");

if(!$conn){
    die("koneksi gagal");
}

// Buat tabel keranjang jika belum ada
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

?>