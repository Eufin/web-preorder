<?php
session_start();
include("../config/db.php");

$username = $_POST['username'];
$password = $_POST['password'];
$remember = isset($_POST['remember']);

$query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
if (mysqli_num_rows($query) > 0) {
    $user = mysqli_fetch_assoc($query);

    // simpan session
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['username'] = $user['username'];

    // kalau centang remember me
    if ($remember) {
        setcookie("remember_user", $user['username'], time() + (86400 * 30), "/");
        setcookie("remember_token", $user['password'], time() + (86400 * 30), "/");
    }

    // redirect sesuai role
    if ($user['role'] == 'owner') {
        header("Location: ../owner.php");
    } else {
        header("Location: ../staff.php");
    }
    exit;
} else {
    echo "<script>alert('Username atau Password salah!'); window.location='../login.php';</script>";
}
?>