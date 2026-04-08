<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM barang WHERE idBarang = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
if (!$data) {
    header("Location: barang.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_logam']);
    $berat = (float)$_POST['berat_gram'];
    $harga_beli = (float)$_POST['harga_beli'];
    $harga_jual = (float)$_POST['harga_jual'];
    $stok = (int)$_POST['stok'];

    $update = "UPDATE barang SET 
                nama_barang='$nama', 
                jenis_logam='$jenis', 
                berat_gram='$berat', 
                harga_beli='$harga_beli', 
                harga_jual='$harga_jual', 
                stok='$stok' 
                WHERE idBarang=$id";
    if (mysqli_query($conn, $update)) {
        header("Location: barang.php?status=updated");
        exit;
    } else {
        $error = "Gagal mengupdate: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
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
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <ul>
            <li><a href="dashboard_admin.php" style="color: white; text-decoration: none;">Dashboard</a></li>
            <li class="active"><a href="barang.php" style="color: white; text-decoration: none;">Data Barang</a></li>
            <li>Transaksi Pembelian</li>
            <li>Transaksi Penjualan</li>
            <li>Laporan</li>
            <li>Kelola User</li>
            <li><a href="logout.php" style="color: white; text-decoration: none;">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Edit Barang</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <div class="form-container">
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Jenis Logam</label>
                    <input type="text" name="jenis_logam" value="<?= htmlspecialchars($data['jenis_logam']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Berat (gram)</label>
                    <input type="number" step="0.01" name="berat_gram" value="<?= $data['berat_gram'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga Beli (Rp)</label>
                    <input type="number" step="1000" name="harga_beli" value="<?= $data['harga_beli'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Harga Jual (Rp)</label>
                    <input type="number" step="1000" name="harga_jual" value="<?= $data['harga_jual'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= $data['stok'] ?>" required>
                </div>
                <button type="submit" class="btn-submit">Update</button>
                <a href="barang.php" style="margin-left: 10px;">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>