<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT b.nama_barang, d.jumlah, d.harga_satuan, d.subtotal 
          FROM detail_beli d 
          JOIN barang b ON d.idBarang = b.idBarang 
          WHERE d.idTransaksiBeli = $id";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Pembelian</title>
    <link rel="stylesheet" href="dashboard_admin.css">
</head>
<body>
<div class="dashboard">
    <div class="sidebar">...</div>
    <div class="main">
        <div class="topbar"><h1>Detail Pembelian</h1><div class="user"><?= $_SESSION['username'] ?></div></div>
        <div class="table-container">
            <table>
                <thead><tr><th>Barang</th><th>Jumlah</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="riwayat_pembelian.php">Kembali</a>
        </div>
    </div>
</div>
</body>
</html>