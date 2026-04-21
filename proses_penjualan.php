<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $total_harga = (float)$_POST['total_harga'];
    $kasir = $_SESSION['username'];
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];

    mysqli_begin_transaction($conn);
    try {
        // Insert header
        $insertHeader = "INSERT INTO transaksi_jual (tanggal, total_harga, kasir) VALUES ('$tanggal', $total_harga, '$kasir')";
        mysqli_query($conn, $insertHeader);
        $idTransaksi = mysqli_insert_id($conn);

        for ($i = 0; $i < count($id_barang); $i++) {
            $idBarang = (int)$id_barang[$i];
            $jml = (int)$jumlah[$i];
            $harga = (float)$harga_satuan[$i];
            $subtotal = $jml * $harga;

            // Cek stok cukup
            $cekStok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM barang WHERE idBarang = $idBarang"));
            if ($cekStok['stok'] < $jml) {
                throw new Exception("Stok tidak cukup untuk barang ID $idBarang");
            }

            $insertDetail = "INSERT INTO detail_jual (idTransaksiJual, idBarang, jumlah, harga_satuan, subtotal) 
                             VALUES ($idTransaksi, $idBarang, $jml, $harga, $subtotal)";
            mysqli_query($conn, $insertDetail);

            // Kurangi stok
            mysqli_query($conn, "UPDATE barang SET stok = stok - $jml WHERE idBarang = $idBarang");
        }

        mysqli_commit($conn);
        header("Location: cetak_nota.php?id=$idTransaksi");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: penjualan.php?error=1");
        exit;
    }
}
?>