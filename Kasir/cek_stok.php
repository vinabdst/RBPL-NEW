<?php
// stok_barang.php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}
if ($_SESSION['role'] != 'Kasir') {
    header("Location: ../Owner/dashboard_admin.php");
    exit;
}

// Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Query barang dengan pencarian
$query = "SELECT idBarang, nama_barang, stok, harga_jual FROM barang";
if ($search != '') {
    $query .= " WHERE nama_barang LIKE '%$search%'";
}
$query .= " ORDER BY nama_barang ASC";
$result = mysqli_query($conn, $query);

// Statistik
$total_barang = mysqli_num_rows($result);
$stok_menipis = 0;
$stok_habis = 0;

// Hitung stok menipis & habis dari hasil query
$data_barang = [];
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['stok'] <= 0) {
        $stok_habis++;
    } elseif ($row['stok'] <= 5) {
        $stok_menipis++;
    }
    $data_barang[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cek Stok Barang - Kasir</title>
    <link rel="stylesheet" href="dashboard_kasir.css">
    <style>
        /* tambahan style untuk tabel stok dan badge */
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .search-bar input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 14px;
        }
        .search-bar button {
            background: #312051;
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 30px;
            cursor: pointer;
        }
        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        .stats-small {
            background: white;
            padding: 15px 20px;
            border-radius: 15px;
            flex: 1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .stats-small h4 {
            margin: 0 0 8px 0;
            color: #312051;
        }
        .stats-small .number {
            font-size: 28px;
            font-weight: bold;
            color: #F0C38E;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-success { background: #d4edda; color: #155724; }
        .table-card table th, .table-card table td {
            vertical-align: middle;
        }
        .btn-reset {
            background: #6c757d;
        }
        .btn-reset:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Kasir</p>
        <ul>
            <li><a href="dashboard_kasir.php">Dashboard</a></li>
            <li><a href="penjualan.php">Transaksi Penjualan</a></li>
            <li class="active"><a href="stok_barang.php">Cek Stok Barang</a></li>
        </ul>
        <div class="logout"><a href="../logout.php">Logout</a></div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Cek Stok Barang</h1>
            <div class="user"><?= htmlspecialchars($_SESSION['username']) ?></div>
        </div>

        <div class="welcome-card">
            <h2>Informasi Stok</h2>
            <p>Pantau ketersediaan barang sebelum bertransaksi.</p>
        </div>

        <!-- Statistik ringkas -->
        <div class="stats-row">
            <div class="stats-small">
                <h4>Total Barang</h4>
                <div class="number"><?= $total_barang ?></div>
                <span>jenis produk</span>
            </div>
            <div class="stats-small">
                <h4>Stok Menipis (≤5)</h4>
                <div class="number"><?= $stok_menipis ?></div>
                <span>perlu segera diisi ulang</span>
            </div>
            <div class="stats-small">
                <h4>Stok Habis (0)</h4>
                <div class="number"><?= $stok_habis ?></div>
                <span>tidak bisa dijual</span>
            </div>
        </div>

        <div class="table-card">
            <h2>Daftar Stok Barang</h2>

            <!-- Form pencarian -->
            <form method="GET" action="" class="search-bar">
                <input type="text" name="search" placeholder="Cari nama barang..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">🔍 Cari</button>
                <?php if ($search != ''): ?>
                    <a href="stok_barang.php" class="btn-reset" style="background:#6c757d; color:white; text-decoration:none; padding:0 20px; border-radius:30px; display:inline-flex; align-items:center;">Reset</a>
                <?php endif; ?>
            </form>

            <?php if (count($data_barang) > 0): ?>
                <table>
                    <thead>
                        <tr><th>No</th><th>Nama Barang</th><th>Stok</th><th>Harga Jual</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($data_barang as $item): ?>
                            <?php
                                $stok = $item['stok'];
                                if ($stok <= 0) {
                                    $status = '<span class="badge badge-danger">Habis</span>';
                                } elseif ($stok <= 5) {
                                    $status = '<span class="badge badge-warning">Menipis ('.$stok.')</span>';
                                } else {
                                    $status = '<span class="badge badge-success">Tersedia</span>';
                                }
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                                <td><?= $stok ?></td>
                                <td>Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                                <td><?= $status ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center; padding: 30px;">Tidak ada barang yang ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>