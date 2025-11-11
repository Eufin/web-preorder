<?php
include("../config/db.php");
session_start();

// Pastikan user login
if (!isset($_SESSION['user_role'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID pesanan dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Cek apakah pesanan ada
    $check = mysqli_query($conn, "SELECT * FROM orders WHERE id = $id");
    if (mysqli_num_rows($check) === 0) {
        die("❌ Pesanan tidak ditemukan.");
    }

    // Update status jadi Lunas
    $update = mysqli_query($conn, "UPDATE orders SET status = 'Lunas' WHERE id = $id");

    if ($update) {
        // Redirect kembali sesuai role
        if ($_SESSION['user_role'] === 'owner') {
            header("Location: ../owner.php?msg=verified");
        } else {
            header("Location: ../staff.php?msg=verified");
        }
        exit;
    } else {
        die("❌ Gagal update: " . mysqli_error($conn));
    }
} else {
    die("❌ ID pesanan tidak valid.");
}
?>