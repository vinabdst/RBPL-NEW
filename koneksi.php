<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $db = "logamstore_db";

    $conn = mysqli_connect($host, $user, $password, $db); //buat konek ke db

    if ($conn->connect_error) {
        die('Maaf, koneksi gagal: ' . $conn->connect_error);
    }
?>