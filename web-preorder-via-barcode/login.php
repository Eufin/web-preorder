<?php
session_start();
include("config/db.php");

// kalau sudah login langsung arahkan ke dashboard sesuai role
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'owner') {
        header("Location: owner.php");
        exit;
    } elseif ($_SESSION['user_role'] === 'staff') {
        header("Location: staff.php");
        exit;
    }
}

// kalau ada cookie remember me tapi session kosong
if (!isset($_SESSION['user_role']) && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    $user = $_COOKIE['remember_user'];
    $token = $_COOKIE['remember_token'];
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$token'");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $_SESSION['user_role'] = $row['role'];
        $_SESSION['username'] = $row['username'];
        header("Location: " . ($row['role'] === 'owner' ? 'owner.php' : 'staff.php'));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Cafe Preorder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <form action="actions/login_action.php" method="POST" class="bg-white p-8 rounded-xl shadow-md w-80 sm:w-96">
        <h1 class="text-2xl font-bold text-center text-green-700 mb-6">Login</h1>

        <input type="text" name="username" placeholder="Username"
            class="border rounded w-full p-2 mb-3 focus:ring-2 focus:ring-green-400 outline-none" required>

        <input type="password" name="password" placeholder="Password"
            class="border rounded w-full p-2 mb-3 focus:ring-2 focus:ring-green-400 outline-none" required>

        <label class="flex items-center text-sm mb-4">
            <input type="checkbox" name="remember" class="mr-2 accent-green-600">
            Ingat saya (tetap login)
        </label>

        <button type="submit" class="bg-green-600 w-full text-white py-2 rounded-lg hover:bg-green-700 transition">
            Login
        </button>

        <p class="text-center text-xs text-gray-500 mt-4">
            © <?= date("Y") ?> udin keseleo
        </p>
    </form>
</body>

</html>