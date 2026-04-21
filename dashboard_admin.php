<?php
session_start();
include "koneksi.php";

// Proteksi halaman
if(!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Cek role
if($_SESSION['role'] != 'Owner') {
    header("Location: dashboard_kasir.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
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
<div class="dashboard">
    <!-- SIDEBAR (konsisten dengan halaman barang) -->
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <ul>
            <li class="active"><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="penjualan.php">Transaksi Penjualan</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- MAIN CONTENT (konsisten dengan halaman tambah_barang) -->
    <div class="main">
        <div class="topbar">
            <h1>Dashboard Admin</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <!-- Welcome card (opsional, mirip dengan form-card tapi untuk sambutan) -->
        <div class="form-card" style="margin-bottom: 30px;">
            <h2 style="color: #312051; margin-top: 0;">Selamat Datang, <?= $_SESSION['nama'] ?>!</h2>
            <p style="color: #555;">Anda login sebagai <strong>Owner</strong>. Kelola data barang, transaksi, dan laporan di sini.</p>
        </div>

        <!-- Cards statistik -->
        <div class="cards">
            <div class="card">
                <h3>Total Barang</h3>
                <p>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM barang";
                    $res = mysqli_query($conn, $query);
                    $row = mysqli_fetch_assoc($res);
                    echo $row['total'];
                    ?>
                </p>
            </div>
            <div class="card">
                <h3>Total Transaksi (Bulan Ini)</h3>
                <p>
                    <?php
                    // Contoh: hitung dari tabel penjualan (sementara 0 karena belum ada)
                    echo "0";
                    ?>
                </p>
            </div>
            <div class="card">
                <h3>Stok Menipis (≤ 10)</h3>
                <p>
                    <?php
                    $query = "SELECT COUNT(*) as menipis FROM barang WHERE stok <= 10";
                    $res = mysqli_query($conn, $query);
                    $row = mysqli_fetch_assoc($res);
                    echo $row['menipis'];
                    ?>
                </p>
            </div>
            <div class="card">
                <h3>Total User</h3>
                <p>
                    <?php
                    $query = "SELECT COUNT(*) as total FROM users";
                    $res = mysqli_query($conn, $query);
                    $row = mysqli_fetch_assoc($res);
                    echo $row['total'];
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>