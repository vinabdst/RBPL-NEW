<?php
session_start();
include '../koneksi.php';

// Hanya Kasir yang boleh akses
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Kasir') {
    header("Location: ../index.php");
    exit;
}

// Ambil daftar barang yang stok > 0
$barang = mysqli_query($conn, "SELECT idBarang, nama_barang, stok, harga_jual FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Penjualan - Kasir</title>
    <link rel="stylesheet" href="dashboard_kasir.css">
    <style>
        .form-card { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .item-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; flex-wrap: wrap; }
        .item-row select, .item-row input { flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-add-item { background: #48426D; color: white; padding: 5px 10px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-submit { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .remove-item { color: red; cursor: pointer; font-weight: bold; margin-left: 5px; }
        .total-box { margin-top: 20px; font-size: 18px; font-weight: bold; text-align: right; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Kasir</p>
        <ul>
            <li><a href="dashboard_kasir.php">Dashboard</a></li>
            <li class="active"><a href="penjualan.php">Transaksi Penjualan</a></li>
            <li><a href="cek_stok.php">Cek Stok Barang</a></li>
        </ul>
        <div class="logout"><a href="../logout.php">Logout</a></div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Transaksi Penjualan</h1>
        </div>

        <div class="form-card">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error">❌ Stok tidak mencukupi atau terjadi kesalahan.</div>
            <?php endif; ?>
            <form method="POST" action="proses_penjualan.php" id="formPenjualan">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                </div><br>

                <h3>Detail Barang</h3>
                <div id="item-list">
                    <div class="item-row">
                        <select name="id_barang[]" required>
                            <option value="">Pilih Barang</option>
                            <?php while($row = mysqli_fetch_assoc($barang)): ?>
                                <option value="<?= $row['idBarang'] ?>" data-stok="<?= $row['stok'] ?>" data-harga="<?= $row['harga_jual'] ?>">
                                    <?= htmlspecialchars($row['nama_barang']) ?> (stok: <?= $row['stok'] ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="number" name="jumlah[]" placeholder="Jumlah" required min="1">
                        <input type="text" name="harga_satuan[]" placeholder="Harga Jual" readonly>
                        <span class="remove-item" onclick="this.parentElement.remove(); hitungTotal()">❌</span>
                    </div>
                </div>
                <button type="button" class="btn-add-item" onclick="tambahItem()">+ Tambah Barang</button>

                <div class="total-box">
                    Total Harga: Rp <span id="total_harga_text">0</span>
                    <input type="hidden" name="total_harga" id="total_harga_input">
                </div>

                <button type="submit" class="btn-submit" style="margin-top: 20px;">💾 Simpan & Cetak Nota</button>
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
                <option value="">Pilih Barang</option>
                <?php 
                $barang2 = mysqli_query($conn, "SELECT idBarang, nama_barang, stok, harga_jual FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
                while($r = mysqli_fetch_assoc($barang2)) {
                    echo "<option value='{$r['idBarang']}' data-stok='{$r['stok']}' data-harga='{$r['harga_jual']}'>" . htmlspecialchars($r['nama_barang']) . " (stok: {$r['stok']})</option>";
                }
                ?>
            </select>
            <input type="number" name="jumlah[]" placeholder="Jumlah" required min="1">
            <input type="text" name="harga_satuan[]" placeholder="Harga Jual" readonly>
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
            const hargaJual = selected.getAttribute('data-harga');
            hargaInput.value = hargaJual ? parseInt(hargaJual) : '';
            hitungTotal();
        }
        function cekStok() {
            const selected = select.options[select.selectedIndex];
            const stokTersedia = parseInt(selected.getAttribute('data-stok')) || 0;
            let jml = parseInt(jumlahInput.value) || 0;
            if (jml > stokTersedia) {
                alert('Jumlah melebihi stok yang tersedia! Maksimal ' + stokTersedia);
                jumlahInput.value = stokTersedia;
                hitungTotal();
            }
        }
        select.addEventListener('change', updateHarga);
        jumlahInput.addEventListener('input', function() { cekStok(); updateHarga(); });
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
        document.getElementById('total_harga_text').innerHTML = total.toLocaleString('id-ID');
        document.getElementById('total_harga_input').value = total;
    }

    document.querySelectorAll('.item-row').forEach(row => attachHargaEvent(row));
</script>
</body>
</html>