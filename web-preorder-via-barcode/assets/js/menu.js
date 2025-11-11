// --- Array untuk menyimpan pesanan sementara (keranjang)
let cart = [];

// --- Fungsi buka popup tambah pesanan ---
function openPopup(id, name, price) {
  document.getElementById("popup").classList.remove("hidden");
  document.getElementById("productId").value = id;
  document.getElementById("popup-name").innerText = name;
  document.getElementById("popup-price").innerText =
    "Rp " + price.toLocaleString();
  document.getElementById("popupForm").dataset.price = price;
}

// --- Tutup popup ---
function closePopup() {
  document.getElementById("popup").classList.add("hidden");
}

// --- Tambahkan produk ke keranjang ---
document.getElementById("popupForm").addEventListener("submit", (e) => {
  e.preventDefault();
  const id = document.getElementById("productId").value;
  const name = document.getElementById("popup-name").innerText;
  const price = parseInt(e.target.dataset.price);
  const suhu = document.getElementById("productSuhu").value;
  const gula = document.getElementById("productGula").value;
  const catatan = document.getElementById("productCatatan").value;
  const jumlah = parseInt(document.getElementById("productJumlah").value);
  const total = price * jumlah;

  // Tambahkan ke array keranjang
  cart.push({ id, name, price, jumlah, total, suhu, gula, catatan });

  // 🔧 Update tampilan total dan isi keranjang (ini yang penting)
  renderCart();

  Swal.fire({
    icon: "success",
    title: "Ditambahkan!",
    text: `${name} berhasil masuk ke keranjang.`,
    showConfirmButton: false,
    timer: 1200,
  });

  closePopup();
});

// --- Tampilkan modal keranjang ---
function openCart() {
  const modal = document.getElementById("cartModal");
  const list = document.getElementById("cartList");
  const totalEl = document.getElementById("cartTotal");

  list.innerHTML = "";
  let totalHarga = 0;

  cart.forEach((item, i) => {
    totalHarga += item.total;
    list.innerHTML += `
      <div class="border rounded p-2 flex justify-between items-center">
        <div>
          <p class="font-semibold">${item.name}</p>
          <p class="text-xs text-gray-500">${item.suhu}, ${item.gula}</p>
          <p class="text-xs">${
            item.jumlah
          }x Rp${item.price.toLocaleString()}</p>
        </div>
        <button onclick="removeItem(${i})" class="text-red-500 hover:text-red-700">✕</button>
      </div>`;
  });

  totalEl.innerText = "Rp " + totalHarga.toLocaleString();
  modal.classList.remove("hidden");
}

// --- Tutup modal keranjang ---
function closeCart() {
  document.getElementById("cartModal").classList.add("hidden");
}

// --- Hapus item dari keranjang ---
function removeItem(index) {
  cart.splice(index, 1);
  renderCart(); // render ulang
}

// --- Checkout: kirim pesanan ke server ---
function checkout() {
  if (cart.length === 0) {
    Swal.fire(
      "Keranjang kosong!",
      "Tambahkan menu terlebih dahulu.",
      "warning"
    );
    return;
  }

  Swal.fire({
    title: "Masukkan nama pemesan",
    input: "text",
    inputPlaceholder: "Contoh: Daffa",
    showCancelButton: true,
    confirmButtonText: "Kirim Pesanan",
  }).then((result) => {
    if (result.isConfirmed && result.value.trim() !== "") {
      const nama = result.value.trim();

      // kirim semua item di keranjang
      cart.forEach((item) => {
        const formData = new FormData();
        formData.append("nama", nama);
        formData.append("id_produk", item.id);
        formData.append("jumlah", item.jumlah);
        formData.append("suhu", item.suhu);
        formData.append("gula", item.gula);
        formData.append("catatan", item.catatan);

        fetch("actions/add_order.php", {
          method: "POST",
          body: formData,
        });
      });

      Swal.fire("Berhasil!", "Pesanan kamu sudah dikirim.", "success");
      cart = [];
      closeCart();
    }
  });
}

// --- (opsional) buka keranjang via tombol ---
document.addEventListener("keydown", (e) => {
  if (e.key === "k") openCart(); // tekan "k" buat liat keranjang
});
// MOBILE CART HANDLER
const cartBtn = document.getElementById("cartButton");
const cartModal = document.getElementById("cartModal");
const cartCount = document.getElementById("cartCount");

function openCartModal() {
  cartModal.classList.remove("hidden");
}

function closeCartModal() {
  cartModal.classList.add("hidden");
}

cartBtn.addEventListener("click", openCartModal);

function renderCart() {
  const listDesktop = document.getElementById("cartList");
  const listMobile = document.getElementById("cartListMobile");
  const totalDesktop = document.getElementById("cartTotal");
  const totalMobile = document.getElementById("cartTotalMobile");

  listDesktop.innerHTML = "";
  listMobile.innerHTML = "";
  let total = 0;

  cart.forEach((item, i) => {
    const subtotal = item.price * item.jumlah;
    total += subtotal;

    const html = `
      <div class="flex justify-between items-start border rounded p-2">
        <div>
          <p class="font-semibold text-gray-800">${item.name}</p>
          <p class="text-xs text-gray-500">${item.suhu} • ${item.gula}</p>
          <p class="text-sm text-gray-600">${
            item.jumlah
          }x Rp${item.price.toLocaleString("id-ID")}</p>
        </div>
        <button onclick="removeItem(${i})" class="text-red-500 hover:text-red-700 text-lg">×</button>
      </div>
    `;

    listDesktop.insertAdjacentHTML("beforeend", html);
    listMobile.insertAdjacentHTML("beforeend", html);
  });

  totalDesktop.textContent = "Rp " + total.toLocaleString("id-ID");
  totalMobile.textContent = "Rp " + total.toLocaleString("id-ID");
  cartCount.textContent = cart.length;
}
