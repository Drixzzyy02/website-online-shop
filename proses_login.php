<?php
session_start();

include "config/koneksi.php";

$email = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
$data = mysqli_fetch_assoc($query);

if($data){

if($password == $data['password']){

$_SESSION['id'] = $data['id'];
$_SESSION['user'] = $data['name'];
$_SESSION['role'] = $data['role'];

if($data['role'] == "admin"){

header("Location: produk.php");

}else{

header("Location: dashboard.php");

}

}else{

echo "Password salah";

}

}else{

echo "Email tidak ditemukan";

}
?>