<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];
// Ambil header termasuk created_at (waktu real) dan nama kasir dari users
$queryHeader = "SELECT t.*, u.nama as nama_kasir, t.created_at 
                FROM transaksi_jual t 
                LEFT JOIN users u ON t.kasir = u.username 
                WHERE t.idTransaksiJual = $id";
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #48426D;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Courier New', 'Lucida Sans Typewriter', monospace;
            padding: 20px;
        }
        .nota-wrapper {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .nota {
            width: 430px;
            margin: 0 auto;
            background: white;
            font-size: 13px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #aaa;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header h3 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #555;
            margin: 2px 0;
        }
        .info {
            margin-bottom: 15px;
            font-size: 12px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 2px 0;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items th, .items td {
            padding: 6px 5px;
            border-bottom: 1px dotted #ccc;
        }
        .items th {
            border-bottom: 1px solid #000;
            text-align: left;
            font-weight: bold;
        }
        .items th:nth-child(1), .items td:nth-child(1) { width: 35%; }
        .items th:nth-child(2), .items td:nth-child(2) { width: 10%; text-align: center; }
        .items th:nth-child(3), .items td:nth-child(3) { width: 24%; text-align: right; }
        .items th:nth-child(4), .items td:nth-child(4) { width: 26%; text-align: right; }
        
        .total {
            text-align: right;
            border-top: 1px dashed #aaa;
            padding-top: 8px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 18px;
            font-size: 11px;
            color: #777;
            border-top: 1px dashed #aaa;
            padding-top: 10px;
        }
        .btn-group {
            text-align: center;
            margin-top: 25px;
        }
        button, .btn-back {
            background: #312051;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 30px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            margin: 0 5px;
            text-decoration: none;
            display: inline-block;
        }
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .nota-wrapper {
                padding: 0;
                box-shadow: none;
            }
            .btn-group {
                display: none;
            }
            .nota {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
<div class="nota-wrapper">
    <div class="nota">
        <div class="header">
            <h3>TOKO BAHAN LOGAM</h3>
            <p>Jl. Kemasan, Kotagede,Yogyakarta</p>
            <p>Telp: (031) 1234-5678</p>
        </div>
        
        <div class="info">
            <table>
                <tr><td width="80">No. Nota</td><td>: <?= str_pad($header['idTransaksiJual'], 6, '0', STR_PAD_LEFT) ?></td></tr>
                <tr><td>Tanggal</td><td>: <?= date('d/m/Y H:i:s', strtotime($header['created_at'])) ?></td></tr>
                <tr><td>Kasir</td><td>: <?= htmlspecialchars($header['nama_kasir'] ?? $header['kasir']) ?></td></tr>
            </table>
        </div>
        
        <table class="items">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga (Rp)</th>
                    <th>Subtotal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_all = 0;
                while($row = mysqli_fetch_assoc($detail)): 
                    $subtotal = $row['subtotal'];
                    $total_all += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <div class="total">
            Total: Rp <?= number_format($total_all, 0, ',', '.') ?>
        </div>
        
        <div class="footer">
            Terima kasih atas pembelian Anda<br>
            Barang yang sudah dibeli tidak dapat dikembalikan
        </div>
    </div>
    
    <div class="btn-group">
        <button onclick="window.print()">🖨️ Cetak Nota</button>
        <a href="<?= ($_SESSION['role'] == 'Owner') ? 'dashboard_admin.php' : 'dashboard_kasir.php' ?>" class="btn-back">← Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>