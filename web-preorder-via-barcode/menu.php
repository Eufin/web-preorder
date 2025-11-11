<?php
include("config/db.php");
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Menu - Preorder</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-50 min-h-screen flex flex-col sm:flex-row">

  <!-- 🔹 MENU UTAMA -->
  <div class="flex-1 max-w-5xl mx-auto p-4 pb-24 sm:pb-4"> <!-- pb-24 biar menu gak ketutupan tombol mobile -->
    <header class="text-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Menu Pre-Order</h1>
      <p class="text-gray-500 text-sm">Pilih menu dan tambahkan ke keranjang</p>
    </header>

    <!-- Grid produk -->
    <main class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
      <?php
      $stmt = mysqli_query($conn, "SELECT * FROM products");
      while ($p = mysqli_fetch_assoc($stmt)) {
        $gambar = htmlspecialchars($p['gambar'] ?: 'default.jpg');
        $nama = htmlspecialchars($p['nama_produk']);
        $harga = number_format($p['harga'], 0, ',', '.');
        echo "
        <div class='bg-white rounded-lg shadow hover:shadow-lg transition transform hover:-translate-y-1 overflow-hidden'>
          <img src='assets/img/$gambar' alt='$nama' class='w-full h-40 object-cover'>
          <div class='p-3 text-center'>
            <h3 class='font-semibold text-gray-800 text-sm'>$nama</h3>
            <p class='text-green-600 font-bold mb-2'>Rp $harga</p>
            <button onclick='openPopup({$p['id']}, \"$nama\", {$p['harga']})'
              class=\"bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700 text-sm transition\">Tambah</button>
          </div>
        </div>
        ";
      }
      ?>
    </main>
  </div>

  <!-- 🔹 SIDEBAR KERANJANG (DESKTOP) -->
  <aside id="cartSidebar"
    class="hidden sm:block w-80 bg-white border-l border-gray-200 p-4 shadow-lg h-full sticky top-0">
    <h2 class="text-lg font-semibold text-gray-700 mb-3">🛒 Keranjang</h2>
    <div id="cartList" class="space-y-2 mb-3 max-h-[60vh] overflow-y-auto"></div>
    <div class="flex justify-between font-semibold text-gray-800 mb-3">
      <span>Total:</span> <span id="cartTotal">Rp 0</span>
    </div>
    <button onclick="checkout()"
      class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">Checkout</button>
  </aside>

  <!-- 🔹 TOMBOL KERANJANG (MOBILE) -->
  <button id="cartButton"
    class="sm:hidden fixed bottom-3 left-1/2 -translate-x-1/2 bg-green-600 text-white px-5 py-3 rounded-full shadow-lg flex items-center gap-2 z-40">
    🛒 <span id="cartCount">0</span> Item
  </button>

  <!-- 🔹 MODAL KERANJANG (MOBILE) -->
  <div id="cartModal" class="hidden fixed inset-0 bg-black/50 flex items-end sm:hidden z-50">
    <div class="bg-white w-full p-4 rounded-t-2xl shadow-lg max-h-[80vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-semibold">🛒 Keranjang</h2>
        <button onclick="closeCartModal()" class="text-gray-600 text-2xl font-bold">×</button>
      </div>
      <div id="cartListMobile" class="space-y-2 mb-3"></div>
      <div class="flex justify-between font-semibold text-gray-800 mb-3">
        <span>Total:</span> <span id="cartTotalMobile">Rp 0</span>
      </div>
      <button onclick="checkout()"
        class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">Checkout</button>
    </div>
  </div>

  <!-- 🔹 POPUP TAMBAH MENU -->
  <div id="popup" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg w-full max-w-sm p-5">
      <h3 id="popup-name" class="font-bold mb-2"></h3>
      <p id="popup-price" class="text-green-600 font-bold mb-3"></p>
      <form id="popupForm">
        <input type="hidden" id="productId">
        <label class="block text-sm">Suhu</label>
        <select id="productSuhu" class="border rounded w-full p-2 mb-2">
          <option>Dingin</option>
          <option>Panas</option>
        </select>
        <label class="block text-sm">Gula</label>
        <select id="productGula" class="border rounded w-full p-2 mb-2">
          <option>Normal</option>
          <option>Less Sugar</option>
          <option>Request</option>
        </select>
        <label class="block text-sm">Catatan</label>
        <input id="productCatatan" class="border rounded w-full p-2 mb-2" placeholder="Contoh: tanpa es">
        <label class="block text-sm">Jumlah</label>
        <input id="productJumlah" type="number" min="1" value="1" class="border rounded w-full p-2 mb-3">
        <div class="flex gap-3">
          <button type="button" onclick="closePopup()" class="flex-1 border py-2 rounded">Batal</button>
          <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded">Tambah</button>
        </div>
      </form>
    </div>
  </div>

  <script src="assets/js/menu.js"></script>
</body>

</html>