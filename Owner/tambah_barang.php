<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_logam']);
    $berat = (float)$_POST['berat_gram'];
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];

    $insert = "INSERT INTO barang (nama_barang, jenis_logam, berat_gram, harga_beli, harga_jual, stok)
               VALUES ('$nama', '$jenis', '$berat', '$harga_beli', '$harga_jual', '$stok')";
    if (mysqli_query($conn, $insert)) {
        header("Location: barang.php?status=added");
        exit;
    } else {
        $error = "Gagal menambah: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .form-container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-submit { background: #48426D; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-submit:hover { background: #312051; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Owner</p>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li class="active"><a href="barang.php">Data Barang</a></li>
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Tambah Barang</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <div class="form-container">
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" placeholder="Contoh: Timbal Batangan" required>
                </div>
                <div class="form-group">
                    <label>Jenis Logam</label>
                    <input type="text" name="jenis_logam" placeholder="Contoh: Timbal, Tembaga, Kuningan" required>
                </div>
                <div class="form-group">
                    <label>Berat (gram)</label>
                    <input type="number" step="0.01" name="berat_gram" required>
                </div>
                <div class="form-group">
                    <label>Harga Beli (Rp)</label>
                    <input type="number" step="1000" name="harga_beli" required>
                </div>
                <div class="form-group">
                    <label>Harga Jual (Rp)</label>
                    <input type="number" step="1000" name="harga_jual" required>
                </div>
                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="number" name="stok" value="0" required>
                </div>
                <button type="submit" class="btn-submit">Simpan</button>
                <a href="barang.php" style="margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>