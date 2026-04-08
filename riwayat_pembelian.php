<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}

$query = "SELECT * FROM transaksi_beli ORDER BY idTransaksiBeli DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pembelian</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .table-container { background: white; padding: 20px; border-radius: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; }
        th { background: #312051; color: white; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li class="active"><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="#">Transaksi Penjualan</a></li>
            <li><a href="#">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Riwayat Pembelian</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <div class="table-container">
            <a href="pembelian.php" class="btn btn-add" style="margin-bottom: 15px;">➕ Tambah Pembelian</a>
            <table>
                <thead>
                    <tr><th>ID</th><th>Tanggal</th><th>Supplier</th><th>Total Harga</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['idTransaksiBeli'] ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= htmlspecialchars($row['supplier']) ?></td>
                        <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                        <td><a href="detail_pembelian.php?id=<?= $row['idTransaksiBeli'] ?>">Detail</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>