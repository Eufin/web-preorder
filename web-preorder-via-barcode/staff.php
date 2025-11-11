<?php
// session_name("staff_session");
session_start();
include("config/db.php");

if (!isset($_SESSION['user_role'])) {
  header("Location: login.php");
  exit;
}

if ($_SESSION['user_role'] !== 'staff') {
  header("Location: owner.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Pegawai</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

  <!-- HEADER -->
  <header class="bg-green-600 text-white p-4 flex justify-between items-center shadow">
    <h1 class="text-lg sm:text-xl font-bold">Dashboard Pegawai</h1>
    <span class="text-sm opacity-80">Ujung kulon Coffee Shop</span>
  </header>

  <header class="text-black p-4 flex justify-between items-center">
    <h1 class="text-lg sm:text-xl font-bold"></h1>
    <div class="flex items-center gap-3">
      <span class="text-sm opacity-80">Hai, <?= $_SESSION['username'] ?></span>
      <a href="actions/logout.php"
        class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-lg text-white text-sm ">Logout</a>
    </div>
  </header>


  <!-- MAIN CONTENT -->
  <main class="flex-1 p-4 sm:p-6 space-y-10">

    <!-- DAFTAR MENU -->
    <section>
      <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-3 sm:mb-4">📋 Daftar Menu</h2>

      <!-- Grid responsif, otomatis menyesuaikan ukuran layar -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
        <?php
        $menus = mysqli_query($conn, "SELECT * FROM products");
        while ($m = mysqli_fetch_assoc($menus)) {
          $gambar = $m['gambar'] ?? 'default.jpg';
          echo "
          <div class='bg-white rounded-xl shadow hover:shadow-lg transition-all duration-300 p-3 flex flex-col items-center'>
            <img src='assets/img/$gambar' class='w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-lg mb-2'>
            <h3 class='text-sm sm:text-base font-semibold text-gray-800 text-center'>{$m['nama_produk']}</h3>
            <p class='text-green-600 font-bold text-sm sm:text-base'>Rp" . number_format($m['harga'], 0, ',', '.') . "</p>
          </div>";
        }
        ?>
      </div>
    </section>

    <!-- PESANAN MASUK -->
    <section>
      <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-3 sm:mb-4">🧾 Pesanan Masuk</h2>

      <div class="overflow-x-auto bg-white rounded-xl shadow">
        <table class="min-w-full text-sm text-center">
          <thead class="bg-gray-200 text-gray-700 font-semibold text-xs sm:text-sm">
            <tr>
              <th class="py-2 px-3">Pemesan</th>
              <th class="py-2 px-3">Menu</th>
              <th class="py-2 px-3">Jumlah</th>
              <th class="py-2 px-3">Total</th>
              <th class="py-2 px-3">Suhu</th>
              <th class="py-2 px-3">Gula</th>
              <th class="py-2 px-3">Catatan</th>
              <th class="py-2 px-3">Status</th>
              <th class="py-2 px-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-xs sm:text-sm">
            <?php
            $sql = "SELECT o.id, o.nama_pemesan, p.nama_produk, o.jumlah, o.total, o.suhu, o.gula, o.catatan, o.status
            FROM orders o
            JOIN products p ON o.id_produk = p.id
            ORDER BY o.id DESC";

            $res = mysqli_query($conn, $sql);

            if (!$res) {
              echo "<tr><td colspan='9' class='text-red-500 py-4'>Error: " . mysqli_error($conn) . "</td></tr>";
            } elseif (mysqli_num_rows($res) == 0) {
              echo "<tr><td colspan='9' class='text-gray-500 py-4'>Belum ada pesanan masuk.</td></tr>";
            } else {
              while ($row = mysqli_fetch_assoc($res)) {
                $statusClass = $row['status'] == 'Pending' ? 'text-orange-600' : 'text-green-600';
                echo "<tr class='border-b hover:bg-gray-50 transition'>
                        <td class='py-2'>{$row['nama_pemesan']}</td>
                        <td>{$row['nama_produk']}</td>
                        <td>{$row['jumlah']}</td>
                        <td>Rp" . number_format($row['total'], 0, ',', '.') . "</td>
                        <td>{$row['suhu']}</td>
                        <td>{$row['gula']}</td>
                        <td class='truncate max-w-[100px]'>{$row['catatan']}</td>
                        <td class='$statusClass font-semibold'>{$row['status']}</td>
                        <td>";
                if ($row['status'] == 'Pending') {
                  echo "<a href='actions/verify_order.php?id={$row['id']}'
                         class='bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 transition'>
                         Verifikasi</a>";
                } else {
                  echo "<span class='text-gray-400'>✔</span>";
                }
                echo "</td></tr>";
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="bg-gray-100 text-center py-3 text-sm text-gray-500 mt-auto">
    &copy; <?= date("Y") ?> Cafe Pre-Order System — Made by You ☕
  </footer>

</body>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'verified'): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Pesanan Diverifikasi!',
      text: 'Status pesanan telah diubah menjadi Lunas.',
      timer: 2000,
      showConfirmButton: false
    });

    // 🔧 Hapus parameter msg dari URL biar gak muncul tiap refresh
    if (window.history.replaceState) {
      const url = new URL(window.location);
      url.searchParams.delete('msg');
      window.history.replaceState({}, document.title, url);
    }
  </script>
<?php endif; ?>

</html>