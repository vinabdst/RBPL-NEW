<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}
$query = "SELECT * FROM transaksi_jual ORDER BY idTransaksiJual DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Penjualan</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .table-container { background: white; padding: 20px; border-radius: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; }
        th { background: #312051; color: white; }
        .btn { padding: 6px 12px; background: #48426D; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li class="active"><a href="riwayat_penjualan.php">Transaksi Penjualan</a></li>
            <li><a href="#">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main">
        <div class="topbar">
            <h1>Riwayat Penjualan</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>
        <div class="table-container">
            <a href="penjualan.php" class="btn" style="margin-bottom:15px; display:inline-block;">➕ Tambah Penjualan</a>
            <table>
                <thead><tr><th>ID</th><th>Tanggal</th><th>Kasir</th><th>Total Harga</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['idTransaksiJual'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= htmlspecialchars($row['kasir']) ?></td>
                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                    <td><a href="cetak_nota.php?id=<?= $row['idTransaksiJual'] ?>">Cetak Nota</a></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>