<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Owner') {
    header("Location: ../index.php");
    exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$query = "SELECT idUser, username, nama, role FROM users";
if (!empty($search)) {
    $query .= " WHERE username LIKE '%$search%' OR nama LIKE '%$search%'";
}
$query .= " ORDER BY idUser ASC";
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Toko Bahan Logam</title>
    <link rel="stylesheet" href="dashboard_admin.css">
    <style>
        .table-container { 
            background: white; 
            padding: 20px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            overflow-x: auto; /* supaya bisa scroll kalau layar kecil */
        }
        .btn { 
            padding: 6px 12px; 
            border-radius: 6px; 
            text-decoration: none; 
            display: inline-block; 
            font-size: 13px; 
        }
        .btn-add { background: #48426D; color: white; }
        .btn-edit { background: #F0C38E; color: #312051; }
        .btn-delete { background: #ff6b6b; color: white; }
        .btn-search { background: #312051; color: white; padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; }
        
        .search-box { 
            display: flex; 
            gap: 10px; 
            align-items: center; 
        }
        .search-box input { 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            width: 250px; 
        }
        
        /* ===== PERBAIKAN TABEL AGAR RAPI ===== */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
            font-family: 'Quicksand', sans-serif;
        }
        th { 
            background: #312051; 
            color: white; 
            padding: 12px 10px; 
            font-weight: 600;
            font-size: 14px;
            text-align: left;
        }
        td { 
            padding: 12px 10px; 
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
            vertical-align: middle;
        }
        /* Lebar kolom agar proporsional */
        th:nth-child(1), td:nth-child(1) { width: 8%; text-align: center; } /* ID */
        th:nth-child(2), td:nth-child(2) { width: 20%; } /* Username */
        th:nth-child(3), td:nth-child(3) { width: 30%; } /* Nama */
        th:nth-child(4), td:nth-child(4) { width: 15%; } /* Role */
        th:nth-child(5), td:nth-child(5) { width: 27%; } /* Aksi */
        
        /* Rata tengah untuk kolom Aksi */
        td:last-child {
            text-align: left;
        }
        /* Biar tombol aksi tidak turun ke bawah */
        .btn-edit, .btn-delete {
            display: inline-block;
            margin-right: 8px;
            margin-bottom: 0;
            white-space: nowrap;
        }
        /* Hover baris */
        tbody tr:hover {
            background-color: #f9f5f0;
        }
        
        .alert { 
            padding: 10px; 
            border-radius: 6px; 
            margin-bottom: 20px; 
        }
        .alert-success { 
            background: #d4edda; 
            color: #155724; 
        }
    </style>
</head>
<body>
<div class="dashboard">
    <div class="sidebar">
        <h2>Toko Bahan Logam</h2>
        <p class="role">Owner</p>
        <ul>
            <li><a href="dashboard_admin.php">Dashboard</a></li>
            <li><a href="./barang.php">Data Barang</a></li>
            <li><a href="pembelian.php">Transaksi Pembelian</a></li>
            <li><a href="laporan.php">Laporan</a></li>
            <li class="active"><a href="user.php">Kelola User</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="topbar">
            <h1>Kelola User</h1>
        </div>

        <div class="table-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <a href="tambah_user.php" class="btn btn-add">➕ Tambah User</a>
                <form method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Cari username atau nama" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn-search">Cari</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['idUser'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= $row['role'] ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $row['idUser'] ?>" class="btn btn-edit">✏️ Edit</a>
                                <a href="hapus_user.php?id=<?= $row['idUser'] ?>" class="btn btn-delete" onclick="return confirm('Yakin hapus user ini?')">🗑️ Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">Tidak ada data user</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../notifikasi.js"></script>

</body>
</html>