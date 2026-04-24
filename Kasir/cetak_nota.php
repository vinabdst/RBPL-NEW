<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];
$queryHeader = "SELECT * FROM transaksi_jual WHERE idTransaksiJual = $id";
$header = mysqli_fetch_assoc(mysqli_query($conn, $queryHeader));
if (!$header) die("Transaksi tidak ditemukan.");

$queryDetail = "SELECT b.nama_barang, d.jumlah, d.harga_satuan, d.subtotal 
                FROM detail_jual d JOIN barang b ON d.idBarang = b.idBarang 
                WHERE d.idTransaksiJual = $id";
$detail = mysqli_query($conn, $queryDetail);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nota Penjualan</title>
    <style>
        body { font-family: 'Courier New', monospace; padding: 20px; }
        .nota { width: 300px; margin: auto; border: 1px solid #000; padding: 15px; background: #fff; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; }
        .items { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items th, .items td { border-bottom: 1px dotted #000; text-align: left; padding: 5px 0; }
        .total { text-align: right; margin-top: 10px; font-weight: bold; border-top: 1px solid #000; padding-top: 5px; }
        .footer { text-align: center; margin-top: 15px; font-size: 12px; }
        button { margin-top: 20px; padding: 8px 15px; cursor: pointer; background: #312051; color: white; border: none; border-radius: 5px; }
        button:hover { background: #48426D; }
        a { color: #312051; }
    </style>
</head>
<body>
<div class="nota">
    <div class="header">
        <h3>TOKO BAHAN LOGAM</h3>
        <p><?= date('d/m/Y', strtotime($header['tanggal'])) ?><br>Kasir: <?= htmlspecialchars($header['kasir']) ?></p>
    </div>
    <table class="items">
        <thead>
            <tr><th>Barang</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($detail)): ?>
        <tr>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= number_format($row['harga_satuan'], 0) ?></td>
            <td><?= number_format($row['subtotal'], 0) ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="total">
        Total: Rp <?= number_format($header['total_harga'], 0, ',', '.') ?>
    </div>
    <div class="footer">
        Terima kasih atas pembelian Anda
    </div>
</div>
<div style="text-align:center; margin-top:20px;">
    <button onclick="window.print()">🖨️ Cetak Nota</button><br><br>
    <a href="dashboard_<?= strtolower($_SESSION['role']) ?>.php">← Kembali ke Dashboard</a>
</div>
</body>
</html>