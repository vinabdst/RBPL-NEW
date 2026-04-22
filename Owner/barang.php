<?php
session_start();
include '../koneksi.php';

// Proteksi: hanya Owner yang boleh akses
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

// Ambil data barang dengan pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT * FROM barang";
if (!empty($search)) {
    $query .= " WHERE nama_barang LIKE '%$search%' OR jenis_logam LIKE '%$search%'";
}
$query .= " ORDER BY idBarang ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Barang - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .main { padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; color: white; display: inline-block; margin: 2px; }
        .btn-add { background: #48426D; }
        .btn-edit { background: #F0C38E; color: #312051; }
        .btn-delete { background: #ff4444; }
        .btn-search { background: #312051; border: none; cursor: pointer; color: white; padding: 10px 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #312051; color: white; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .search-box { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-box input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
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
            <li><a href="pembelian.php" style="color: white; text-decoration: none;">Transaksi Pembelian</a></li>
            <li><a href="laporan.php" style="color: white; text-decoration: none;">Laporan</a></li>
            <li><a href="user.php" style="color: white; text-decoration: none;">Kelola User</a></li>
            <li><a href="../logout.php" style="color: white; text-decoration: none;">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Data Barang</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <div class="card">
            <!-- Tombol Tambah & Pencarian -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="tambah_barang.php" class="btn btn-add">➕ Tambah Barang</a>
                <form method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Cari nama barang atau jenis logam..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-search">Cari</button>
                </form>
            </div>

            <!-- Tabel Barang -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Barang</th>
                        <th>Jenis Logam</th>
                        <th>Berat (gram)</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['idBarang'] ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><?= htmlspecialchars($row['jenis_logam']) ?></td>
                            <td><?= number_format($row['berat_gram'], 0) ?> gr</td>
                            <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                            <td><?= $row['stok'] ?></td>
                            <td>
                                <a href="edit_barang.php?id=<?= $row['idBarang'] ?>" class="btn btn-edit">✏️ Edit</a>
                                <a href="hapus_barang.php?id=<?= $row['idBarang'] ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus barang ini?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">Tidak ada data barang</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="notifikasi.js"></script>

</body>
</html>