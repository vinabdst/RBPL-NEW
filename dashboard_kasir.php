<?php
    session_start();
    include "koneksi.php";
    
    // Proteksi halaman
    if(!isset($_SESSION['login'])) {
        header("Location: index.php");
        exit;
    }
    
    // Cek role (optional)
    if($_SESSION['role'] != 'Kasir') {
        header("Location: dashboard_kasir.php");
        exit;
    }
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="dashboard_kasir.css">
</head>
<body>

<div class="container">

  <!-- SIDEBAR -->
  <div class="sidebar">
    <h2>Toko Logam</h2>
    <p class="role">Kasir</p>

    <ul>
      <li class="active">Dashboard</li>
      <li>Transaksi Penjualan</li>
      <li>Transaksi Pembelian</li>
    </ul>

    <div class="logout">Logout</div>
  </div>

  <!-- MAIN -->
  <div class="main">
    <h1>Dashboard Kasir</h1>

    <!-- WELCOME CARD -->
    <div class="welcome-card">
      <h2>Selamat Datang, kasir!</h2>
      <p>Hari ini adalah Selasa, 16 Desember 2025</p>
    </div>

    <!-- STATS -->
    <div class="stats">
      <div class="stat-card">
        <h3>Total Penjualan Hari Ini</h3>
        <p class="big">Rp 1.100.000</p>
        <span>1 transaksi</span>
      </div>

      <div class="stat-card">
        <h3>Barang Tersedia</h3>
        <p class="big">4</p>
        <span>Item produk</span>
      </div>
    </div>

    <!-- TABEL TRANSAKSI -->
    <div class="table-card">
      <h2>Transaksi Terakhir</h2>
      <table>
        <thead>
          <tr>
            <th>ID Penjualan</th>
            <th>Tanggal</th>
            <th>Total Harga</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>PJ002</td>
            <td>15/12/2025</td>
            <td>Rp 1.100.000</td>
            <td>Lunas</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

</div>

</body>
</html>