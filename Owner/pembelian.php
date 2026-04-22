<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

// Ambil daftar barang untuk dropdown
$barang = mysqli_query($conn, "SELECT idBarang, nama_barang, stok, harga_beli FROM barang ORDER BY nama_barang ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Pembelian</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .form-card { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: bold; display: block; margin-bottom: 5px; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn-add-item { background: #48426D; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-submit { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .remove-item { color: red; cursor: pointer; }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="barang.php">Data Barang</a></li>
            <li class="active"><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li><a href="user.php">Kelola User</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Transaksi Pembelian dari Supplier</h1>
            <div class="user"><?= $_SESSION['username'] ?></div>
        </div>

        <div class="form-card">
            <form method="POST" action="proses_pembelian.php" id="formPembelian">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" name="supplier" placeholder="Nama supplier" required>
                </div>

                <h3>Detail Barang</h3>
                <div id="item-list">
                    <div class="item-row">
                        <select name="id_barang[]" required>
                            <option value="">Pilih Barang</option>
                            <?php while($row = mysqli_fetch_assoc($barang)): ?>
                                <option value="<?= $row['idBarang'] ?>" data-harga="<?= $row['harga_beli'] ?>">
                                    <?= $row['nama_barang'] ?> (stok: <?= $row['stok'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="number" name="jumlah[]" placeholder="Jumlah" required>
                        <input type="text" name="harga_satuan[]" placeholder="Harga satuan" readonly>
                        <span class="remove-item" onclick="this.parentElement.remove()">❌</span>
                    </div>
                </div>
                <button type="button" class="btn-add-item" onclick="tambahItem()">+ Tambah Barang</button>

                <div class="form-group" style="margin-top: 20px;">
                    <label>Total Harga</label>
                    <input type="text" id="total_harga" name="total_harga" readonly>
                </div>

                <button type="submit" class="btn-submit">Simpan Transaksi</button>
            </form>
        </div>
    </div>
</div>

<script>
    function tambahItem() {
        const container = document.getElementById('item-list');
        const newRow = document.createElement('div');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <select name="id_barang[]" required>
                <?php 
                $barang2 = mysqli_query($conn, "SELECT idBarang, nama_barang, stok, harga_beli FROM barang ORDER BY nama_barang ASC");
                $options = "";
                while($r = mysqli_fetch_assoc($barang2)) {
                    $options .= "<option value='{$r['idBarang']}' data-harga='{$r['harga_beli']}'>{$r['nama_barang']} (stok: {$r['stok']})</option>";
                }
                echo $options;
                ?>
            </select>
            <input type="number" name="jumlah[]" placeholder="Jumlah" required>
            <input type="text" name="harga_satuan[]" placeholder="Harga satuan" readonly>
            <span class="remove-item" onclick="this.parentElement.remove(); hitungTotal()">❌</span>
        `;
        container.appendChild(newRow);
        attachHargaEvent(newRow);
    }

    function attachHargaEvent(row) {
        const select = row.querySelector('select');
        const jumlahInput = row.querySelector('input[name="jumlah[]"]');
        const hargaInput = row.querySelector('input[name="harga_satuan[]"]');
        
        function updateHarga() {
            const selected = select.options[select.selectedIndex];
            const hargaBeli = selected.getAttribute('data-harga');
            hargaInput.value = hargaBeli ? parseInt(hargaBeli) : '';
            hitungTotal();
        }
        select.addEventListener('change', updateHarga);
        jumlahInput.addEventListener('input', hitungTotal);
    }

    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const jumlah = row.querySelector('input[name="jumlah[]"]').value;
            const harga = row.querySelector('input[name="harga_satuan[]"]').value;
            if (jumlah && harga) {
                total += parseInt(jumlah) * parseInt(harga);
            }
        });
        document.getElementById('total_harga').value = total;
    }

    // Attach event ke item yang sudah ada
    document.querySelectorAll('.item-row').forEach(row => attachHargaEvent(row));
</script>
</body>
</html>