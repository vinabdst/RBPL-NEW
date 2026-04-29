<?php
session_start();
include "../koneksi.php";

if(!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}
if($_SESSION['role'] != 'Owner') {
    header("Location: ../Kasir/dashboard_kasir.php");
    exit;
}

// Total barang
$totalBarang = 0;
$queryBarang = "SELECT COUNT(*) as total FROM barang";
$resBarang = mysqli_query($conn, $queryBarang);
if($resBarang && $row = mysqli_fetch_assoc($resBarang)) $totalBarang = $row['total'];

// Stok menipis (<=10)
$stokMenipis = 0;
$queryStok = "SELECT COUNT(*) as menipis FROM barang WHERE stok <= 10";
$resStok = mysqli_query($conn, $queryStok);
if($resStok && $row = mysqli_fetch_assoc($resStok)) $stokMenipis = $row['menipis'];

// Total user
$totalUser = 0;
$queryUser = "SELECT COUNT(*) as total FROM users";
$resUser = mysqli_query($conn, $queryUser);
if($resUser && $row = mysqli_fetch_assoc($resUser)) $totalUser = $row['total'];

// Omzet bulan ini
$bulan_ini = date('m');
$tahun_ini = date('Y');
$queryOmzet = "SELECT SUM(total_harga) as total FROM transaksi_jual WHERE MONTH(tanggal) = '$bulan_ini' AND YEAR(tanggal) = '$tahun_ini'";
$resOmzet = mysqli_query($conn, $queryOmzet);
$omzetBulanIni = 0;
if($resOmzet && $row = mysqli_fetch_assoc($resOmzet)) {
    $omzetBulanIni = $row['total'] ?? 0;
}

// Laba bersih (kotor) bulan ini
$queryModal = "SELECT SUM(d.jumlah * b.harga_beli) as total_modal 
               FROM detail_jual d 
               JOIN barang b ON d.idBarang = b.idBarang 
               JOIN transaksi_jual t ON d.idTransaksiJual = t.idTransaksiJual 
               WHERE MONTH(t.tanggal) = '$bulan_ini' AND YEAR(t.tanggal) = '$tahun_ini'";
$resModal = mysqli_query($conn, $queryModal);
$totalModal = 0;
if($resModal && $row = mysqli_fetch_assoc($resModal)) $totalModal = $row['total_modal'] ?? 0;
$labaBersih = $omzetBulanIni - $totalModal;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        /* Baris pertama 4 kolom */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 25px;
        }
        /* Baris kedua: hanya 1 card, dibuat di tengah dengan lebar sesuai card lain */
        .stats-row-single {
            display: flex;
            justify-content: center;
        }
        .stats-row-single .stat-card {
            width: calc(25% - 19px); /* agar seukuran dengan card di baris pertama */
            min-width: 200px;
        }
        @media (max-width: 1000px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Owner</p>
        <ul>
            <li class="active"><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout"><a href="../logout.php">Logout</a></div>
    </div>

    <div class="main">
        <h1>Dashboard Admin</h1>
        <div class="welcome-card">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h2>
            <p><?= date('l, d F Y') ?></p>
        </div>

        <!-- Baris pertama: 4 card -->
        <div class="stats-row">
            <div class="stat-card">
                <h3>Total Barang</h3>
                <p class="big"><?= $totalBarang ?></p>
            </div>
            <div class="stat-card">
                <h3>Omzet (Bulan Ini)</h3>
                <p class="big">Rp <?= number_format($omzetBulanIni, 0, ',', '.') ?></p>
            </div>
            <div class="stat-card">
                <h3>Stok Menipis (≤10)</h3>
                <p class="big"><?= $stokMenipis ?></p>
            </div>
            <div class="stat-card">
                <h3>Total User</h3>
                <p class="big"><?= $totalUser ?></p>
            </div>
        </div>

        <div class="stats-row-single">
            <div class="stat-card">
                <h3>Laba Bersih (Bulan Ini)</h3>
                <p class="big">Rp <?= number_format($labaBersih, 0, ',', '.') ?></p>
            </div>
        </div>
    </div>
</div>
<script src="../notifikasi.js"></script>
</body>
</html>