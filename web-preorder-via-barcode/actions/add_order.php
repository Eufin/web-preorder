<?php
// include("../config/db.php");

// $nama = $_POST['nama'];
// $id_produk = $_POST['id_produk'];
// $jumlah = $_POST['jumlah'];

// // ambil harga produk
// $res = mysqli_query($conn, "SELECT harga FROM products WHERE id='$id_produk'");
// $row = mysqli_fetch_assoc($res);
// $total = $row['harga'] * $jumlah;

// // simpan pesanan
// $sql = "INSERT INTO orders (nama_pemesan, id_produk, jumlah, total) 
//         VALUES ('$nama', '$id_produk', '$jumlah', '$total')";

// if (mysqli_query($conn, $sql)) {
//     echo "<script>alert('Pesanan berhasil! Silakan tunggu konfirmasi.');window.location='../menu.php';</script>";
// } else {
//     echo "Error: " . mysqli_error($conn);
// }

// actions/add_order.php

include("../config/db.php");

$nama = $_POST['nama'] ?? '';
$id_produk = $_POST['id_produk'] ?? '';
$jumlah = $_POST['jumlah'] ?? 1;
$suhu = $_POST['suhu'] ?? 'Dingin';
$gula = $_POST['gula'] ?? 'Normal';
$catatan = $_POST['catatan'] ?? '';

if (!$nama || !$id_produk) {
    http_response_code(400);
    echo "Data tidak lengkap!";
    exit;
}

$q = mysqli_query($conn, "SELECT harga FROM products WHERE id='$id_produk'");
$row = mysqli_fetch_assoc($q);
$total = $row['harga'] * $jumlah;

$sql = "INSERT INTO orders (nama_pemesan, id_produk, jumlah, total, suhu, gula, catatan)
        VALUES ('$nama', '$id_produk', '$jumlah', '$total', '$suhu', '$gula', '$catatan')";

if (mysqli_query($conn, $sql)) {
    echo "OK";
} else {
    echo "Error: " . mysqli_error($conn);
}

?>