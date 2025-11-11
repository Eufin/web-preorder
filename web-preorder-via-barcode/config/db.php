<?php
// koneksi database sederhana, gunakan ini di semua file PHP yang butuh DB

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'preorder_db';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    // jika koneksi gagal, tampilkan pesan dan stop
    die("Koneksi gagal: " . mysqli_connect_error());
}
// set charset biar utf8 aman
mysqli_set_charset($conn, "utf8mb4");
?>