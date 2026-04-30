<?php
session_start();
include 'koneksi.php';

// cookie
if (isset($_COOKIE['id']) && isset($_COOKIE['username'])) {
    $id = $_COOKIE['id'];
    $username = $_COOKIE['username'];

    $result = mysqli_query($conn, "SELECT username FROM users WHERE idUser = '$id'");
    $row = mysqli_fetch_assoc($result);

    if ($username === hash('sha256', $row['username'])) {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $row['username'];
    }
}

if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row["password"]) && $role == $row["role"]) {
            $_SESSION["login"] = true;
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];
            $_SESSION["nama"] = $row["nama"];

            if (isset($_POST["remember"])) {
                setcookie('id', $row['idUser'], time() + 60);
                setcookie('username', hash('sha256', $row['username']), time() + 60);
            }

            if ($row["role"] == "Owner") {
                header("Location: Owner/dashboard_admin.php");
            } else {
                header("Location: Kasir/dashboard_kasir.php");
            }
            exit;
        } else {
            $error = "Username/password/role salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}

// Ambil notifikasi flash dari session (untuk reset password sukses)
$flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>LOGIN</title>
</head>
<body>
<div class="card">
    <div class="left">
        <h2>SISTEM INFORMASI<br>TOKO BAHAN LOGAM</h2>
    </div>

    <div class="right">
        <form action="" method="POST">
            <div class="icon">⚙️</div>
            <h2>Masuk ke SIMTBL</h2>
            <p class="subtitle">Masuk ke akun Anda untuk melanjutkan</p>

            <?php if (isset($error)): ?>
                <div style="background: #ff4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan Username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan Password" required>

            <label>Role</label>
            <select name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="Owner">Owner</option>
                <option value="Kasir">Kasir</option>
            </select>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                <div style="display: flex; align-items: center;">
                    <input type="checkbox" name="remember" id="remember" style="width: auto; margin-right: 5px;">
                    <label for="remember" style="margin: 0; color: white;">Remember Me</label>
                </div>
                <div style="font-size: 12px;">
                    <a href="lupa_password.php" style="color: #F0C38E; text-decoration: none;">Lupa password?</a>
                </div>
            </div>

            <button type="submit" name="login">Masuk</button>
            <p class="footer">© 2026 Toko Logam. All rights reserved.</p>
        </form>
    </div>
</div>

<?php if ($flash): ?>
<div id="flashNotif" class="flash-notification <?= $flash['type'] ?>">
    <?= htmlspecialchars($flash['message']) ?>
</div>
<script>
    // Notifikasi akan hilang setelah 2 detik
    setTimeout(function() {
        var notif = document.getElementById('flashNotif');
        if (notif) {
            notif.style.opacity = '0';
            setTimeout(function() {
                notif.remove();
            }, 300);
        }
    }, 2000);
</script>
<?php endif; ?>

</body>
</html>