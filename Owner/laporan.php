<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

// Ambil parameter filter
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'bulanan';
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01');
$tanggal_selesai = isset($_GET['tanggal_selesai']) ? $_GET['tanggal_selesai'] : date('Y-m-d');

// Query laporan penjualan
$sql_penjualan = "";
if ($periode == 'harian') {
    $sql_penjualan = "SELECT tanggal, total_harga FROM transaksi_jual WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai' ORDER BY tanggal";
} elseif ($periode == 'tahunan') {
    $sql_penjualan = "SELECT MONTH(tanggal) as bulan, SUM(total_harga) as total FROM transaksi_jual WHERE YEAR(tanggal) = $tahun GROUP BY MONTH(tanggal) ORDER BY bulan";
} else { // bulanan
    $sql_penjualan = "SELECT tanggal, total_harga FROM transaksi_jual WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun ORDER BY tanggal";
}
$result_penjualan = mysqli_query($conn, $sql_penjualan);

// Query laporan pembelian
$sql_pembelian = "";
if ($periode == 'harian') {
    $sql_pembelian = "SELECT tanggal, total_harga FROM transaksi_beli WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai' ORDER BY tanggal";
} elseif ($periode == 'tahunan') {
    $sql_pembelian = "SELECT MONTH(tanggal) as bulan, SUM(total_harga) as total FROM transaksi_beli WHERE YEAR(tanggal) = $tahun GROUP BY MONTH(tanggal) ORDER BY bulan";
} else {
    $sql_pembelian = "SELECT tanggal, total_harga FROM transaksi_beli WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun ORDER BY tanggal";
}
$result_pembelian = mysqli_query($conn, $sql_pembelian);

// Query stok barang
$sql_stok = "SELECT nama_barang, stok FROM barang ORDER BY stok ASC";
$result_stok = mysqli_query($conn, $sql_stok);

