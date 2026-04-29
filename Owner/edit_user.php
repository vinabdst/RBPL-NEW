<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM users WHERE idUser = $id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
if (!$user) {
    header("Location: user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $new_password = $_POST['password'];

    $update = "UPDATE users SET username='$username', nama='$nama', role='$role'";
    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update .= ", password='$hashed'";
    }
    $update .= " WHERE idUser=$id";

    if (mysqli_query($conn, $update)) {
        header("Location: user.php?status=updated");
        exit;
    } else {
        $error = "Gagal update: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .form-card { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Quicksand', sans-serif; }
        .btn-submit { background: #48426D; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Quicksand', sans-serif; }
        .btn-cancel { background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 6px; margin-left: 10px; text-decoration: none; font-family: 'Quicksand', sans-serif; }
        .btn-cancel:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Owner</p>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="./barang.php">Data Barang</a></li>
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li class="active"><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Edit User</h1>
        </div>

        <div class="form-card">
            <?php if (isset($error)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 20px;"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="Kasir" <?= $user['role'] == 'Kasir' ? 'selected' : '' ?>>Kasir</option>
                        <option value="Owner" <?= $user['role'] == 'Owner' ? 'selected' : '' ?>>Owner</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn-submit">Update</button>
                    <a href="user.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../notifikasi.js"></script>
</body>
</html>