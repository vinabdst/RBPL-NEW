<?php
session_start();
include "../koneksi.php";

// Proteksi halaman
if(!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

// Cek role
if($_SESSION['role'] != 'Owner') {
    header("Location: ../Kasir/dashboard_kasir.php");
    exit;
}

// Query data (dengan penanganan error jika query gagal)
$totalBarang = 0;
$totalTransaksiBulan = 0;
$stokMenipis = 0;
$totalUser = 0;

$queryBarang = "SELECT COUNT(*) as total FROM barang";
$resBarang = mysqli_query($conn, $queryBarang);
if($resBarang && $row = mysqli_fetch_assoc($resBarang)) $totalBarang = $row['total'];

$queryStok = "SELECT COUNT(*) as menipis FROM barang WHERE stok <= 10";
$resStok = mysqli_query($conn, $queryStok);
if($resStok && $row = mysqli_fetch_assoc($resStok)) $stokMenipis = $row['menipis'];

$queryUser = "SELECT COUNT(*) as total FROM users";
$resUser = mysqli_query($conn, $queryUser);
if($resUser && $row = mysqli_fetch_assoc($resUser)) $totalUser = $row['total'];

// Total transaksi bulan ini (contoh placeholder)
$totalTransaksiBulan = 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
</head>
<body>
<div class="container">
    <!-- SIDEBAR -->
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
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main">
        <h1>Dashboard Admin</h1>

        <div class="welcome-card">
            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h2>
            <p><?= date('l, d F Y') ?></p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Barang</h3>
                <p class="big"><?= $totalBarang ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Transaksi (Bulan Ini)</h3>
                <p class="big"><?= $totalTransaksiBulan ?></p>
            </div>
            <div class="stat-card">
                <h3>Stok Menipis (≤ 10)</h3>
                <p class="big"><?= $stokMenipis ?></p>
            </div>
            <div class="stat-card">
                <h3>Total User</h3>
                <p class="big"><?= $totalUser ?></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>