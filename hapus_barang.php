<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM barang WHERE idBarang = $id");
header("Location: barang.php?status=deleted");
exit;