<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];
$queryHeader = "SELECT * FROM transaksi_beli WHERE idTransaksiBeli = $id";
$headerResult = mysqli_query($conn, $queryHeader);
$header = mysqli_fetch_assoc($headerResult);
if (!$header) {
    header("Location: riwayat_pembelian.php");
    exit;
}

$queryDetail = "SELECT b.nama_barang, d.jumlah, d.harga_satuan, d.subtotal 
                FROM detail_beli d 
                JOIN barang b ON d.idBarang = b.idBarang 
                WHERE d.idTransaksiBeli = $id";
$detailResult = mysqli_query($conn, $queryDetail);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Pembelian - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .detail-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .info-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0c38e; }
        .info-header p { margin: 5px 0; font-size: 16px; }
        .info-header strong { color: #312051; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #312051; color: white; }
        tr:hover { background: #f9f9f9; }
        .total-row { font-weight: bold; background: #f4f6f9; }
        .total-row td { border-top: 2px solid #312051; }
        .btn-back { background: #6c757d; color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; display: inline-block; margin-top: 20px; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Owner</p>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li class="active"><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Detail Pembelian</h1>
        </div>

        <div class="detail-container">
            <div class="info-header">
                <p><strong>ID Transaksi:</strong> <?= $header['idTransaksiBeli'] ?></p>
                <p><strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($header['tanggal'])) ?></p>
                <p><strong>Supplier:</strong> <?= htmlspecialchars($header['supplier']) ?></p>
            </div>

            <table>
                <thead>
                    <tr><th>Nama Barang</th><th>Jumlah</th><th>Harga Satuan</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($detailResult) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($detailResult)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= $row['jumlah'] ?> pcs</td>
                            <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong>Total Keseluruhan</strong></td>
                            <td><strong>Rp <?= number_format($header['total_harga'], 0, ',', '.') ?></strong></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center;">Tidak ada detail barang untuk transaksi ini</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="riwayat_pembelian.php" class="btn-back">← Kembali ke Riwayat</a>
        </div>
    </div>
</div>
<script src="../notifikasi.js"></script>
</body>
</html>