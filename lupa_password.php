<?php
session_start();
include 'koneksi.php';

$step = isset($_GET['step']) ? $_GET['step'] : 'form';

if ($step == 'reset' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    // Cek apakah username dan nama cocok
    $query = "SELECT idUser FROM users WHERE username='$username' AND nama='$nama'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if ($password_baru === $konfirmasi) {
            $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed' WHERE idUser=" . $user['idUser'];
            mysqli_query($conn, $update);
            header("Location: index.php?status=reset_success");
            exit;
        } else {
            $error = "Password baru dan konfirmasi tidak cocok.";
        }
    } else {
        $error = "Username dan Nama Lengkap tidak cocok.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Toko Bahan Logam</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #312051, #48426D);
            font-family: 'Quicksand', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .reset-container {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .reset-container h2 {
            text-align: center;
            color: #312051;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #312051;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #48426D;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            color: #312051;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="reset-container">
    <h2>Reset Password</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="lupa_password.php?step=reset">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Masukkan username Anda">
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required placeholder="Sesuai dengan data akun">
        </div>
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password_baru" required>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi" required>
        </div>
        <button type="submit">Reset Password</button>
    </form>
    <div class="back-link">
        <a href="index.php">← Kembali ke Login</a>
    </div>
</div>
</body>
</html>