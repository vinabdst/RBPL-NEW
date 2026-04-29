<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

$query = "SELECT * FROM transaksi_beli ORDER BY idTransaksiBeli ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pembelian - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .table-container { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .btn-add { background: #48426D; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; display: inline-block; margin-bottom: 20px; }
        .btn-add:hover { background: #312051; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #312051; color: white; }
        tr:hover { background: #f5f5f5; }
        .detail-link { color: #48426D; text-decoration: none; font-weight: bold; }
        .detail-link:hover { text-decoration: underline; }
        .btn-back { background: #6c757d; color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; display: inline-block; margin-top: 20px; }
        .btn-back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Owner</p>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li class="active"><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Riwayat Pembelian</h1>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['idTransaksiBeli'] ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['supplier']) ?></td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><a href="detail_pembelian.php?id=<?= $row['idTransaksiBeli'] ?>" class="detail-link">📋 Detail</a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Belum ada transaksi pembelian</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="pembelian.php" class="btn-back">← Kembali</a>
        </div>
    </div>
</div>

<!-- Sertakan notifikasi.js jika ingin notifikasi muncul (opsional, karena sudah ada alert hijau) -->
<script src="../notifikasi.js"></script>
<script>
    // Jika ingin menggunakan notifikasi.js (agar muncul notifikasi sukses di tengah atas)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'added') {
        showNotification('Pembelian berhasil ditambahkan!', 'success');
        // Hapus parameter dari URL agar notifikasi tidak muncul berulang saat refresh
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
</script>
</body>
</html>