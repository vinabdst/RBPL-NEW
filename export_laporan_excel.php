<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') exit;

$periode = $_GET['periode'];
$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];
$tanggal_mulai = $_GET['tanggal_mulai'];
$tanggal_selesai = $_GET['tanggal_selesai'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_".date('Ymd').".xls");

echo "<table border='1'>";
echo "<tr><th colspan='3'>LAPORAN TOKO BAHAN LOGAM</th></tr>";
echo "<tr><th>Tanggal</th><th>Penjualan</th><th>Pembelian</th></tr>";

$dates = [];
if ($periode == 'harian') {
    $q = mysqli_query($conn, "SELECT tanggal, total_harga FROM transaksi_jual WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'");
    while($r = mysqli_fetch_assoc($q)) $dates[$r['tanggal']]['jual'] = $r['total_harga'];
    $q2 = mysqli_query($conn, "SELECT tanggal, total_harga FROM transaksi_beli WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'");
    while($r = mysqli_fetch_assoc($q2)) $dates[$r['tanggal']]['beli'] = $r['total_harga'];
    ksort($dates);
    foreach($dates as $tgl => $data) {
        echo "<tr><td>$tgl</td><td>".($data['jual']??0)."</td><td>".($data['beli']??0)."</td></tr>";
    }
} else if ($periode == 'bulanan') {
    $q = mysqli_query($conn, "SELECT tanggal, total_harga FROM transaksi_jual WHERE MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun");
    while($r = mysqli_fetch_assoc($q)) $dates[$r['tanggal']]['jual'] = $r['total_harga'];
    $q2 = mysqli_query($conn, "SELECT tanggal, total_harga FROM transaksi_beli WHERE MONTH(tanggal)=$bulan AND YEAR(tanggal)=$tahun");
    while($r = mysqli_fetch_assoc($q2)) $dates[$r['tanggal']]['beli'] = $r['total_harga'];
    ksort($dates);
    foreach($dates as $tgl => $data) {
        echo "<tr><td>$tgl</td><td>".($data['jual']??0)."</td><td>".($data['beli']??0)."</td></tr>";
    }
} else {
    $q = mysqli_query($conn, "SELECT MONTH(tanggal) as bulan, SUM(total_harga) as total FROM transaksi_jual WHERE YEAR(tanggal)=$tahun GROUP BY MONTH(tanggal)");
    while($r = mysqli_fetch_assoc($q)) $dates[$r['bulan']]['jual'] = $r['total'];
    $q2 = mysqli_query($conn, "SELECT MONTH(tanggal) as bulan, SUM(total_harga) as total FROM transaksi_beli WHERE YEAR(tanggal)=$tahun GROUP BY MONTH(tanggal)");
    while($r = mysqli_fetch_assoc($q2)) $dates[$r['bulan']]['beli'] = $r['total'];
    for($i=1; $i<=12; $i++) {
        $jual = $dates[$i]['jual'] ?? 0;
        $beli = $dates[$i]['beli'] ?? 0;
        echo "<tr><td>Bulan $i</td><td>$jual</td><td>$beli</td></tr>";
    }
}
echo "</table>";
?>