// Hitung total omset & laba kotor
$total_penjualan = 0;
$total_pembelian = 0;
if ($periode == 'harian') {
    $q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi_jual WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'");
    $total_penjualan = mysqli_fetch_assoc($q)['total'] ?? 0;
    $q2 = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi_beli WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'");
    $total_pembelian = mysqli_fetch_assoc($q2)['total'] ?? 0;
} elseif ($periode == 'tahunan') {
    $q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi_jual WHERE YEAR(tanggal) = $tahun");
    $total_penjualan = mysqli_fetch_assoc($q)['total'] ?? 0;
    $q2 = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi_beli WHERE YEAR(tanggal) = $tahun");
    $total_pembelian = mysqli_fetch_assoc($q2)['total'] ?? 0;
} else {
    $q = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi_jual WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun");
    $total_penjualan = mysqli_fetch_assoc($q)['total'] ?? 0;
    $q2 = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi_beli WHERE MONTH(tanggal) = $bulan AND YEAR(tanggal) = $tahun");
    $total_pembelian = mysqli_fetch_assoc($q2)['total'] ?? 0;
}
$laba_kotor = $total_penjualan - $total_pembelian;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .main {
            padding-bottom: 60px;
        }
        /* Perbaikan: Tombol Cetak & Export UKURAN SAMA */
        .action-buttons {
            text-align: right;
            margin-top: 30px;
            margin-bottom: 40px;
            clear: both;
        }
        .btn-export, .btn-print {
            display: inline-block;
            padding: 8px 18px;          /* padding sama */
            font-size: 14px;             /* font sama */
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            border: none;
            border-radius: 6px;          /* sudut sama */
            cursor: pointer;
            text-decoration: none;
            margin-left: 12px;
            transition: 0.2s;
            text-align: center;
            min-width: 130px;            /* lebar minimal sama (opsional) */
        }
        .btn-export {
            background: #28a745;
            color: white;
        }
        .btn-print {
            background: #17a2b8;
            color: white;
        }
        .btn-export:hover, .btn-print:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        /* Sisanya tetap seperti semula */
        .filter-card, .stats-card, .table-card { background: white; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .filter-form { display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; }
        .filter-form .form-group { margin-bottom: 0; }
        .filter-form label { font-weight: bold; display: block; margin-bottom: 5px; }
        .filter-form input, .filter-form select { padding: 8px; border-radius: 5px; border: 1px solid #ddd; }
        .btn-filter { background: #48426D; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .stat-box { background: #f4f6f9; padding: 15px; border-radius: 10px; text-align: center; }
        .stat-box h3 { margin: 0; color: #555; font-size: 14px; }
        .stat-box .value { font-size: 24px; font-weight: bold; color: #F0C38E; margin: 10px 0 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #312051; color: white; }
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
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li class="active"><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Laporan Bisnis</h1>
        </div>

        <!-- Filter -->
        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>Periode</label>
                    <select name="periode" id="periode" onchange="this.form.submit()">
                        <option value="harian" <?= $periode == 'harian' ? 'selected' : '' ?>>Harian</option>
                        <option value="bulanan" <?= $periode == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                        <option value="tahunan" <?= $periode == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                    </select>
                </div>
                <div id="range_harian" style="display: <?= $periode == 'harian' ? 'flex' : 'none' ?>; gap: 10px;">
                    <div class="form-group">
                        <label>Dari tanggal</label>
                        <input type="date" name="tanggal_mulai" value="<?= $tanggal_mulai ?>">
                    </div>
                    <div class="form-group">
                        <label>Sampai tanggal</label>
                        <input type="date" name="tanggal_selesai" value="<?= $tanggal_selesai ?>">
                    </div>
                </div>
                <div id="range_bulanan" style="display: <?= $periode == 'bulanan' ? 'flex' : 'none' ?>; gap: 10px;">
                    <div class="form-group">
                        <label>Bulan</label>
                        <select name="bulan">
                            <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="tahun">
                            <?php for($y=2020; $y<=date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div id="range_tahunan" style="display: <?= $periode == 'tahunan' ? 'flex' : 'none' ?>; gap: 10px;">
                    <div class="form-group">
                        <label>Tahun</label>
                        <select name="tahun">
                            <?php for($y=2020; $y<=date('Y'); $y++): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-filter">Tampilkan</button>
            </form>
        </div>

        <!-- Statistik Ringkasan -->
        <div class="stats-grid">
            <div class="stat-box">
                <h3>Total Penjualan</h3>
                <p class="value">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Pembelian</h3>
                <p class="value">Rp <?= number_format($total_pembelian, 0, ',', '.') ?></p>
            </div>
            <div class="stat-box">
                <h3>Laba Kotor</h3>
                <p class="value">Rp <?= number_format($laba_kotor, 0, ',', '.') ?></p>
            </div>
        </div>

        <!-- Tabel Penjualan -->
        <div class="table-card">
            <h2>Detail Penjualan</h2>
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Total Harga</th></tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result_penjualan) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result_penjualan)): ?>
                        <tr>
                            <td><?= $periode == 'tahunan' ? bulanIndo($row['bulan']) : $row['tanggal'] ?></td>
                            <td>Rp <?= number_format($row['total'] ?? $row['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">Tidak ada data penjualan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Pembelian -->
        <div class="table-card">
            <h2>Detail Pembelian</h2>
            <table>
                <thead><tr><th>Tanggal</th><th>Total Harga</th></tr></thead>
                <tbody>
                    <?php if(mysqli_num_rows($result_pembelian) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result_pembelian)): ?>
                        <tr>
                            <td><?= $periode == 'tahunan' ? bulanIndo($row['bulan']) : $row['tanggal'] ?></td>
                            <td>Rp <?= number_format($row['total'] ?? $row['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">Tidak ada data pembelian</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Stok -->
        <div class="table-card">
            <h2>Status Stok Barang</h2>
            <table>
                <thead><tr><th>Nama Barang</th><th>Stok (gram)</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result_stok)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= number_format($row['stok'], 0) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Ekspor & Cetak - UKURAN SUDAH SAMA -->
        <div class="action-buttons">
            <a href="export_laporan_excel.php?periode=<?= $periode ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&tanggal_mulai=<?= $tanggal_mulai ?>&tanggal_selesai=<?= $tanggal_selesai ?>" class="btn-export">📊 Export Excel</a>
            <button class="btn-print" onclick="window.print()">🖨️ Cetak Laporan</button>
        </div>
    </div>
</div>
<script>
    const periodeSelect = document.getElementById('periode');
    function toggleFilters() {
        const val = periodeSelect.value;
        document.getElementById('range_harian').style.display = val === 'harian' ? 'flex' : 'none';
        document.getElementById('range_bulanan').style.display = val === 'bulanan' ? 'flex' : 'none';
        document.getElementById('range_tahunan').style.display = val === 'tahunan' ? 'flex' : 'none';
    }
    periodeSelect.addEventListener('change', toggleFilters);
    toggleFilters();
</script>
</body>
</html>
<?php
function bulanIndo($bulan) {
    $nama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return $nama[(int)$bulan];
}
?>