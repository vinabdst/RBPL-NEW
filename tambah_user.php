<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $check = mysqli_query($conn, "SELECT idUser FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        $insert = "INSERT INTO users (username, password, nama, role) 
                   VALUES ('$username', '$password', '$nama', '$role')";
        if (mysqli_query($conn, $insert)) {
            header("Location: user.php?status=added");
            exit;
        } else {
            $error = "Gagal menambah user: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah User</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .form-card { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .btn-submit { background: #48426D; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
        .btn-cancel { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 6px; margin-left: 10px; text-decoration: none; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li><a href="#">Transaksi Pembelian</a></li>
            <li><a href="#">Transaksi Penjualan</a></li>
            <li><a href="#">Laporan</a></li>
            <li class="active"><a href="user.php">Kelola User</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Tambah User</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <div class="form-card">
            <?php if (isset($error)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 20px;"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="Kasir">Kasir</option>
                        <option value="Owner">Owner</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-submit">Simpan</button>
                    <a href="user.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>