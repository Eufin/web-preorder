<?php
include("../config/db.php");

$data = json_decode(file_get_contents("php://input"), true);
$namaPemesan = $data['namaPemesan'];
$cart = $data['cart'];

if (!$cart || count($cart) == 0) {
    echo "Keranjang kosong!";
    exit;
}

foreach ($cart as $item) {
    $id_produk = $item['id'];
    $jumlah = $item['jumlah'];
    $suhu = $item['suhu'];
    $gula = $item['gula'];
    $catatan = $item['catatan'];
    $total = $item['harga'] * $jumlah;

    $sql = "INSERT INTO orders (nama_pemesan, id_produk, jumlah, total, suhu, gula, catatan)
            VALUES ('$namaPemesan', '$id_produk', '$jumlah', '$total', '$suhu', '$gula', '$catatan')";
    mysqli_query($conn, $sql);
}

echo "Pesanan berhasil dikirim!";
?>