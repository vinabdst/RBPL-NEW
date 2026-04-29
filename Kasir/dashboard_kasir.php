<?php
session_start();
include "../koneksi.php";

if(!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}
if($_SESSION['role'] != 'Kasir') {
    header("Location: ../Owner/dashboard_admin.php");
    exit;
}

// Total penjualan hari ini
$hari_ini = date('Y-m-d');
$query_hari = "SELECT SUM(total_harga) as total, COUNT(*) as jumlah FROM transaksi_jual WHERE tanggal = '$hari_ini'";
$res_hari = mysqli_query($conn, $query_hari);
$data_hari = mysqli_fetch_assoc($res_hari);
$total_hari = $data_hari['total'] ?? 0;
$jml_transaksi = $data_hari['jumlah'] ?? 0;

// Jumlah barang dengan stok > 0
$query_stok = "SELECT COUNT(*) as jml FROM barang WHERE stok > 0";
$res_stok = mysqli_query($conn, $query_stok);
$jml_barang = mysqli_fetch_assoc($res_stok)['jml'] ?? 0;

// 5 transaksi terakhir
$query_transaksi = "SELECT idTransaksiJual, tanggal, total_harga FROM transaksi_jual ORDER BY idTransaksiJual DESC LIMIT 5";
$transaksi_terakhir = mysqli_query($conn, $query_transaksi);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="dashboard_kasir.css">
    <style>
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .stat-card .big { font-size: 28px; font-weight: bold; margin: 10px 0; color: #F0C38E; }
        .table-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Kasir</p>
        <ul>
            <li class="active"><a href="dashboard_kasir.php">Dashboard</a></li>
            <li><a href="penjualan.php" style="color:white; text-decoration:none;">Transaksi Penjualan</a></li>
            <li><a href="cek_stok.php" style="color:white; text-decoration:none;">Cek Stok Barang</a></li>
        </ul>
        <div class="logout"><a href="../logout.php" style="color:white; text-decoration:none;">Logout</a></div>
    </div>
    <div class="main">
        <div class="page-header">
            <h1>Dashboard Admin</h1>
        </div>
        
        <div class="welcome-card">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h2>
            <p><?= date('l, d F Y') ?></p>
        </div>
        <div class="stats">
            <div class="stat-card">
                <h3>Total Penjualan Hari Ini</h3>
                <p class="big">Rp <?= number_format($total_hari, 0, ',', '.') ?></p>
                <span><?= $jml_transaksi ?> transaksi</span>
            </div>
            <div class="stat-card">
                <h3>Barang Tersedia</h3>
                <p class="big"><?= $jml_barang ?></p>
                <span>Item produk dengan stok > 0</span>
            </div>
        </div>
        <div class="table-card">
            <h2>Transaksi Terakhir</h2>
            <table>
                <thead>
                    <tr><th>ID Penjualan</th><th>Tanggal</th><th>Total Harga</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($transaksi_terakhir) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($transaksi_terakhir)): ?>
                        <tr>
                            <td><?= $row['idTransaksiJual'] ?></td>
                            <td><?= $row['tanggal'] ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td>Lunas</td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Belum ada transaksi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>