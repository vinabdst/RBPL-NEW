<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
    $total_harga = (float)$_POST['total_harga'];
    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];

    // Mulai transaksi database
    mysqli_begin_transaction($conn);
    try {
        // Insert header transaksi beli
        $insertHeader = "INSERT INTO transaksi_beli (tanggal, supplier, total_harga) VALUES ('$tanggal', '$supplier', $total_harga)";
        mysqli_query($conn, $insertHeader);
        $idTransaksi = mysqli_insert_id($conn);

        // Insert detail dan update stok
        for ($i = 0; $i < count($id_barang); $i++) {
            $idBarang = (int)$id_barang[$i];
            $jml = (int)$jumlah[$i];
            $harga = (float)$harga_satuan[$i];
            $subtotal = $jml * $harga;

            $insertDetail = "INSERT INTO detail_beli (idTransaksiBeli, idBarang, jumlah, harga_satuan, subtotal) 
                             VALUES ($idTransaksi, $idBarang, $jml, $harga, $subtotal)";
            mysqli_query($conn, $insertDetail);

            // Update stok barang (tambah stok)
            $updateStok = "UPDATE barang SET stok = stok + $jml WHERE idBarang = $idBarang";
            mysqli_query($conn, $updateStok);
        }

        mysqli_commit($conn);
        header("Location: riwayat_pembelian.php?status=added");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: pembelian.php?status=error");
        exit;
    }
}
?>