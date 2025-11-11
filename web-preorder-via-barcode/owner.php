<?php
session_start();
include("config/db.php");

// 🔒 Cek login & role
if (!isset($_SESSION['user_role'])) {
  header("Location: login.php");
  exit;
}
if ($_SESSION['user_role'] !== 'owner') {
  header("Location: staff.php");
  exit;
}

/* ============================================================
   📊 Query Grafik Penjualan 30 Hari Terakhir
   ============================================================ */
$query = "
  SELECT 
    DATE(tanggal) AS tgl,
    COALESCE(SUM(total), 0) AS pendapatan
  FROM orders
  WHERE status = 'Lunas'
    AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
  GROUP BY DATE(tanggal)
  ORDER BY tgl ASC
";
$result = mysqli_query($conn, $query);

$labels = [];
$data = [];
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['tgl'];
    $data[] = (float) $row['pendapatan'];
  }
}

// 🧮 Isi hari kosong dengan nilai 0 biar grafik tetap 30 hari
for ($i = 29; $i >= 0; $i--) {
  $tgl = date("Y-m-d", strtotime("-$i days"));
  if (!in_array($tgl, $labels)) {
    $labels[] = $tgl;
    $data[] = 0;
  }
}

// Urutkan ulang (pastikan tanggal sesuai urutan)
array_multisort($labels, SORT_ASC, $data);

?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Dashboard Owner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto px-4 py-6">

    <!-- 🔹 Header -->
    <header class="flex flex-col sm:flex-row justify-between items-center mb-6 bg-white p-4 rounded-lg shadow">
      <h1 class="text-2xl font-bold text-gray-800">📊 Dashboard Owner</h1>
      <div class="flex items-center gap-3 mt-2 sm:mt-0">
        <span class="text-sm text-gray-600">👋 Halo, <b><?= htmlspecialchars($_SESSION['username']) ?></b></span>
        <a href="actions/logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
          Logout
        </a>
      </div>
    </header>

    <!-- 🔹 Grafik Penjualan -->
    <section class="bg-white p-4 sm:p-6 rounded-lg shadow mb-6">
      <h3 class="font-semibold text-gray-700 mb-4">Grafik Penjualan (30 Hari Terakhir)</h3>
      <div class="relative w-full h-72">
        <canvas id="salesChart"></canvas>
      </div>
    </section>

    <!-- 🔹 Tabel Pesanan Terbaru -->
    <section class="bg-white p-4 sm:p-6 rounded-lg shadow">
      <h3 class="font-semibold text-gray-700 mb-4">Daftar Pesanan Terbaru</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
          <thead class="bg-gray-100 text-gray-700 font-semibold">
            <tr>
              <th class="py-2 px-3 text-left">Nama Pemesan</th>
              <th class="py-2 px-3 text-left">Menu</th>
              <th class="py-2 px-3">Jumlah</th>
              <th class="py-2 px-3">Total</th>
              <th class="py-2 px-3">Status</th>
              <th class="py-2 px-3">Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $q = "
              SELECT o.nama_pemesan, p.nama_produk, o.jumlah, o.total, o.status, o.tanggal
              FROM orders o
              JOIN products p ON o.id_produk = p.id
              ORDER BY o.id DESC
              LIMIT 100
            ";
            $r = mysqli_query($conn, $q);

            if (mysqli_num_rows($r) == 0) {
              echo "<tr><td colspan='6' class='text-center text-gray-500 py-4'>Belum ada data pesanan.</td></tr>";
            } else {
              while ($row = mysqli_fetch_assoc($r)) {
                $statusColor = $row['status'] == 'Lunas' ? 'text-green-600' : 'text-orange-500';
                echo "
                <tr class='border-t hover:bg-gray-50 transition'>
                  <td class='py-2 px-3'>{$row['nama_pemesan']}</td>
                  <td class='py-2 px-3'>{$row['nama_produk']}</td>
                  <td class='py-2 px-3 text-center'>{$row['jumlah']}</td>
                  <td class='py-2 px-3 text-right'>Rp" . number_format($row['total'], 0, ',', '.') . "</td>
                  <td class='py-2 px-3 font-semibold $statusColor'>{$row['status']}</td>
                  <td class='py-2 px-3 text-gray-500'>" . date("d M Y", strtotime($row['tanggal'])) . "</td>
                </tr>
                ";
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <!-- 📊 Chart.js -->
  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const labels = <?= json_encode($labels) ?>;
    const data = <?= json_encode($data) ?>;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Pendapatan Harian (Rp)',
          data: data,
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22,163,74,0.15)',
          fill: true,
          tension: 0.3,
          borderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#16a34a'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            ticks: { color: '#374151', font: { size: 10 } },
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: '#374151',
              callback: value => 'Rp ' + value.toLocaleString('id-ID')
            },
            grid: { color: '#e5e7eb' }
          }
        },
        plugins: {
          legend: {
            display: true,
            labels: { color: '#374151' }
          },
          tooltip: {
            callbacks: {
              label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
            }
          }
        }
      }
    });
  </script>

  <!-- 🔔 SweetAlert success popup -->
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
    </script>
  <?php endif; ?>

</body>

</html>